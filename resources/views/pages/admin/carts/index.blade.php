@extends('layouts.app')

@section('content')
    <x-common.component-card title="Cart">

        <div x-data="penjualan()" x-init="loadCart();

        $watch('search', () => fetchProducts())" class="space-y-6">


            <x-tables.carts.table>

                {{-- ALERT --}}
                @if (session('success'))
                    <x-ui.alert variant="success" title="Berhasil" :message="session('success')" :showLink="false" />
                @endif

                @if (session('error'))
                    <x-ui.alert variant="error" title="Error" :message="session('error')" :showLink="false" />
                @endif

                {{-- SEARCH --}}
                <x-slot:search>
                    <div class="relative">
                        <button type="button" class="absolute -translate-y-1/2 left-4 top-1/2">
                            <svg class="fill-gray-500 dark:fill-gray-400" width="20" height="20" viewBox="0 0 20 20"
                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M3.04199 9.37381C3.04199 5.87712 5.87735 3.04218 9.37533 3.04218C12.8733 3.04218 15.7087 5.87712 15.7087 9.37381C15.7087 12.8705 12.8733 15.7055 9.37533 15.7055C5.87735 15.7055 3.04199 12.8705 3.04199 9.37381ZM9.37533 1.54218C5.04926 1.54218 1.54199 5.04835 1.54199 9.37381C1.54199 13.6993 5.04926 17.2055 9.37533 17.2055C11.2676 17.2055 13.0032 16.5346 14.3572 15.4178L17.1773 18.2381C17.4702 18.531 17.945 18.5311 18.2379 18.2382C18.5308 17.9453 18.5309 17.4704 18.238 17.1775L15.4182 14.3575C16.5367 13.0035 17.2087 11.2671 17.2087 9.37381C17.2087 5.04835 13.7014 1.54218 9.37533 1.54218Z"
                                    fill="" />
                            </svg>
                        </button>
                        <input type="text" placeholder="Search..." x-model.debounce.500ms="search"
                            class="h-[42px] w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pl-[42px] pr-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800 xl:w-[300px]" />
                        <div class="absolute z-10 mt-1 w-full bg-white border rounded shadow" x-show="products.length > 0"
                            @click.outside="products = []">
                            <template x-for="product in products" :key="product.id">
                                <div class="px-4 py-2 flex justify-between items-center"
                                    :class="product.available_stock <= 0 ?
                                        'bg-gray-100 text-gray-400 cursor-not-allowed' :
                                        'hover:bg-gray-100 cursor-pointer'"
                                    @click="handleProductClick(product)" <span x-text="product.name"></span>

                                    <span class="text-sm">
                                        <template x-if="product.available_stock > 0">
                                            <span class="text-gray-500">
                                                Rp <span x-text="format(product.price)"></span>
                                            </span>
                                        </template>

                                        <template x-if="product.available_stock <= 0">
                                            <span class="text-red-500 font-medium">
                                                Stok habis
                                            </span>
                                        </template>
                                    </span>
                                </div>
                            </template>
                        </div>


                    </div>
                </x-slot:search>


                {{-- TABLE HEADER --}}
                <x-slot:thead>
                    <tr class="border-gray-200 border-y dark:border-gray-700">
                        <th scope="col"
                            class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
                            Product</th>
                        <th scope="col"
                            class="px-4 py-3 font-normal text-gray-500 text-center text-theme-sm dark:text-gray-400">
                            Harga</th>
                        <th scope="col"
                            class="px-4 py-3 font-normal text-gray-500 text-center text-theme-sm dark:text-gray-400">
                            Qty</th>
                        <th scope="col"
                            class="px-4 py-3 font-normal text-gray-500 text-center text-theme-sm dark:text-gray-400">
                            Subtotal</th>
                        <th scope="col"
                            class="px-4 py-3 font-normal text-gray-500 text-center text-theme-sm dark:text-gray-400">
                            Action</th>
                    </tr>
                </x-slot:thead>

                {{-- TABLE BODY --}}
                <x-slot:tbody>

                    <template x-for="item in cart" :key="item.product_id">
                        <tr>
                            <td class="py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500 dark:text-gray-400" x-text="item.name"></div>
                            </td>
                            <td class="py-4 whitespace-nowrap">
                                <div class="text-sm text-center text-gray-500 dark:text-gray-400"
                                    x-text="format(item.price)"></div>
                            </td>
                            <td class="py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500 dark:text-gray-400 text-center"> <input type="number"
                                        min="1" class="form-input w-20 text-center" :value="item.qty"
                                        @change="updateQty(item, $event.target.value)">
                                </div>
                            </td>
                            <td class="py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500 dark:text-gray-400 text-center"
                                    x-text="format(item.price * item.qty)"></div>
                            </td>
                            <td class="py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500 dark:text-gray-400 text-center">
                                    <button @click="removeItem(item)" class="btn btn-sm btn-danger">
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>

                    <div class="flex justify-end">
                        <div class="text-lg font-semibold  text-gray-300">
                            Total Item:
                            Rp <span x-text="format(subtotal)"></span>
                        </div>
                    </div>

                    {{-- EMPTY --}}
                    <tr x-show="cart.length === 0">
                        <td colspan="5" class="text-center text-gray-500">
                            Belum ada item
                        </td>
                    </tr>
                </x-slot:tbody>

                {{-- PAGINATION --}}
                <x-slot:pagination>
                    @include('partials.pagination-tailadmin')
                </x-slot:pagination>

            </x-tables.carts.table>
            <div class="flex justify-end mt-6 text-gray-400">
                <button class="btn btn-success" :disabled="cart.length === 0" @click="checkout">
                    Checkout
                </button>
            </div>

            {{-- POP UP --}}
            <div x-show="showInvoicePopup" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
                <div class="bg-white dark:bg-gray-900 p-6 rounded-lg w-[360px]">
                    <h3 class="text-lg font-semibold mb-2">
                        Transaksi Berhasil
                    </h3>

                    <div class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                        <div>
                            Invoice:
                            <span class="font-semibold" x-text="invoiceNumber"></span>
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 mt-6">
                        <button class="btn btn-secondary" @click="showInvoicePopup = false">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>


        </div>
    </x-common.component-card>

    {{-- SCRIPT --}}

    <x-script.carts.index />
@endsection
