@extends('layouts.app')

@section('content')

<x-common.component-card title="Peminjaman Barang">

    <div x-data="loanPage()" x-init="init()" class="space-y-6">

        {{-- SEARCH --}}
        <div class="flex justify-between items-center">

            <div class="w-full max-w-md relative">
                <input
                    type="text"
                    x-model.debounce.500ms="search"
                    placeholder="Cari barang..."
                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm
                           focus:border-blue-300 focus:ring-2 focus:ring-blue-500/10
                           dark:border-gray-700 dark:bg-gray-900 dark:text-white">
            </div>

        </div>

        {{-- LIST ITEMS --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">

            <template x-for="item in items" :key="item.id">
                <div
                    class="rounded-2xl border bg-white p-5 shadow-sm
               hover:ring-2 hover:ring-blue-500/30 transition
               dark:bg-white/[0.03]">

                    <div class="flex gap-5">

                        <!-- IMAGE -->
                        <div class="flex-shrink-0">
                            <template x-if="item.image">
                                <img :src="item.image"
                                    @click="imagePreview = item.image"
                                    class="w-24 h-24 object-cover rounded-xl border cursor-pointer hover:opacity-80 transition">
                            </template>

                            <template x-if="!item.image">
                                <div class="w-24 h-24 flex items-center justify-center
                    bg-gray-100 rounded-xl text-gray-400 text-xs border">
                                    No Image
                                </div>
                            </template>
                        </div>
<!-- IMAGE PREVIEW MODAL -->
<div x-show="imagePreview"
     x-transition.opacity
     @keydown.escape.window="imagePreview = null"
     class="fixed inset-0 z-[99999] bg-black/80 flex items-center justify-center p-6"
     @click="imagePreview = null">

    <img :src="imagePreview"
         class="max-w-full max-h-full rounded-xl shadow-2xl"
         @click.stop>

</div>



                        <!-- CONTENT -->
                        <div class="flex-1 flex flex-col justify-between">

                            <!-- TOP -->
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90"
                                        x-text="item.name"></h3>

                                    <p class="text-sm text-gray-500 mt-1"
                                        x-text="item.category.name"></p>
                                </div>

                                <span
                                    class="text-xs px-3 py-1 rounded-full"
                                    :class="item.is_active
                            ? 'bg-green-100 text-green-700'
                            : 'bg-gray-200 text-gray-600'">
                                    <span x-text="item.is_active ? 'Aktif' : 'Nonaktif'"></span>
                                </span>
                            </div>

                            <!-- BOTTOM -->
                            <div class="flex justify-between items-end mt-4">
                                <p class="text-sm text-gray-600">
                                    Stok:
                                    <span class="font-semibold"
                                        :class="item.stock > 0 ? 'text-gray-700' : 'text-red-600'"
                                        x-text="item.stock"></span>
                                </p>

                                <button
                                    @click="openLoanModal(item)"
                                    :disabled="!item.is_active || item.stock <= 0"
                                    :class="(!item.is_active || item.stock <= 0)
                            ? 'bg-gray-400 cursor-not-allowed'
                            : 'bg-blue-600 hover:bg-blue-700'"
                                    class="px-4 py-2 text-sm text-white rounded-lg shadow-sm">
                                    Pinjam
                                </button>
                            </div>

                        </div>
                    </div>

                </div>
            </template>

            <template x-if="items.length === 0">
                <div class="col-span-full text-center text-gray-500 py-10">
                    Tidak ada barang ditemukan
                </div>
            </template>

        </div>

        {{-- PAGINATION --}}
        <div class="flex justify-between items-center mt-6">
            <button
                @click="prevPage"
                :disabled="currentPage === 1"
                class="px-4 py-2 rounded-lg border disabled:opacity-50">
                Previous
            </button>

            <span class="text-sm text-gray-600">
                Page <span x-text="currentPage"></span>
                of <span x-text="lastPage"></span>
            </span>

            <button
                @click="nextPage"
                :disabled="currentPage === lastPage"
                class="px-4 py-2 rounded-lg border disabled:opacity-50">
                Next
            </button>
        </div>

        {{-- ===================== --}}
        {{-- LOAN MODAL --}}
        {{-- ===================== --}}
        {{-- BACKDROP --}}
        <div x-show="loanOpen"
            x-transition.opacity
            class="fixed inset-0 z-40 bg-black/60 backdrop-blur-sm"
            x-init="$watch('loanOpen', value => {
        document.body.classList.toggle('overflow-hidden', value)
     })">
        </div>

        {{-- MODAL WRAPPER --}}
        <div x-show="loanOpen"
            x-transition
            class="fixed inset-0 z-[9999] flex justify-center
            overflow-y-auto">

            <div class="w-full max-w-lg
                mt-24 mb-10
                max-h-[85vh] overflow-y-auto
                rounded-2xl bg-white shadow-2xl
                dark:bg-gray-900 border
                border-gray-200 dark:border-gray-700"
                @click.stop>

                <div class="px-6 py-6 space-y-6">

                    {{-- HEADER --}}
                    <div class="flex items-center justify-between border-b pb-4
                        border-gray-200 dark:border-gray-700">

                        <div>
                            <h2 class="text-lg font-semibold text-gray-800 dark:text-white">
                                Buat Peminjaman
                            </h2>
                            <p class="text-xs text-gray-500">
                                Periksa kembali sebelum menyimpan
                            </p>
                        </div>

                        <button @click="loanOpen = false"
                            class="text-gray-400 hover:text-red-500 transition">
                            ✕
                        </button>
                    </div>

                    {{-- ITEM INFO --}}
                    <div class="rounded-xl bg-gray-50 dark:bg-white/[0.03] p-4 border
                        border-gray-200 dark:border-gray-700">

                        <p class="text-sm text-gray-500 mb-1">Barang Dipilih</p>

                        <div class="flex justify-between items-center">
                            <div>
                                <p class="font-semibold text-gray-800 dark:text-white"
                                    x-text="selectedItem?.name"></p>
                                <p class="text-xs text-gray-500"
                                    x-text="selectedItem?.category?.name"></p>
                            </div>

                            <span class="text-xs px-3 py-1 rounded-full"
                                :class="selectedItem?.stock > 0
                              ? 'bg-green-100 text-green-700'
                              : 'bg-red-100 text-red-600'">
                                Stok:
                                <span x-text="selectedItem?.stock"></span>
                            </span>
                        </div>
                    </div>

                    {{-- FORM --}}
                    <form @submit.prevent="submitLoan()" class="space-y-5">

                        <div class="relative" @click.away="teacherOpen = false">

                            <label class="block text-sm mb-1">Guru</label>

                            <!-- INPUT DISPLAY -->
                            <input type="text"
                                x-model="teacherSearch"
                                @focus="teacherOpen = true"
                                placeholder="Cari guru..."
                                class="w-full rounded-lg border px-3 py-2 text-sm
                                focus:ring-2 focus:ring-blue-500/20
                                dark:bg-gray-800 dark:border-gray-700">

                            <!-- DROPDOWN -->
                            <div x-show="teacherOpen"
                                x-transition
                                class="absolute z-50 mt-1 w-full max-h-48 overflow-y-auto
                                rounded-lg border bg-white shadow-lg
                                dark:bg-gray-800 dark:border-gray-700">

                                <template x-for="teacher in filteredTeachers()" :key="teacher.id">
                                    <div @click="selectTeacher(teacher)"
                                        class="px-3 py-2 cursor-pointer hover:bg-gray-100
                        dark:hover:bg-gray-700 text-sm"
                                        x-text="teacher.name">
                                    </div>
                                </template>

                                <div x-show="filteredTeachers().length === 0"
                                    class="px-3 py-2 text-sm text-gray-400">
                                    Tidak ditemukan
                                </div>

                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm mb-1">Jumlah</label>
                                <input type="number"
                                    min="1"
                                    :max="selectedItem?.stock"
                                    x-model="form.quantity"
                                    required
                                    class="w-full rounded-lg border px-3 py-2 text-sm
                                      focus:ring-2 focus:ring-blue-500/20
                                      dark:bg-gray-800 dark:border-gray-700">
                            </div>

                            <div>
                                <label class="block text-sm mb-1">Tanggal</label>
                                <input type="datetime-local"
                                    x-model="form.loan_date"
                                    required
                                    class="form-control w-full rounded-lg border px-3 py-2 text-sm
                                      focus:ring-2 focus:ring-blue-500/20
                                      dark:bg-gray-800 dark:border-gray-700">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm mb-1">Lokasi (Opsional)</label>
                            <input type="text"
                                x-model="form.location"
                                class="w-full rounded-lg border px-3 py-2 text-sm
                                  focus:ring-2 focus:ring-blue-500/20
                                  dark:bg-gray-800 dark:border-gray-700">
                        </div>

                        <div class="flex justify-end gap-3 pt-4 border-t
                            border-gray-200 dark:border-gray-700">

                            <button type="button"
                                @click="loanOpen = false"
                                class="px-4 py-2 rounded-lg border
                                   hover:bg-gray-100 dark:hover:bg-gray-800">
                                Batal
                            </button>

                            <button type="submit"
                                class="px-5 py-2 rounded-lg bg-blue-600 text-white
                                   hover:bg-blue-700 transition shadow-sm">
                                Simpan
                            </button>

                        </div>

                    </form>

                </div>
            </div>
        </div>



    </div>

</x-common.component-card>

@endsection


@push('scripts')
<script>
    function loanPage() {
        return {

            items: [],
            teachers: [],
            search: '',
            currentPage: 1,
            lastPage: 1,

            loanOpen: false,
            selectedItem: null,
            teacherOpen: false,
            teacherSearch: '',

            imagePreview: null,

            form: {
                item_id: null,
                teacher_id: '',
                quantity: 1,
                location: '',
                loan_date: ''
            },

            init() {
                this.fetchItems()
                this.fetchTeachers()

                const now = new Date()
                const offset = now.getTimezoneOffset()
                const local = new Date(now.getTime() - (offset * 60000))
                    .toISOString()
                    .slice(0, 16)

                this.form.loan_date = local

                this.$watch('search', () => {
                    this.fetchItems(1)
                })
            },

            fetchItems(page = 1) {
                this.currentPage = page

                const params = new URLSearchParams({
                    page,
                    search: this.search
                })

                fetch(`/admin/items?${params.toString()}`, {
                        headers: {
                            Accept: 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.items = data.data
                        this.currentPage = data.current_page
                        this.lastPage = data.last_page
                    })
            },

            fetchTeachers() {
                fetch('/admin/teachers', {
                        headers: {
                            Accept: 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.teachers = data.data ?? data
                    })
            },

            openLoanModal(item) {
                this.selectedItem = item
                this.form.item_id = item.id
                this.form.quantity = 1
                this.loanOpen = true
            },
            filteredTeachers() {
                if (!this.teacherSearch) return this.teachers

                return this.teachers.filter(t =>
                    t.name.toLowerCase().includes(this.teacherSearch.toLowerCase())
                )
            },

            selectTeacher(teacher) {
                this.form.teacher_id = teacher.id
                this.teacherSearch = teacher.name
                this.teacherOpen = false
            },


            submitLoan() {
                if (!this.form.teacher_id) {
                    alert('Guru wajib dipilih')
                    return
                }

                fetch('/admin/loans', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            Accept: 'application/json'
                        },
                        body: JSON.stringify(this.form)
                    })
                    .then(async res => {
                        const data = await res.json()
                        if (!res.ok) throw data
                        return data
                    })
                    .then(() => {
                        this.loanOpen = false
                        this.fetchItems(this.currentPage)
                    })
                    .catch(err => {
                        alert(err.message || 'Gagal menyimpan')
                    })
            },

            nextPage() {
                if (this.currentPage < this.lastPage)
                    this.fetchItems(this.currentPage + 1)
            },

            prevPage() {
                if (this.currentPage > 1)
                    this.fetchItems(this.currentPage - 1)
            }

        }
    }
</script>
@endpush