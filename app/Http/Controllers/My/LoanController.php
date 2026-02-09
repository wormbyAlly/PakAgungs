<?php

namespace App\Http\Controllers\My;

use App\Http\Controllers\Controller;
use App\Models\Loan;

class LoanController extends Controller
{
    public function index()
    {
        $loans = Loan::with(['item', 'teacher'])
            ->where('user_id', auth()->id())
            ->latest()
->paginate(10);
        return view('my.loans.index', compact('loans'));
    }
}
