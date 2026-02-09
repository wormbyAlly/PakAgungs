@extends('layouts.app')

@section('content')

<div
    x-data="itemDashboard()"
    x-init="fetchItems()"
    class="space-y-6">

    {{-- HEADER --}}
    <div class="rounded-2xl bg-white p-6 dark:bg-white/[0.03]">
        <h2 class="text-2xl font-semibold text-gray-800 dark:text-white/90">
            Daftar Barang
        </h2>
        <p class="text-gray-500">
            Barang yang tersedia untuk dipinjam
        </p>
    </div>

    {{-- SEARCH --}}
    <div class="flex gap-3">
        <input
            type="text"
            x-model.debounce.500ms="search"
            placeholder="Cari barang atau kategori..."
            class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm
                   focus:border-brand-300 focus:ring-brand-500/10
                   dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
    </div>

    {{-- LIST ITEMS --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">

        <template x-for="item in items" :key="item.id">
            <a
                :href="`/admin/items/${item.id}`"
                class="block rounded-2xl border bg-white p-5 shadow-sm
                       hover:ring-2 hover:ring-brand-500/40
                       transition dark:bg-white/[0.03]">

                {{-- HEADER ITEM --}}
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <h3
                            class="text-lg font-semibold text-gray-800 dark:text-white/90"
                            x-text="item.name">
                        </h3>

                        <p
                            class="text-sm text-gray-500"
                            x-text="item.category.name">
                        </p>
                    </div>

                    <span
                        class="text-xs px-2 py-1 rounded-full"
                        :class="item.is_active
                            ? 'bg-green-100 text-green-700'
                            : 'bg-gray-200 text-gray-600'">
                        <span x-text="item.is_active ? 'Aktif' : 'Nonaktif'"></span>
                    </span>
                </div>

                {{-- STOK --}}
                <div class="mb-4">
                    <p class="text-sm text-gray-600">
                        Stok:
                        <span
                            class="font-semibold"
                            :class="item.stock > 0 ? 'text-gray-800' : 'text-red-600'"
                            x-text="item.stock">
                        </span>
                    </p>
                </div>

                {{-- FOOTER --}}
                <div class="text-right text-sm text-brand-600">
                    Lihat Detail →
                </div>

            </a>
        </template>

        {{-- EMPTY --}}
        <template x-if="items.length === 0">
            <div class="col-span-full text-center text-gray-500 py-10">
                Tidak ada barang ditemukan
            </div>
        </template>

    </div>

    {{-- PAGINATION --}}
    <div class="flex justify-center gap-2 mt-6">
        <button
            @click="prevPage"
            :disabled="currentPage === 1"
            class="rounded-lg border px-3 py-1 text-sm disabled:opacity-50">
            Prev
        </button>

        <span class="text-sm text-gray-600">
            Page <span x-text="currentPage"></span> / <span x-text="lastPage"></span>
        </span>

        <button
            @click="nextPage"
            :disabled="currentPage === lastPage"
            class="rounded-lg border px-3 py-1 text-sm disabled:opacity-50">
            Next
        </button>
    </div>

</div>

@endsection

@push('scripts')
<script>
function itemDashboard() {
    return {
        items: [],
        search: '',
        currentPage: 1,
        lastPage: 1,

        fetchItems(page = 1) {
            this.currentPage = page;

            const params = new URLSearchParams({
                page: this.currentPage,
                search: this.search
            });

            fetch(`/admin/items?${params.toString()}`, {
                headers: { 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                this.items = data.data;
                this.currentPage = data.current_page;
                this.lastPage = data.last_page;
            })
            .catch(() => {
                toast?.error?.('Gagal memuat barang');
            });
        },

        nextPage() {
            if (this.currentPage < this.lastPage) {
                this.fetchItems(this.currentPage + 1);
            }
        },

        prevPage() {
            if (this.currentPage > 1) {
                this.fetchItems(this.currentPage - 1);
            }
        },

        init() {
            this.$watch('search', () => {
                this.fetchItems(1);
            });
        }
    }
}
</script>
@endpush
