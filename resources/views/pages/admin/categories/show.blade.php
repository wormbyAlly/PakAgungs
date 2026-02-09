@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
            {{ $product->name }}
        </h1>
        <p class="text-sm text-gray-500">
            Kode Produk: <span class="font-medium">{{ $product->code }}</span>
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">

        <!-- TOTAL -->
        <div class="rounded-xl border p-4 bg-white dark:bg-gray-800 text-center">
            <p class="text-sm text-gray-500">Total Stok</p>
            <p class="text-2xl font-bold">{{ $totalStock }}</p>
        </div>

        <!-- AVAILABLE -->
        <div class="rounded-xl border p-4 bg-green-50 dark:bg-green-900/20 text-center">
            <p class="text-sm text-green-600">Stok Layak Jual</p>
            <p class="text-2xl font-bold text-green-700">
                {{ $availableStock }}
            </p>
        </div>

        <!-- EXPIRED -->
        <div class="rounded-xl border p-4 bg-red-50 dark:bg-red-900/20 text-center">
            <p class="text-sm text-red-600 text-center">Stok Expired</p>
            <p class="text-2xl font-bold text-red-700">
                {{ $expiredStock }}
            </p>
        </div>

        <!-- Mendekati Expired -->
        <div class="rounded-xl border p-4 bg-yellow-50 dark:bg-yellow-900/20">
            <p class="text-sm text-yellow-600 text-center">Expired Terdekat</p>

            <p class="text-lg font-bold text-yellow-700 text-center">
                @if ($nearestExpiredDate)
                    {{ \Carbon\Carbon::parse($nearestExpiredDate)->format('d M Y') }}
                @else
                    -
                @endif
            </p>
        </div>

    </div>
    <div class="rounded-xl border overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-100 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-2 text-left">LOT</th>
                    <th class="px-4 py-2">Expired</th>
                    <th class="px-4 py-2">Qty</th>
                    <th class="px-4 py-2">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($stocks as $stock)
                    <tr class="border-t">
                        <td class="px-4 py-2 text-gray-400">{{ $stock->lot_number }}</td>
                        <td class="px-4 py-2 text-gray-400 text-center">
                            {{ $stock->expired->format('d M Y') }}
                        </td>
                        <td class="px-4 py-2 text-center text-gray-400">{{ $stock->qty }}</td>
                        <td class="px-4 py-2 text-center  text-gray-400">
                            @if ($stock->expired->isPast())
                                <span class="text-red-600 font-semibold">Expired</span>
                            @elseif ($stock->expired->diffInDays(now()) <= 30)
                                <span class="text-yellow-600 font-semibold">Near Expired</span>
                            @else
                                <span class="text-green-600 font-semibold">Aman</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>

        </table>
        <div class="p-4">
            {{ $stocks->links() }}
        </div>
    </div>
@endsection
