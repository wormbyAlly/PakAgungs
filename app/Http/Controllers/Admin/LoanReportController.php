<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class LoanReportController extends Controller
{
public function index(Request $request)
{
    $query = Loan::query()
        ->leftJoin('loan_returns', 'loan_returns.loan_id', '=', 'loans.id')
        ->with([
            'item',
            'teacher',
            'user',
            'return.returnedBy'
        ])
        ->select('loans.*')
        ->orderByRaw('loan_returns.returned_at IS NULL') // yang sudah return dulu
        ->orderByDesc('loan_returns.returned_at')
        ->orderByDesc('loans.loan_date');

    if ($request->filled('from')) {
        $query->whereDate('loans.loan_date', '>=', $request->from);
    }

    if ($request->filled('to')) {
        $query->whereDate('loans.loan_date', '<=', $request->to);
    }

    $loans = $query->get();

    return view('admin.loan_reports.index', compact('loans'));
}


    public function pdf(Request $request)
    {
        $query = Loan::with([
            'item',
            'teacher',
            'user',
            'return.returnedBy'
        ])->orderBy('loan_date', 'desc');

        if ($request->filled('from')) {
            $query->whereDate('loan_date', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('loan_date', '<=', $request->to);
        }

        $loans = $query->get();

        $pdf = Pdf::loadView('admin.loan_reports.pdf', compact('loans'));

        return $pdf->download('laporan-peminjaman.pdf');
    }
}
