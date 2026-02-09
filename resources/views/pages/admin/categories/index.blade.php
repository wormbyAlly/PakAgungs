@extends('layouts.app')

@section('content')
    <x-common.component-card title="Kelola Kategori">

        <div x-data="categoryTable()" x-init="console.log('INIT', categories)">

            <x-tables.categories.table>

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
                            <svg class="fill-gray-500 dark:fill-gray-400" width="20" height="20"
                                viewBox="0 0 20 20" fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M3.04199 9.37381C3.04199 5.87712 5.87735 3.04218 9.37533 3.04218C12.8733 3.04218 15.7087 5.87712 15.7087 9.37381C15.7087 12.8705 12.8733 15.7055 9.37533 15.7055C5.87735 15.7055 3.04199 12.8705 3.04199 9.37381ZM9.37533 1.54218C5.04926 1.54218 1.54199 5.04835 1.54199 9.37381C1.54199 13.6993 5.04926 17.2055 9.37533 17.2055C11.2676 17.2055 13.0032 16.5346 14.3572 15.4178L17.1773 18.2381C17.4702 18.531 17.945 18.5311 18.2379 18.2382C18.5308 17.9453 18.5309 17.4704 18.238 17.1775L15.4182 14.3575C16.5367 13.0035 17.2087 11.2671 17.2087 9.37381C17.2087 5.04835 13.7014 1.54218 9.37533 1.54218Z"/>
                            </svg>
                        </button>

                        <input
                            type="text"
                            placeholder="Cari kategori..."
                            x-model.debounce.500ms="search"
                            class="h-[42px] w-full rounded-lg border border-gray-300 bg-transparent
                                   py-2.5 pl-[42px] pr-4 text-sm text-gray-800 shadow-theme-xs
                                   placeholder:text-gray-400 focus:border-blue-300 focus:outline-none
                                   focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700
                                   dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30
                                   dark:focus:border-blue-800 xl:w-[300px]" />
                    </div>
                </x-slot:search>

                {{-- TABLE HEAD --}}
                <x-slot:thead>
                    <tr class="border-gray-200 border-y dark:border-gray-700">
                        <th class="px-4 py-3 text-start text-theme-sm text-gray-500">Nama Kategori</th>
                        <th class="px-4 py-3 text-end text-theme-sm text-gray-500">
                            <div class="flex justify-end mr-3">Actions</div>
                        </th>
                    </tr>
                </x-slot:thead>

                {{-- TABLE BODY --}}
                <x-slot:tbody>
                    <template x-for="category in categories" :key="category.id">
                        <tr>

                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-800 dark:text-white"
                                     x-text="category.name"></div>
                            </td>

                            {{-- ACTION --}}
                            <td class="w-24 px-4 py-4 text-sm text-right whitespace-nowrap">
                                <div class="flex justify-center relative">
                                    <x-common.table-dropdown>

                                        <x-slot name="button">
                                            <button type="button" class="text-gray-500 dark:text-gray-400">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M5.999 10.495C6.827 10.495 7.499 11.167 7.499 11.995V12.005C7.499 12.834 6.827 13.505 5.999 13.505C5.171 13.505 4.499 12.834 4.499 12.005V11.995C4.499 11.167 5.171 10.495 5.999 10.495ZM17.999 10.495C18.827 10.495 19.499 11.167 19.499 11.995V12.005C19.499 12.834 18.827 13.505 17.999 13.505C17.171 13.505 16.499 12.834 16.499 12.005V11.995C16.499 11.167 17.171 10.495 17.999 10.495ZM11.999 10.495C11.171 10.495 10.499 11.167 10.499 11.995V12.005C10.499 12.834 11.171 13.505 11.999 13.505C12.827 13.505 13.499 12.834 13.499 12.005V11.995C13.499 11.167 12.827 10.495 11.999 10.495Z"
                                                        fill="currentColor" />
                                                </svg>
                                            </button>
                                        </x-slot>

                                        <x-slot name="content">
                                            <button
                                                @click="
                                                    isOpen = false;
                                                    openEditModal(category);
                                                "
                                                class="flex w-full px-3 py-2 text-left text-gray-500 text-theme-xs hover:bg-gray-100">
                                                Edit
                                            </button>

                                            <button
                                                @click="
                                                    isOpen = false;
                                                    deleteCategory(category.id);
                                                "
                                                class="flex w-full px-3 py-2 text-left text-red-700 text-theme-xs hover:bg-red-50">
                                                Hapus
                                            </button>
                                        </x-slot>

                                    </x-common.table-dropdown>
                                </div>
                            </td>
                        </tr>
                    </template>
                </x-slot:tbody>

                {{-- PAGINATION --}}
                <x-slot:pagination>
                    @include('partials.pagination-tailadmin')
                </x-slot:pagination>

            </x-tables.categories.table>

        </div>
    </x-common.component-card>

    <x-script.categories.index />
@endsection
