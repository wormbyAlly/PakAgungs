@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <h1 class="text-2xl font-semibold">Riwayat Peminjaman Saya</h1>

    <div class="rounded-xl border overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-3 text-left">Item</th>
                    <th class="px-4 py-3">Qty</th>
                    <th class="px-4 py-3">Guru</th>
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">Status</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($loans as $loan)
                    <tr class="border-t">
                        <td class="px-4 py-3">{{ $loan->item->name }}</td>
                        <td class="px-4 py-3 text-center">{{ $loan->quantity }}</td>
                        <td class="px-4 py-3">{{ $loan->teacher->name }}</td>
                        <td class="px-4 py-3">
                            {{ \Carbon\Carbon::parse($loan->loan_date)->format('d M Y') }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded text-xs
                                {{ $loan->status === 'returned'
                                    ? 'bg-green-100 text-green-700'
                                    : 'bg-yellow-100 text-yellow-700' }}">
                                {{ ucfirst($loan->status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                            Belum ada riwayat peminjaman
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $loans->links() }}

</div>
@endsection
