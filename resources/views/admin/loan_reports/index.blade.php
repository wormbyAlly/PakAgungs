@extends('layouts.app')

@section('content')

<x-common.component-card title="Laporan Peminjaman">

    <div class="rounded-2xl border border-gray-200 bg-white pt-4 dark:border-gray-800 dark:bg-white/[0.03]">

        {{-- HEADER --}}
        <div class="flex flex-col gap-3 px-5 mb-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">

            <div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                    List Laporan Peminjaman
                </h3>
            </div>

            {{-- FILTER AREA --}}
            <form method="GET" class="flex flex-col gap-3 sm:flex-row sm:items-center">

                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-400">Dari</label>
                    <input type="date" name="from"
                        value="{{ request('from') }}"
                        class="h-10 rounded-lg border border-gray-300 bg-transparent px-3 text-sm
                               dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                </div>

                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-400">Sampai</label>
                    <input type="date" name="to"
                        value="{{ request('to') }}"
                        class="h-10 rounded-lg border border-gray-300 bg-transparent px-3 text-sm
                               dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                </div>

                <div class="flex gap-2 pt-4 sm:pt-0">
                    <button
                        class="rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary/90">
                        Filter
                    </button>

                    <a href="{{ route('admin.loan-reports.pdf', request()->all()) }}"
                        class="rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700">
                        Export PDF
                    </a>
                </div>

            </form>
        </div>

        {{-- TABLE --}}
        <div class="overflow-hidden">
            <div class="max-w-full px-5 overflow-x-auto">
                <table class="min-w-full">

                    <thead>
                        <tr class="border-y border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-3 text-start text-theme-sm text-gray-500">Barang</th>
                            <th class="px-4 py-3 text-start text-theme-sm text-gray-500">Qty</th>
                            <th class="px-4 py-3 text-start text-theme-sm text-gray-500">Lokasi</th>
                            <th class="px-4 py-3 text-start text-theme-sm text-gray-500">Guru</th>
                            <th class="px-4 py-3 text-start text-theme-sm text-gray-500">Tgl Pinjam</th>
                            <th class="px-4 py-3 text-start text-theme-sm text-gray-500">Tgl Kembali</th>
                            <th class="px-4 py-3 text-start text-theme-sm text-gray-500">Kondisi</th>
                            <th class="px-4 py-3 text-start text-theme-sm text-gray-500">User Pinjam</th>
                            <th class="px-4 py-3 text-start text-theme-sm text-gray-500">User Kembali</th>
                            <th class="px-4 py-3 text-center text-theme-sm text-gray-500">Status</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">

                        @forelse($loans as $loan)
                        <tr>

                            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                {{ $loan->item->name }}
                            </td>

                            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                {{ $loan->quantity }}
                            </td>

                            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                {{ $loan->location }}
                            </td>

                            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                {{ $loan->teacher->name }}
                            </td>

                            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                {{ $loan->loan_date }}
                            </td>

                            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                {{ optional($loan->return)->returned_at ?? '-' }}
                            </td>

                            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                {{ optional($loan->return)->condition ?? '-' }}
                            </td>

                            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                {{ $loan->user->name }}
                            </td>

                            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                {{ optional(optional($loan->return)->returnedBy)->name ?? '-' }}
                            </td>

                            {{-- STATUS BADGE --}}
                            <td class="px-4 py-3 text-center">

                                @if($loan->status === 'borrowed')
                                    <span class="px-2 py-1 text-xs font-semibold text-yellow-700 bg-yellow-100 rounded">
                                        Borrowed
                                    </span>
                                @elseif($loan->status === 'returned')
                                    <span class="px-2 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded">
                                        Returned
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold text-red-700 bg-red-100 rounded">
                                        Canceled
                                    </span>
                                @endif

                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="px-4 py-6 text-center text-sm text-gray-500">
                                Tidak ada data laporan
                            </td>
                        </tr>
                        @endforelse

                    </tbody>

                </table>
            </div>
        </div>

    </div>

</x-common.component-card>

@endsection
