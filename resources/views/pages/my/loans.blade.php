@extends('layouts.app')

@section('content')
<h1 class="text-xl font-semibold mb-4">Riwayat Peminjaman Saya</h1>

@forelse ($loans as $loan)
    <div class="border p-4 rounded mb-2">
        <div>{{ $loan->item->name }}</div>
        <div>Status: {{ $loan->status }}</div>
    </div>
@empty
    <p>Belum ada peminjaman</p>
@endforelse

<div class="mt-4">
    {{ $loans->links() }}
</div>
@endsection
