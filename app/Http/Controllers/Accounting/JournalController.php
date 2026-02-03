<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Journal;
use Illuminate\Http\Request;

class JournalController extends Controller
{
    public function index(Request $request)
    {
        $journals = Journal::with(['coa.jenis', 'sale'])
            ->when($request->filled('date'), function ($q) use ($request) {
                $q->whereDate('journal_date', $request->date);
            })
            ->orderBy('journal_date', 'desc')
            ->orderBy('journal_no')
            ->paginate(20);

        return view('pages.accounting.journals.index', compact('journals'));
    }

    public function show(string $journal_no)
    {
        $entries = Journal::with(['coa.jenis', 'sale'])
            ->where('journal_no', $journal_no)
            ->orderBy('type', 'desc') // debit dulu
            ->get();

        abort_if($entries->isEmpty(), 404);

        return view('pages.accounting.journals.show', [
            'journalNo' => $journal_no,
            'entries'   => $entries,
        ]);
    }
}
