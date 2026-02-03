@extends('layouts.app')

@section('content')
<x-common.component-card title="Kelola Customer">

    <div x-data="customerTable()" x-init="init()">

        <x-tables.customers.table>

            {{-- ALERT --}}
            <x-slot:alert>
                {{-- toast via JS --}}
            </x-slot:alert>

            {{-- SEARCH --}}
            <x-slot:search>
                <div class="relative">
                    <button type="button" class="absolute -translate-y-1/2 left-4 top-1/2">
                        <svg class="fill-gray-500" width="20" height="20" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M3.04 9.37C3.04 5.88 5.88 3.04 9.37 3.04c3.5 0 6.34 2.84 6.34 6.33 0 3.5-2.84 6.34-6.34 6.34-3.49 0-6.33-2.84-6.33-6.34ZM9.37 1.54A7.83 7.83 0 1 0 17.2 9.37a7.83 7.83 0 0 0-7.83-7.83Z"/>
                        </svg>
                    </button>

                    <input
                        type="text"
                        placeholder="Cari customer..."
                        x-model.debounce.500ms="search"
                        class="h-[42px] w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pl-[42px] pr-4 text-sm"
                    />
                </div>
            </x-slot:search>

            {{-- TABLE HEAD --}}
            <x-slot:thead>
                <tr class="border-y">
                    <th class="px-4 py-3 text-left">Nama</th>
                    <th class="px-4 py-3 text-left">Email</th>
                    <th class="px-4 py-3 text-left">No Telp</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </x-slot:thead>

            {{-- TABLE BODY --}}
            <x-slot:tbody>
                <template x-for="customer in customers" :key="customer.id">
                    <tr>
                        <td class="px-4 py-3" x-text="customer.name"></td>

                        <td class="px-4 py-3 text-gray-500"
                            x-text="customer.email ?? '-'">
                        </td>

                        <td class="px-4 py-3" x-text="customer.phone"></td>

                        <td class="px-4 py-3 text-right">
                            <x-common.table-dropdown>
                                <x-slot name="button">
                                    <button class="text-gray-500">
                                        <svg width="24" height="24" viewBox="0 0 24 24">
                                            <path fill="currentColor"
                                                d="M5.99 12a1.5 1.5 0 1 1 0-.01Zm6 0a1.5 1.5 0 1 1 0-.01Zm6 0a1.5 1.5 0 1 1 0-.01Z"/>
                                        </svg>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <button
                                        class="block w-full px-3 py-2 text-left text-theme-xs hover:bg-gray-100">
                                        Edit
                                    </button>

                                    <button
                                        class="block w-full px-3 py-2 text-left text-red-600 hover:bg-red-50">
                                        Hapus
                                    </button>
                                </x-slot>
                            </x-common.table-dropdown>
                        </td>
                    </tr>
                </template>
            </x-slot:tbody>

            {{-- PAGINATION --}}
            <x-slot:pagination>
                @include('partials.pagination-tailadmin')
            </x-slot:pagination>

        </x-tables.customers.table>
    </div>

</x-common.component-card>

<x-script.customers.index />
@endsection
