@extends('layouts.app')

@section('content')

    <h1 class="text-xl font-semibold mb-4">Jurnal Umum</h1>

    <table class="w-full text-sm border">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-2 border">Tanggal</th>
                <th class="p-2 border">No. Jurnal</th>
                <th class="p-2 border">Invoice</th>
                <th class="p-2 border">Keterangan</th>
                <th class="p-2 border text-right">Debit</th>
                <th class="p-2 border text-right">Kredit</th>
                <th class="p-2 border"></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($journals as $journal)
                <tr>
                    <td class="p-2 border">{{ $journal->journal_date }}</td>
                    <td class="p-2 border">{{ $journal->journal_no }}</td>
                    <td class="p-2 border">
                        {{ $journal->sale?->invoice_number ?? '-' }}
                    </td>
                    <td class="p-2 border">{{ $journal->description }}</td>
                    <td class="p-2 border text-right">
                        {{ $journal->type === 'debit' ? number_format($journal->amount, 2) : '' }}
                    </td>
                    <td class="p-2 border text-right">
                        {{ $journal->type === 'credit' ? number_format($journal->amount, 2) : '' }}
                    </td>
                    <td class="p-2 border text-center">
                        <a href="{{ route('accounting.journals.show', $journal->journal_no) }}"
                           class="text-blue-600 underline">
                            Lihat
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">
        {{ $journals->links() }}
    </div>
@endsection

