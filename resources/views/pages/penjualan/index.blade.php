@extends('layouts.app')

@section('title', 'Daftar Penjualan')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex justify-between items-center">
        <h1 class="text-xl font-semibold">Daftar Penjualan</h1>

        <a href="{{ route('penjualan.create') }}" class="btn btn-primary">
            + Pendaftaran Penjualan
        </a>
    </div>

    {{-- Table --}}
    <div class="card">
        <div class="card-body overflow-x-auto">
            <table class="table w-full">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Invoice</th>
                        <th>Tanggal</th>
                        <th>Customer</th>
                        <th>Kasir</th>
                        <th class="text-right">Total</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sales as $index => $sale)
                        <tr>
                            <td>
                                {{ $sales->firstItem() + $index }}
                            </td>
                            <td class="font-medium">
                                {{ $sale->invoice_number }}
                            </td>
                            <td>
                                {{ $sale->sale_date->format('d/m/Y H:i') }}
                            </td>
                            <td>
                                {{ $sale->customer->name }}
                            </td>
                            <td>
                                {{ $sale->user->name }}
                            </td>
                            <td class="text-right">
                                {{ number_format($sale->total) }}
                            </td>
                            <td>
                                <a
                                    href="{{ route('sales.show', $sale->invoice_number) }}"
                                    class="btn btn-sm btn-secondary"
                                >
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-gray-500">
                                Belum ada data penjualan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div>
        {{ $sales->links() }}
    </div>

</div>
@endsection
