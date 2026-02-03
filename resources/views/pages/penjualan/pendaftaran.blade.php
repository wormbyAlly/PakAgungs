@extends('layouts.app')

@section('title', 'Pendaftaran Penjualan')

@section('content')
<x-common.component-card title="Pendaftaran">
    <div x-data="pendaftaranPenjualan()" class="space-y-6">

        {{-- SEARCH --}}
        <div class="card">
            <div class="card-body space-y-4">
                <h3 class="font-semibold text-lg text-gray-300">Cari Transaksi</h3>

                <input type="text" x-model="keyword" placeholder="Invoice / Nama / Telp / Email" class="form-input w-full text-gray-100">

                <button class="btn btn-primary text-gray-300" @click="search">
                    Cari
                </button>

                <template x-if="error">
                    <x-ui.alert variant="error" title="Error" :showLink="false">
                        <span x-text="error"></span>
                    </x-ui.alert>
                </template>
            </div>
        </div>

        {{-- DETAIL SALE --}}

        <template x-if="sale">
            <div class="card ">
                <div class="card-body space-y-4">

                    <h3 class="font-semibold text-lg">
                        Invoice: <span x-text="sale.invoice_number"></span>
                    </h3>

                    {{-- ITEMS --}}
                    <table class="table w-full">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Qty</th>
                                <th>Harga</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="item in sale.items" :key="item.id">
                                <tr>
                                    <td x-text="item.product.name"></td>
                                    <td x-text="item.qty"></td>
                                    <td x-text="format(item.price)"></td>
                                    <td x-text="format(item.subtotal)"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>

                    <div class="text-right font-semibold">
                        Subtotal:
                        Rp <span x-text="format(subtotal)"></span>
                    </div>

                    {{-- CUSTOMER --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <input type="text" x-model="customer.name" placeholder="Nama Customer" class="form-input">
                        <input type="text" x-model="customer.phone" placeholder="No Telp" class="form-input">
                        <input type="email" x-model="customer.email" placeholder="Email" class="form-input">
                    </div>

                    {{-- DISCOUNT --}}
                    <div>
                        <label>Diskon</label>
                        <input type="number" min="0" x-model.number="discount" class="form-input w-full">
                    </div>

                    {{-- TOTAL --}}
                    <div class="text-right text-lg font-bold">
                        Total:
                        Rp <span x-text="format(grandTotal())"></span>
                    </div>

                    <button class="btn btn-success" @click="finalize">
                        Finalisasi Penjualan
                    </button>

                </div>
            </div>
        </template>

    </div>

    {{-- SCRIPT --}}
    <script>
        function pendaftaranPenjualan() {
            return {
                keyword: '',
                sale: null,
                error: null,
                discount: 0,
                customer: {
                    name: '',
                    phone: '',
                    email: '',
                },

                get subtotal() {
                    return this.sale ?
                        this.sale.items.reduce((s, i) => s + Number(i.subtotal), 0) :
                        0
                },

                grandTotal() {
                    return Math.max(this.subtotal - this.discount, 0)
                },

                search() {
                    this.error = null

                    fetch('{{ route('penjualan.pendaftaran.search') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document
                                    .querySelector('meta[name=csrf-token]').content
                            },
                            body: JSON.stringify({
                                keyword: this.keyword
                            })
                        })
                        .then(r => r.json())
                        .then(data => {
                            if (data.message) throw data

                            this.sale = data
                            this.discount = 0

                            this.customer = data.customer ?? {
                                name: '',
                                phone: '',
                                email: ''
                            }
                        })


                        .catch(err => {
                            this.sale = null
                            this.error = err.message ?? 'Data tidak ditemukan'
                        })
                },

                finalize() {
                    fetch(`/penjualan/pendaftaran/${this.sale.id}/finalize`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                            },
                            body: JSON.stringify({
                                ...this.customer,
                                discount: this.discount
                            })
                        })

                        .then(r => {
                            if (!r.ok) return r.json().then(e => {
                                throw e
                            })
                            return r.json()
                        })
                        .then(res => {
                            alert('Penjualan berhasil difinalisasi')
                            window.location.reload()
                        })
                        .catch(err => {
                            console.error(err)
                            alert(err.message ?? 'Validasi gagal')
                        })

                },

                format(n) {
                    return new Intl.NumberFormat('id-ID').format(n)
                }
            }
        }
    </script>
     </x-common.component-card>
@endsection
