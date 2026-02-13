<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\LoanReturn;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoanReturnController extends Controller
{
   public function index(Request $request)
{
    $query = Loan::with(['item', 'teacher'])
        ->where('status', 'borrowed');

    if ($request->search) {
        $query->where(function ($q) use ($request) {
            $q->whereHas('item', function ($q2) use ($request) {
                $q2->where('name', 'like', '%' . $request->search . '%');
            })
            ->orWhereHas('teacher', function ($q2) use ($request) {
                $q2->where('name', 'like', '%' . $request->search . '%');
            });
        });
    }

    $loans = $query
        ->latest('loan_date')
        ->paginate(9);

    return view('loan_returns.index', compact('loans'));
}


    /**
     * Simpan pengembalian barang
     */
  public function store(Request $request, $loanId)
{
    $loan = Loan::with('item')->findOrFail($loanId);

    // Pastikan hanya yang status borrowed bisa dikembalikan
    if ($loan->status !== 'borrowed') {
        return back()->with('error', 'Barang sudah dikembalikan.');
    }

    $request->validate([
        'condition' => 'required|in:baik,rusak',
        'condition_note' => 'required_if:condition,rusak'
    ]);

    // Simpan data pengembalian
    LoanReturn::create([
        'loan_id' => $loan->id,
        'returned_by_user_id' => Auth::id(),
        'condition' => $request->condition,
        'condition_note' => $request->condition_note,
        'returned_at' => now(),
    ]);

    // Kembalikan stok barang
    $loan->item->increment('stock', $loan->quantity);

    // Update status loan
    $loan->update([
        'status' => 'returned',
        'return_date' => now(),
    ]);

    return redirect()->route('loan-returns.index')
        ->with('success', 'Barang berhasil dikembalikan.');
}
}
