<!-- ===================== -->
<!-- EDIT ITEM MODAL -->
<!-- ===================== -->

<!-- Backdrop -->
<div x-show="editOpen" x-transition.opacity
    class="fixed inset-0 z-40 bg-black/50 backdrop-blur-sm"
    @click="editOpen = false">
</div>

<!-- Modal -->
<div x-show="editOpen" x-transition @keydown.escape.window="editOpen = false"
    class="fixed inset-0 z-50 flex items-center justify-center px-4">

    <div class="w-full max-w-md rounded-xl bg-white p-5 shadow-xl dark:bg-gray-900" @click.stop>

        <!-- Header -->
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                Edit Item
            </h2>

            <button @click="editOpen = false"
                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                ✕
            </button>
        </div>

        <!-- Form -->
        <form @submit.prevent="submitEdit()" class="space-y-4">

            <!-- Item Name -->
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    Item Name
                </label>

                <input type="text" x-model="editItem.name" required
                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm
                           dark:border-gray-700 dark:bg-gray-900 dark:text-white">
            </div>

            <!-- Category -->
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    Category
                </label>

                <select x-model="editItem.category_id" required
                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm
                           dark:border-gray-700 dark:bg-gray-900 dark:text-white">

                    <template x-for="category in categories" :key="category.id">
                        <option :value="category.id" x-text="category.name"></option>
                    </template>
                </select>
            </div>

            <!-- Stock -->
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    Stock
                </label>

                <input type="number" min="0" x-model="editItem.stock" required
                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm
                           dark:border-gray-700 dark:bg-gray-900 dark:text-white">
            </div>

            <!-- Status -->
            <div class="flex items-center gap-2">
                <input type="checkbox" x-model="editItem.is_active"
                    class="rounded border-gray-300 dark:border-gray-700">
                <span class="text-sm text-gray-700 dark:text-gray-300">
                    Active
                </span>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-2 pt-4">
                <button type="button" @click="editOpen = false"
                    class="rounded-lg border px-4 py-2 text-sm text-gray-600 dark:text-gray-300">
                    Cancel
                </button>

                <button type="submit"
                    class="rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary/90">
                    Update
                </button>
            </div>

        </form>

    </div>
</div>
