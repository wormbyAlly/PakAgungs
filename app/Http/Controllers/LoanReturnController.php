<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\LoanReturn;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoanReturnController extends Controller
{
    public function index()
{
    $loans = Loan::with(['item', 'teacher'])
        ->where('status', 'borrowed')
        ->orderBy('loan_date')
        ->get();

    return view('loan_returns.index', compact('loans'));
}

    /**
     * Simpan pengembalian barang
     */
    public function store(Request $request)
    {
        $request->validate([
            'loan_id' => ['required', 'exists:loans,id'],
            'condition' => ['required', 'in:baik,rusak'],
            'condition_note' => ['nullable', 'string'],
        ]);

        // VALIDASI KERAS: rusak wajib ada alasan
        if ($request->condition === 'rusak' && empty($request->condition_note)) {
            return back()
                ->withErrors(['condition_note' => 'Wajib diisi jika kondisi rusak'])
                ->withInput();
        }

        $loan = Loan::with('item')->findOrFail($request->loan_id);

        // CEGAH DOUBLE RETURN
        if ($loan->status === 'returned') {
            abort(409, 'Barang ini sudah dikembalikan');
        }

        DB::transaction(function () use ($loan, $request) {

            // 1️⃣ Simpan audit pengembalian
            LoanReturn::create([
                'loan_id' => $loan->id,
                'returned_by_user_id' => auth()->id(),
                'condition' => $request->condition,
                'condition_note' => $request->condition_note,
                'returned_at' => now(),
            ]);

            // 2️⃣ Update status loan
            $loan->update([
                'status' => 'returned',
                'return_date' => now()->toDateString(),
            ]);

            // 3️⃣ Kembalikan stok item
            Item::where('id', $loan->item_id)
                ->increment('stock', $loan->quantity);
        });

        return redirect()
            ->back()
            ->with('success', 'Barang berhasil dikembalikan');
    }
}
