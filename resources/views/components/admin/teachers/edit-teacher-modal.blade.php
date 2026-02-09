<!-- Backdrop -->
<div x-show="editOpen" x-transition.opacity
     class="fixed inset-0 z-40 bg-black/50 backdrop-blur-sm"
     @click="closeEditModal()">
</div>

<!-- Modal -->
<div x-show="editOpen" x-transition
     @keydown.escape.window="closeEditModal()"
     class="fixed inset-0 z-50 flex items-center justify-center px-4">

    <div class="w-full max-w-md rounded-xl bg-white p-5 shadow-xl dark:bg-gray-900"
         @click.stop>

        <!-- Header -->
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                Edit Guru
            </h2>

            <button @click="closeEditModal()"
                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                ✕
            </button>
        </div>

        <!-- Form -->
        <form @submit.prevent="submitEditTeacher()" class="space-y-4">

            <!-- Nama Guru -->
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    Nama Guru
                </label>
                <input type="text" x-model="editTeacher.name" required
                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm
                           text-gray-800 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10
                           dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-2 pt-4">
                <button type="button" @click="closeEditModal()"
                    class="rounded-lg border px-4 py-2 text-sm text-gray-600 dark:text-gray-300">
                    Cancel
                </button>

                <button type="submit"
                    class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">
                    Update
                </button>
            </div>
        </form>

    </div>
</div>
