@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <h1 class="text-2xl font-semibold">Riwayat Peminjaman Saya</h1>

    {{-- Flash Message --}}
    @if(session('success'))
        <div class="p-3 rounded bg-green-100 text-green-700 text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="p-3 rounded bg-red-100 text-red-700 text-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="rounded-xl border overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-3 text-left">Item</th>
                    <th class="px-4 py-3 text-center">Qty</th>
                    <th class="px-4 py-3 text-left">Guru</th>
                    <th class="px-4 py-3 text-left">Tanggal</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
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

                        {{-- STATUS --}}
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 rounded text-xs
                                @if($loan->status === 'returned')
                                    bg-green-100 text-green-700
                                @elseif($loan->status === 'canceled')
                                    bg-red-100 text-red-700
                                @else
                                    bg-yellow-100 text-yellow-700
                                @endif">
                                {{ ucfirst($loan->status) }}
                            </span>
                        </td>

                        {{-- AKSI --}}
                        <td class="px-4 py-3 text-center">
                            @if($loan->status === 'borrowed')
                                <form action="{{ route('loans.cancel', $loan->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Yakin ingin membatalkan peminjaman ini?')">
                                    @csrf
                                    @method('PATCH')

                                    <button type="submit"
                                        class="px-3 py-1 text-xs text-white bg-red-600 rounded hover:bg-red-700">
                                        Cancel
                                    </button>
                                </form>
                            @else
                                <span class="text-gray-400 text-xs">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-gray-500">
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
