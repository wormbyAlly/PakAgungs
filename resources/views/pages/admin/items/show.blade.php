@extends('layouts.app')

@section('content')
<div
    x-data="loanModal()"
    class="max-w-3xl mx-auto space-y-6"
>

    {{-- ITEM INFO --}}
    <div class="rounded-xl bg-white p-6">
        <h2 class="text-2xl font-semibold">{{ $item->name }}</h2>
        <p class="text-sm text-gray-500">
            Kategori: {{ $item->category->name }}
        </p>
    </div>

    {{-- ITEM META --}}
    <div class="grid grid-cols-2 gap-4">
        <div class="rounded-lg border p-4">
            <p class="text-sm text-gray-500">Stok</p>
            <p class="text-xl font-semibold">{{ $item->stock }}</p>
        </div>

        <div class="rounded-lg border p-4">
            <p class="text-sm text-gray-500">Status</p>
            <p class="text-sm">
                {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
            </p>
        </div>
    </div>

    {{-- ACTION --}}
    @if ($item->is_active && $item->stock > 0)
        <button
            @click="loanOpen = true"
            class="px-6 py-3 bg-blue-600 text-white rounded-lg"
        >
            Pinjam Barang
        </button>
    @else
        <p class="text-sm text-gray-500">
            Barang tidak tersedia untuk dipinjam
        </p>
    @endif


    {{-- ===================== --}}
    {{-- LOAN MODAL --}}
    {{-- ===================== --}}

    {{-- Backdrop --}}
    <div
        x-show="loanOpen"
        x-transition.opacity
        class="fixed inset-0 z-40 bg-black/50 backdrop-blur-sm"
        @click="loanOpen = false"
    ></div>

    {{-- Modal --}}
    <div
        x-show="loanOpen"
        x-transition
        class="fixed inset-0 z-50 flex items-center justify-center px-4"
        @keydown.escape.window="loanOpen = false"
    >
        <div
            class="w-full max-w-md rounded-xl bg-white p-5 shadow-xl"
            @click.stop
        >

            {{-- Header --}}
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-800">
                    Pinjam Barang
                </h2>

                <button
                    @click="loanOpen = false"
                    class="text-gray-400 hover:text-gray-600"
                >
                    ✕
                </button>
            </div>

            {{-- Form --}}
            <form @submit.prevent="submitLoan()" class="space-y-4">

                {{-- Teacher --}}
                <div class="relative">
                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        Guru Penanggung Jawab
                    </label>

                    <input
                        type="text"
                        x-model="teacherQuery"
                        @focus="showTeacherDropdown = true"
                        @keydown.escape="showTeacherDropdown = false"
                        placeholder="Cari guru..."
                        class="h-11 w-full rounded-lg border px-4 text-sm"
                    />

                    <div
                        x-show="showTeacherDropdown && filteredTeachers.length"
                        x-transition
                        @click.outside="showTeacherDropdown = false"
                        class="absolute z-50 mt-1 max-h-56 w-full overflow-auto
                               rounded-lg border bg-white shadow-lg"
                    >
                        <template x-for="teacher in filteredTeachers" :key="teacher.id">
                            <div
                                @click="selectTeacher(teacher)"
                                class="cursor-pointer px-4 py-2 text-sm hover:bg-gray-100"
                                x-text="teacher.name"
                            ></div>
                        </template>
                    </div>
                </div>

                {{-- Quantity --}}
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        Jumlah
                    </label>
                    <input
                        type="number"
                        min="1"
                        :max="{{ $item->stock }}"
                        x-model="form.quantity"
                        required
                        class="h-11 w-full rounded-lg border px-4 text-sm"
                    />
                </div>

                {{-- Location --}}
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        Lokasi
                    </label>
                    <input
                        type="text"
                        x-model="form.location"
                        
                        class="h-11 w-full rounded-lg border px-4 text-sm"
                    />
                </div>

                {{-- Loan Date --}}
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        Tanggal Peminjaman
                    </label>
                    <input
                        type="date"
                        x-model="form.loan_date"
                        required
                        class="h-11 w-full rounded-lg border px-4 text-sm"
                    />
                </div>

                {{-- Actions --}}
                <div class="flex justify-end gap-2 pt-4">
                    <button
                        type="button"
                        @click="loanOpen = false"
                        class="rounded-lg border px-4 py-2 text-sm text-gray-600"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white"
                    >
                        Simpan
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>


{{-- ===================== --}}
{{-- SCRIPT --}}
{{-- ===================== --}}
<script>
    function loanModal() {
        return {
            loanOpen: false,

            teachers: [],
            teacherQuery: '',
            selectedTeacher: null,
            showTeacherDropdown: false,

      form: {
    item_id: {{ $item->id }},
    teacher_id: null,
    quantity: 1,
    location: '',
    loan_date: '',
},

init() {
    this.fetchTeachers()

    // set default ke hari ini (format YYYY-MM-DD)
    const today = new Date().toISOString().split('T')[0]
    this.form.loan_date = today
},


            fetchTeachers() {
                fetch('/admin/teachers', {
                        headers: { Accept: 'application/json' }
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.teachers = data.data ?? data
                    })
            },

            get filteredTeachers() {
                if (!this.teacherQuery.trim()) return []
                return this.teachers.filter(t =>
                    t.name.toLowerCase().includes(this.teacherQuery.toLowerCase())
                )
            },

            selectTeacher(teacher) {
                this.selectedTeacher = teacher
                this.teacherQuery = teacher.name
                this.form.teacher_id = teacher.id
                this.showTeacherDropdown = false
            },

            submitLoan() {
                if (!this.form.teacher_id) {
                    alert('Guru penanggung jawab wajib dipilih')
                    return
                }

                fetch('/admin/loans', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            Accept: 'application/json',
                        },
                        body: JSON.stringify(this.form),
                    })
                    .then(async res => {
                        const data = await res.json()
                        if (!res.ok) throw data
                        return data
                    })
                    .then(() => {
                        this.loanOpen = false
                        location.reload()
                    })
                    .catch(err => {
                        alert(err.message || 'Gagal menyimpan peminjaman')
                    })
            },
        }
    }
</script>
@endsection
