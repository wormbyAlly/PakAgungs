@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    {{-- HEADER --}}
    <div class="rounded-xl bg-white p-6 shadow">
        <h2 class="text-xl font-semibold text-gray-800">
            Pengembalian Barang
        </h2>
        <p class="text-sm text-gray-500">
            Pilih transaksi peminjaman yang akan dikembalikan
        </p>
    </div>

    {{-- LIST LOAN --}}
    @forelse ($loans as $loan)
    <div
        x-data="{ condition: 'baik' }"
        class="rounded-xl bg-white p-5 shadow space-y-4">

        {{-- INFO --}}
        <div class="flex justify-between">
            <div>
                <h3 class="font-semibold text-gray-800">
                    {{ $loan->item->name }}
                </h3>
                <p class="text-sm text-gray-500">
                    Guru: {{ $loan->teacher->name }}
                </p>
                <p class="text-sm text-gray-500">
                    Lokasi: {{ $loan->location }}
                </p>
                <p class="text-sm text-gray-500">
                    Jumlah: {{ $loan->quantity }}
                </p>
            </div>

            <span class="text-xs px-3 py-1 rounded-full bg-yellow-100 text-yellow-700 h-fit">
                Dipinjam
            </span>
        </div>

        {{-- FORM RETURN --}}
        <form
            method="POST"
action="{{ route('loan-returns.store', ['loan' => $loan->id]) }}"
            class="space-y-3">
            @csrf

            <input type="hidden" name="loan_id" value="{{ $loan->id }}">

            <select
                name="condition"
                x-model="condition"
                class="w-full rounded-lg border px-4 py-2 text-sm">
                <option value="baik">Kondisi Baik</option>
                <option value="rusak">Rusak</option>
            </select>

            {{-- CATATAN RUSAK --}}
            <template x-if="condition === 'rusak'">
                <textarea
                    name="condition_note"
                    required
                    placeholder="Jelaskan kerusakan..."
                    class="w-full rounded-lg border px-4 py-2 text-sm"></textarea>
            </template>

            <button
                type="submit"
                class="rounded-lg bg-green-600 px-5 py-2 text-sm text-white">
                Konfirmasi Pengembalian
            </button>
        </form>
    </div>
    @empty
    <div class="text-center text-gray-500 py-10">
        Tidak ada barang yang perlu dikembalikan
    </div>
    @endforelse

</div>
@endsection