@extends('layouts.app')

@section('content')

<x-common.component-card title="Riwayat Peminjaman Saya">

    {{-- Flash Message --}}
    @if(session('success'))
        <x-ui.alert variant="success" title="Berhasil" :message="session('success')" :showLink="false" />
    @endif

    @if(session('error'))
        <x-ui.alert variant="error" title="Error" :message="session('error')" :showLink="false" />
    @endif

    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">

        <!-- HEADER SECTION -->
        <div class="flex flex-col gap-4 px-6 py-5 border-b border-gray-200 dark:border-gray-800 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                    List Peminjaman
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Total {{ $loans->total() }} data peminjaman
                </p>
            </div>

            <!-- SEARCH -->
<form method="GET" action="{{ route('loans.my') }}" class="flex gap-2 w-full sm:w-auto">
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Cari barang / guru / status..."
                       class="w-full sm:w-64 rounded-xl border border-gray-300 px-4 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">

                <button type="submit"
                        class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition">
                    Search
                </button>

                @if(request('search'))
                    <a href="{{ route('loans.my') }}"
                       class="rounded-xl bg-gray-200 px-4 py-2 text-sm hover:bg-gray-300 dark:bg-gray-700 dark:text-white">
                        Reset
                    </a>
                @endif
            </form>

        </div>

        <!-- TABLE -->
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">

                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 dark:bg-gray-900 dark:border-gray-700">
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Barang</th>
                        <th class="px-6 py-3 text-center font-medium text-gray-500">Qty</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Guru</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Tanggal</th>
                        <th class="px-6 py-3 text-center font-medium text-gray-500">Status</th>
                        <th class="px-6 py-3 text-center font-medium text-gray-500">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">

                    @forelse ($loans as $loan)

                        <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition duration-200">

                            <td class="px-6 py-4 font-medium text-gray-800 dark:text-gray-200">
                                {{ $loan->item->name }}
                            </td>

                            <td class="px-6 py-4 text-center text-gray-600 dark:text-gray-400">
                                {{ $loan->quantity }}
                            </td>

                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                                {{ $loan->teacher->name }}
                            </td>

                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                                {{ \Carbon\Carbon::parse($loan->loan_date)->format('d M Y H:i') }}
                            </td>

                            <!-- STATUS -->
                            <td class="px-6 py-4 text-center">
                                @if($loan->status === 'returned')
                                    <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                        ● Returned
                                    </span>
                                @elseif($loan->status === 'canceled')
                                    <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                                        ● Canceled
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">
                                        ● Borrowed
                                    </span>
                                @endif
                            </td>

                            <!-- AKSI -->
                            <td class="px-6 py-4 text-center">
                                @if($loan->status === 'borrowed')
                                    <form action="{{ route('loans.cancel', $loan->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Yakin ingin membatalkan peminjaman ini?')">
                                        @csrf
                                        @method('PATCH')

                                        <button type="submit"
                                            class="rounded-lg bg-red-600 px-4 py-1.5 text-xs font-medium text-white hover:bg-red-700 transition">
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
                            <td colspan="6"
                                class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                Tidak ada data peminjaman
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

        <!-- PAGINATION -->
        <div class="px-6 py-4 border-t border-gray-200 dark:border-white/[0.05]">
            {{ $loans->links('partials.pagination-tailadmin') }}
        </div>

    </div>

</x-common.component-card>

@endsection
