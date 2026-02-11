<?php
namespace App\Http\Controllers\Admin;

use App\Models\Loan;
use App\Models\Item;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;



class LoanController extends Controller
{
    
    public function cancel(Loan $loan)
    {
        // 1. Pastikan milik user yang login
        if ($loan->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // 2. Hanya bisa cancel jika masih borrowed
        if ($loan->status !== Loan::STATUS_BORROWED) {
            return back()->with('error', 'Loan tidak bisa dibatalkan.');
        }

       DB::transaction(function () use ($loan) {

    $loan = Loan::where('id', $loan->id)
        ->lockForUpdate()
        ->first();

    if ($loan->status !== Loan::STATUS_BORROWED) {
        throw new \Exception('Loan already processed.');
    }

    $loan->item()->lockForUpdate()->first()
        ->increment('stock', $loan->quantity);

    $loan->update([
        'status' => Loan::STATUS_CANCELED,
    ]);
});


        return back()->with('success', 'Peminjaman berhasil dibatalkan.');
    }

    public function index()
    {
        $loans = Loan::with(['item', 'teacher', 'recordedBy'])
            ->latest()
            ->paginate(10);

        return view('loans.index', compact('loans'));
    }

    public function create()
    {
        return view('loans.create', [
            'items'    => Item::where('stock', '>', 0)->get(),
            'teachers' => Teacher::orderBy('name')->get(),
        ]);
    }

  public function store(Request $request)
{
    try {
        $validated = $request->validate([
            'item_id'    => 'required|exists:items,id',
            'teacher_id' => 'required|exists:teachers,id',
            'quantity'   => 'required|integer|min:1',
            'location'   => 'required|string|max:255',
            'loan_date'  => 'required|date',
        ]);
    } catch (ValidationException $e) {
        return response()->json([
            'message' => 'Validation error',
            'errors'  => $e->errors(),
        ], 422);
    }

    DB::transaction(function () use ($request) {

        $item = Item::lockForUpdate()->findOrFail($request->item_id);

        if ($item->stock < $request->quantity) {
            throw new \Exception('Stok tidak mencukupi');
        }

        $item->decrement('stock', $request->quantity);

        Loan::create([
            'user_id'    => auth()->id(),
            'item_id'    => $request->item_id,
            'teacher_id' => $request->teacher_id,
            'quantity'   => $request->quantity,
            'location'   => $request->location,
            'loan_date'  => $request->loan_date,
            'status'     => 'borrowed',
        ]);
    });

    return response()->json([
        'message' => 'Peminjaman berhasil dicatat'
    ]);
}


   public function markReturned(Loan $loan)
{
    DB::transaction(function () use ($loan) {

        $loan = Loan::lockForUpdate()->findOrFail($loan->id);

        if ($loan->status !== 'borrowed') {
            throw new \Exception('Peminjaman sudah dikembalikan');
        }

        $loan->item()->lockForUpdate()->increment('stock', $loan->quantity);

        $loan->update([
            'status'      => 'returned',
            'return_date' => now(),
        ]);
    });

    return back()->with('success', 'Barang berhasil dikembalikan');
}

}
