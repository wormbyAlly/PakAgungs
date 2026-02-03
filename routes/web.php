<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\StockController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\Accounting\JournalController;
use App\Http\Controllers\SaleController;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Controllers\ProductSearchController;
use App\Http\Controllers\Penjualan\SaleRegistrationController;



// routes/web.php
Route::get('/products/search', ProductSearchController::class)
    ->middleware('auth');


// Route::get('/products/search', function (Request $request) {
//     return Product::query()
//         ->when($request->search, function ($q) use ($request) {
//             $q->where('name', 'like', '%' . $request->search . '%');
//         })
//         ->limit(10)
//         ->get(['id', 'name', 'price']);
// })->name('products.search');

Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | PENJUALAN 
    |--------------------------------------------------------------------------
    */


    Route::middleware('auth')->prefix('penjualan')->name('penjualan.')->group(function () {

        Route::get('/pendaftaran', [SaleRegistrationController::class, 'index'])
            ->name('pendaftaran');

        Route::post('/pendaftaran/search', [SaleRegistrationController::class, 'search'])
            ->name('pendaftaran.search');

        Route::post('/pendaftaran/{sale}/finalize', [SaleRegistrationController::class, 'finalize'])
            ->name('pendaftaran.finalize');
    });


    /*
    |--------------------------------------------------------------------------
    | CART (SESSION ONLY)
    |--------------------------------------------------------------------------
    */

    // Route::post('/cart/add', [CartController::class, 'add'])
    //     ->name('cart.add');

    // Route::post('/cart/update', [CartController::class, 'update'])
    //     ->name('cart.update');

    // Route::get('/cart/current', function () {
    //     return response()->json(
    //         session('cart', ['items' => []])
    //     );
    // })->name('cart.current');


    /*
    |--------------------------------------------------------------------------
    | CHECKOUT (FINAL TRANSACTION)
    |--------------------------------------------------------------------------
    */

    Route::post('/penjualan/simpan', [CheckoutController::class, 'store'])
        ->name('penjualan.simpan');
});
/*
|--------------------------------------------------------------------------
| STOCK MODULE (Admin User)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])
    ->prefix('accounting')
    ->name('accounting.')
    ->group(function () {

        Route::get('/journals', [JournalController::class, 'index'])
            ->name('journals.index');

        Route::get('/journals/{journal_no}', [JournalController::class, 'show'])
            ->name('journals.show');
    });


/*
|--------------------------------------------------------------------------
| Cart
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    Route::get('/cart', [CartController::class, 'index'])
        ->name('cart.index');

    Route::post('/cart/add', [CartController::class, 'add'])
        ->name('cart.add');

    Route::post('/cart/update', [CartController::class, 'update'])
        ->name('cart.update');

    Route::post('/cart/remove', [CartController::class, 'remove'])
        ->name('cart.remove');

    Route::post('/cart/clear', [CartController::class, 'clear'])
        ->name('cart.clear');

    // Route::post('/cart/discount', [CartController::class, 'applyDiscount'])
    //     ->name('cart.discount');

    // Route::post('/cart/header', [CartController::class, 'saveHeader'])
    //     ->name('cart.header');

    Route::get('/cart/current', [CartController::class, 'current'])
        ->name('cart.current');
});

/*
|--------------------------------------------------------------------------
| Checkout
|--------------------------------------------------------------------------
*/


Route::middleware(['auth'])->group(function () {

    Route::post('/checkout', [CheckoutController::class, 'process'])
        ->name('checkout.process');

});


/*
|--------------------------------------------------------------------------
| Product & Stock Route
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->as('admin.')
    ->group(function () {
        Route::resource('products', ProductController::class);
        Route::resource('stocks', StockController::class);
    Route::resource('customers', CustomerController::class)->except(['show']);

});


/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])
        ->name('pages.auth.login');

    Route::post('/login', [LoginController::class, 'login'])
        ->middleware('throttle:login')
        ->name('auth.login.attempt');
});
Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('auth.logout');

Route::get('/logout', function () {
    abort(404);
});


/*
|--------------------------------------------------------------------------
| User Route
|--------------------------------------------------------------------------
*/

// USER DASHBOARD
Route::middleware(['auth.required', 'role:user,admin'])->group(function () {
    Route::get('/dashboard', function () {
        return view('pages.dashboard.user');
    })->name('dashboard.user');
});


/*
|--------------------------------------------------------------------------
| Admin Route
|--------------------------------------------------------------------------
*/

// ADMIN DASHBOARD
Route::middleware(['auth.required', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', fn() => view('pages.dashboard.admin'))
            ->name('dashboard.admin');

        Route::get('/users', [UserController::class, 'index'])
            ->name('users.index');

        Route::get('/users/create', [UserController::class, 'create'])
            ->name('users.create');

        Route::post('/users', [UserController::class, 'store'])
            ->name('users.store');

        Route::get('/users/{user}/edit', [UserController::class, 'edit'])
            ->name('users.edit');

        Route::put('/users/{user}', [UserController::class, 'update'])
            ->name('users.update');

        Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])
            ->name('admin.users.toggle-status');

        Route::patch('/users/{user}/reset-password', [UserController::class, 'resetPassword'])
            ->name('users.reset-password');
    });
Route::delete('/admin/users/{user}', [UserController::class, 'destroy'])
    ->middleware(['auth', 'role:admin'])
    ->name('admin.users.destroy');

Route::delete('/admin/users/{user}', [UserController::class, 'destroy'])
    ->middleware(['auth', 'role:admin']);


/*
|--------------------------------------------------------------------------
| Bawaan dari TailAdmin
|--------------------------------------------------------------------------
*/

// dashboard pages
Route::middleware(['auth.required'])->get('/', function () {
    return view('pages.dashboard.ecommerce');
})->name('dashboard');


// calender pages
Route::get('/calendar', function () {
    return view('pages.calender', ['title' => 'Calendar']);
})->name('calendar');

// profile pages
Route::get('/profile', function () {
    return view('pages.profile', ['title' => 'Profile']);
})->name('profile');

// form pages
Route::get('/form-elements', function () {
    return view('pages.form.form-elements', ['title' => 'Form Elements']);
})->name('form-elements');

// tables pages
Route::get('/basic-tables', function () {
    return view('pages.tables.basic-tables', ['title' => 'Basic Tables']);
})->name('basic-tables');

// pages

Route::get('/blank', function () {
    return view('pages.blank', ['title' => 'Blank']);
})->name('blank');

// error pages
Route::get('/error-404', function () {
    return view('pages.errors.error-404', ['title' => 'Error 404']);
})->name('error-404');

// chart pages
Route::get('/line-chart', function () {
    return view('pages.chart.line-chart', ['title' => 'Line Chart']);
})->name('line-chart');

Route::get('/bar-chart', function () {
    return view('pages.chart.bar-chart', ['title' => 'Bar Chart']);
})->name('bar-chart');


// authentication pages
// Route::get('/signin', function () {
//     return view('pages.auth.signin', ['title' => 'Sign In']);
// })->name('signin');

// Route::get('/signup', function () {
//     return view('pages.auth.signup', ['title' => 'Sign Up']);
// })->name('signup');

// ui elements pages
Route::get('/alerts', function () {
    return view('pages.ui-elements.alerts', ['title' => 'Alerts']);
})->name('alerts');

Route::get('/avatars', function () {
    return view('pages.ui-elements.avatars', ['title' => 'Avatars']);
})->name('avatars');

Route::get('/badge', function () {
    return view('pages.ui-elements.badges', ['title' => 'Badges']);
})->name('badges');

Route::get('/buttons', function () {
    return view('pages.ui-elements.buttons', ['title' => 'Buttons']);
})->name('buttons');

Route::get('/image', function () {
    return view('pages.ui-elements.images', ['title' => 'Images']);
})->name('images');

Route::get('/videos', function () {
    return view('pages.ui-elements.videos', ['title' => 'Videos']);
})->name('videos');
