<!-- ===================== -->
<!-- CREATE LOAN MODAL -->
<!-- ===================== -->
<button @click="loanOpen = true"
    class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-3 text-sm font-medium text-white hover:bg-blue-700">
    Pinjam Barang
</button>

<!-- Backdrop -->
<div x-show="loanOpen" x-transition.opacity
    class="fixed inset-0 z-40 bg-black/50 backdrop-blur-sm"
    @click="loanOpen = false">
</div>

<!-- Modal -->
<div x-show="loanOpen" x-transition
    @keydown.escape.window="loanOpen = false"
    class="fixed inset-0 z-50 flex items-center justify-center px-4">

    <div class="w-full max-w-md rounded-xl bg-white p-5 shadow-xl" @click.stop>

        <!-- Header -->
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-800">
                Peminjaman Barang
            </h2>

            <button @click="loanOpen = false"
                class="text-gray-400 hover:text-gray-600">
                ✕
            </button>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('loans.store') }}" class="space-y-4">
            @csrf

            <!-- ITEM ID (hidden, karena dari item detail page) -->
            <input type="hidden" name="item_id" value="{{ $item->id }}">

            <!-- TEACHER DROPDOWN -->
            <div class="relative">
                <label class="mb-1 block text-sm font-medium text-gray-700">
                    Guru Penanggung Jawab
                </label>

                <input type="text"
                    x-model="teacherQuery"
                    @focus="showTeacherDropdown = true"
                    @keydown.escape="showTeacherDropdown = false"
                    placeholder="Cari guru..."
                    class="h-11 w-full rounded-lg border px-4 text-sm" />

                <input type="hidden" name="teacher_id" :value="selectedTeacher?.id">

                <div x-show="showTeacherDropdown && filteredTeachers.length"
                    x-transition
                    @click.outside="showTeacherDropdown = false"
                    class="absolute z-50 mt-1 max-h-56 w-full overflow-auto
                           rounded-lg border bg-white shadow-lg">

                    <template x-for="teacher in filteredTeachers" :key="teacher.id">
                        <div
                            @click="
                                selectedTeacher = teacher;
                                teacherQuery = teacher.name;
                                showTeacherDropdown = false;
                            "
                            class="cursor-pointer px-4 py-2 text-sm hover:bg-gray-100">
                            <span x-text="teacher.name"></span>
                        </div>
                    </template>
                </div>
            </div>

            <!-- QUANTITY -->
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                    Jumlah
                </label>

                <input type="number" name="quantity" min="1" required
                    class="h-11 w-full rounded-lg border px-4 text-sm">
            </div>

            <!-- LOCATION -->
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                    Lokasi Penggunaan
                </label>

                <input type="text" name="location" required
                    class="h-11 w-full rounded-lg border px-4 text-sm">
            </div>

            <!-- LOAN DATE -->
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                    Tanggal Peminjaman
                </label>

                <input type="date" name="loan_date" required
                    class="h-11 w-full rounded-lg border px-4 text-sm">
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-2 pt-4">
                <button type="button"
                    @click="loanOpen = false"
                    class="rounded-lg border px-4 py-2 text-sm text-gray-600">
                    Cancel
                </button>

                <button type="submit"
                    class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                    Simpan
                </button>
            </div>
        </form>

    </div>
</div>
