<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use Illuminate\Http\Request;

class MyLoanController extends Controller
{
   public function index()
{
    $loans = Loan::with(['item', 'teacher'])
        ->where('user_id', auth()->id())
        ->orderByDesc('created_at')
        ->paginate(10);

    return view('my.loans.index', compact('loans'));
}


}
