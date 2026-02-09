@extends('layouts.app')

@section('content')
    <x-common.component-card title="Items / Barang">

        <div x-data="itemTable()" x-init="init()">

<x-tables.items.table :categories="$categories">

                {{-- ALERT --}}
                @if (session('success'))
                    <x-ui.alert variant="success" title="Berhasil" :message="session('success')" :showLink="false" />
                @endif

                @if (session('error'))
                    <x-ui.alert variant="error" title="Error" :message="session('error')" :showLink="false" />
                @endif

                {{-- CREATE MODAL --}}
                <x-slot:create_modal>
                    <x-admin.items.create-item-modal :categories="$categories" />
                </x-slot:create_modal>

                {{-- SEARCH --}}
                <x-slot:search>
                    <div class="relative">
                        <button type="button" class="absolute -translate-y-1/2 left-4 top-1/2">
                            <svg class="fill-gray-500 dark:fill-gray-400" width="20" height="20" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M3.04199 9.37381C3.04199 5.87712 5.87735 3.04218 9.37533 3.04218C12.8733 3.04218 15.7087 5.87712 15.7087 9.37381C15.7087 12.8705 12.8733 15.7055 9.37533 15.7055C5.87735 15.7055 3.04199 12.8705 3.04199 9.37381ZM9.37533 1.54218C5.04926 1.54218 1.54199 5.04835 1.54199 9.37381C1.54199 13.6993 5.04926 17.2055 9.37533 17.2055C11.2676 17.2055 13.0032 16.5346 14.3572 15.4178L17.1773 18.2381C17.4702 18.531 17.945 18.5311 18.2379 18.2382C18.5308 17.9453 18.5309 17.4704 18.238 17.1775L15.4182 14.3575C16.5367 13.0035 17.2087 11.2671 17.2087 9.37381C17.2087 5.04835 13.7014 1.54218 9.37533 1.54218Z" />
                            </svg>
                        </button>
                        <input type="text" placeholder="Search..."
                            x-model.debounce.500ms="search"
                            class="h-[42px] w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pl-[42px] pr-4 text-sm text-gray-800 shadow-theme-xs
                            placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10
                            dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30
                            dark:focus:border-blue-800 xl:w-[300px]" />
                    </div>
                </x-slot:search>

                {{-- TABLE HEADER --}}
                <x-slot:thead>
                    <tr class="border-gray-200 border-y dark:border-gray-700">
                        <th class="px-4 py-3 text-start text-theme-sm text-gray-500">Barang</th>
                        <th class="px-4 py-3 text-start text-theme-sm text-gray-500">Category</th>
                        <th class="px-4 py-3 text-end text-theme-sm text-gray-500">Stock</th>
                        <th class="px-4 py-3 text-center text-theme-sm text-gray-500">Status</th>
                        <th class="px-4 py-4 text-theme-sm text-gray-500">
                            <div class="flex justify-end mr-3">Actions</div>
                        </th>
                    </tr>
                </x-slot:thead>

                {{-- TABLE BODY --}}
                <x-slot:tbody>
                    <template x-for="item in items" :key="item.id">
                        <tr>
                            <td class="text-sm text-gray-500 dark:text-gray-400" x-text="item.name"></td>

                            <td class="text-sm text-gray-500 dark:text-gray-400"
                                x-text="item.category ? item.category.name : '-'"></td>

                            <td class="text-sm text-gray-500 dark:text-gray-400 text-end"
                                x-text="item.stock"></td>

                            <td class="text-sm text-center">
                                <span x-show="item.is_active"
                                    class="px-2 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded">
                                    ACTIVE
                                </span>

                                <span x-show="!item.is_active"
                                    class="px-2 py-1 text-xs font-semibold text-gray-600 bg-gray-100 rounded">
                                    INACTIVE
                                </span>
                            </td>

                            {{-- ACTION --}}
                            <td class="w-24 px-4 py-4 text-sm font-medium text-end whitespace-nowrap">
                                <div class="flex justify-center relative">
                                    <x-common.table-dropdown>
                                        <x-slot name="button">
                                            <button type="button" class="text-gray-500 dark:text-gray-400">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M5.99902 10.4951C6.82745 10.4951 7.49902 11.1667 7.49902 11.9951V12.0051C7.49902 12.8335 6.82745 13.5051 5.99902 13.5051C5.1706 13.5051 4.49902 12.8335 4.49902 12.0051V11.9951C4.49902 11.1667 5.1706 10.4951 5.99902 10.4951ZM17.999 10.4951C18.8275 10.4951 19.499 11.1667 19.499 11.9951V12.0051C19.499 12.8335 18.8275 13.5051 17.999 13.5051C17.1706 13.5051 16.499 12.8335 16.499 12.0051V11.9951C16.499 11.1667 17.1706 10.4951 17.999 10.4951ZM13.499 11.9951C13.499 11.1667 12.8275 10.4951 11.999 10.4951C11.1706 10.4951 10.499 11.1667 10.499 11.9951V12.0051C10.499 12.8335 11.1706 13.5051 11.999 13.5051C12.8275 13.5051 13.499 12.8335 13.499 12.0051V11.9951Z"
                                                        fill="currentColor" />
                                                </svg>
                                            </button>
                                        </x-slot>

                                        <x-slot name="content">
                                            <button @click="openEditModal(item)"
                                                class="flex w-full px-3 py-2 text-left text-theme-xs text-gray-500 hover:bg-gray-100">
                                                Edit
                                            </button>

                                            <button @click="deleteItem(item.id)"
                                                class="flex w-full px-3 py-2 text-left text-theme-xs text-red-700 hover:bg-red-50">
                                                Delete
                                            </button>
                                        </x-slot>
                                    </x-common.table-dropdown>
                                </div>
                            </td>
                        </tr>
                    </template>

                    {{-- EMPTY --}}
                    <tr x-show="items.length === 0">
                        <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">
                            No item data found
                        </td>
                    </tr>
                </x-slot:tbody>

                {{-- PAGINATION --}}
                <x-slot:pagination>
                    @include('partials.pagination-tailadmin')
                </x-slot:pagination>

            </x-tables.items.table>

        </div>
    </x-common.component-card>

    {{-- SCRIPT --}}
    <x-script.items.index />

@endsection
