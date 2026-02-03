@extends('layouts.app')

@section('content')

    <h1 class="text-xl font-semibold mb-4">
        Detail Jurnal {{ $journalNo }}
    </h1>

    <table class="w-full text-sm border">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-2 border">COA</th>
                <th class="p-2 border">Nama Akun</th>
                <th class="p-2 border">Debit</th>
                <th class="p-2 border">Kredit</th>
            </tr>
        </thead>
        <tbody>
            @php($totalDebit = 0)
            @php($totalCredit = 0)

            @foreach ($entries as $entry)
                <tr>
                    <td class="p-2 border">{{ $entry->coa->code }}</td>
                    <td class="p-2 border">{{ $entry->coa->nama }}</td>
                    <td class="p-2 border text-right">
                        @if ($entry->type === 'debit')
                            @php($totalDebit += $entry->amount)
                            {{ number_format($entry->amount, 2) }}
                        @endif
                    </td>
                    <td class="p-2 border text-right">
                        @if ($entry->type === 'credit')
                            @php($totalCredit += $entry->amount)
                            {{ number_format($entry->amount, 2) }}
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot class="bg-gray-100 font-semibold">
            <tr>
                <td colspan="2" class="p-2 border text-right">TOTAL</td>
                <td class="p-2 border text-right">
                    {{ number_format($totalDebit, 2) }}
                </td>
                <td class="p-2 border text-right">
                    {{ number_format($totalCredit, 2) }}
                </td>
            </tr>
        </tfoot>
    </table>
@endsection
