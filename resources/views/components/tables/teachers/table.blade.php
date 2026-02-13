<div class="rounded-2xl border border-gray-200 bg-white pt-4 dark:border-gray-800 dark:bg-white/[0.03]">
    <!-- Header -->
    <div class="flex flex-col gap-2 px-5 mb-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
        <!-- KIRI: Judul -->
        <div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                List Guru
            </h3>
        </div>

        <!-- KANAN: Action -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <button class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-3
           text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50
           dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400" @click="createOpen = true">+ Tambah Guru</button>

            <x-admin.teachers.create-teacher-modal />
            <x-admin.teachers.edit-teacher-modal />

            <form>
                {{ $search }}
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-hidden">
        <div class="max-w-full px-5 overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    {{ $thead }}
                </thead>

                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    {{ $tbody }}
                </tbody>
            </table>
        </div>
    </div>

    {{ $pagination }}
</div>