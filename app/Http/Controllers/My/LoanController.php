<?php

namespace App\Http\Controllers\My;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $loans = Loan::with(['item', 'teacher'])
            ->where('user_id', auth()->id())
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('item', function ($item) use ($search) {
                        $item->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('teacher', function ($teacher) use ($search) {
                        $teacher->where('name', 'like', "%{$search}%");
                    })
                    ->orWhere('status', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString(); // <- supaya pagination tetap membawa search

        return view('my.loans.index', compact('loans', 'search'));
    }
}
