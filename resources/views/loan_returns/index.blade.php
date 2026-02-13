@extends('layouts.app')

@section('content')

<x-common.component-card title="Pengembalian Barang">

<div class="space-y-6">

    {{-- SEARCH --}}
    <form method="GET" class="flex justify-between items-center">
        <div class="w-full max-w-md">
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari barang atau guru..."
                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm
                       focus:border-green-300 focus:ring-2 focus:ring-green-500/10
                       dark:border-gray-700 dark:bg-gray-900 dark:text-white">
        </div>
    </form>

    {{-- LIST --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">

        @forelse ($loans as $loan)
        <div x-data="{ condition: 'baik' }"
            class="rounded-2xl border bg-white p-5 shadow-sm
                   hover:ring-2 hover:ring-green-500/30 transition
                   dark:bg-white/[0.03]">

            <div class="flex gap-5">

                {{-- IMAGE --}}
                <div class="flex-shrink-0">
                    @if($loan->item->image)
                        <img src="{{ asset('storage/'.$loan->item->image) }}"
                             class="w-24 h-24 object-cover rounded-xl border">
                    @else
                        <div class="w-24 h-24 flex items-center justify-center
                                    bg-gray-100 rounded-xl text-gray-400 text-xs border">
                            No Image
                        </div>
                    @endif
                </div>

                {{-- CONTENT --}}
                <div class="flex-1 flex flex-col justify-between">

                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                            {{ $loan->item->name }}
                        </h3>

                        <p class="text-sm text-gray-500 mt-1">
                            Guru: {{ $loan->teacher->name }}
                        </p>

                        <p class="text-xs text-gray-400 mt-1">
                            Dipinjam:
                            {{ \Carbon\Carbon::parse($loan->loan_date)->format('d M Y H:i') }}
                        </p>
                    </div>

                    <div class="mt-3 text-sm text-gray-600 space-y-1">
                        <p>Jumlah: <span class="font-semibold text-gray-800">{{ $loan->quantity }}</span></p>
                        @if($loan->location)
                            <p>Lokasi: {{ $loan->location }}</p>
                        @endif
                    </div>

                </div>
            </div>

            {{-- FORM --}}
            <form
                method="POST"
                action="{{ route('loan-returns.store', $loan->id) }}"
                class="mt-5 space-y-3 border-t pt-4">

                @csrf

                <select
                    name="condition"
                    x-model="condition"
                    class="w-full rounded-lg border px-4 py-2 text-sm
                           focus:ring-2 focus:ring-green-500/20
                           dark:bg-gray-800 dark:border-gray-700">
                    <option value="baik">Kondisi Baik</option>
                    <option value="rusak">Rusak</option>
                </select>

                <template x-if="condition === 'rusak'">
                    <textarea
                        name="condition_note"
                        required
                        placeholder="Jelaskan kerusakan..."
                        class="w-full rounded-lg border px-4 py-2 text-sm
                               focus:ring-2 focus:ring-red-500/20
                               dark:bg-gray-800 dark:border-gray-700"></textarea>
                </template>

                <button
                    type="submit"
                    class="w-full px-4 py-2 text-sm text-white rounded-lg shadow-sm
                           bg-green-600 hover:bg-green-700 transition">
                    Konfirmasi Pengembalian
                </button>

            </form>

        </div>

        @empty
            <div class="col-span-full text-center text-gray-500 py-10">
                Tidak ada barang yang perlu dikembalikan
            </div>
        @endforelse

    </div>

    {{-- PAGINATION --}}
    <div>
        {{ $loans->withQueryString()->links() }}
    </div>

</div>

</x-common.component-card>

@endsection
