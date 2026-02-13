<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Peminjaman</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 5px; }
        th { background: #eee; }
    </style>
</head>
<body>

<h2>Laporan Peminjaman</h2>

<table>
    <thead>
        <tr>
            <th>Barang</th>
            <th>Qty</th>
            <th>Lokasi</th>
            <th>Guru</th>
            <th>Tgl Pinjam</th>
            <th>Tgl Kembali</th>
            <th>Kondisi</th>
            <th>User Pinjam</th>
            <th>User Kembali</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($loans as $loan)
        <tr>
            <td>{{ $loan->item->name }}</td>
            <td>{{ $loan->quantity }}</td>
            <td>{{ $loan->location }}</td>
            <td>{{ $loan->teacher->name }}</td>
            <td>{{ $loan->loan_date }}</td>
            <td>{{ optional($loan->return)->returned_at }}</td>
            <td>{{ optional($loan->return)->condition ?? '-' }}</td>
            <td>{{ $loan->user->name }}</td>
            <td>{{ optional(optional($loan->return)->returnedBy)->name ?? '-' }}</td>
            <td>{{ $loan->status }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
