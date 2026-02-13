<!-- ===================== -->
<!-- CREATE ITEM MODAL -->
<!-- ===================== -->

<button @click="createOpen = true"
    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-3
           text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50
           dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
    + Add Item
</button>

<!-- Backdrop -->
<div x-show="createOpen" x-transition.opacity
    class="fixed inset-0 z-40 bg-black/50 backdrop-blur-sm"
    @click="createOpen = false">
</div>

<!-- Modal -->
<div x-show="createOpen" x-transition @keydown.escape.window="createOpen = false"
    class="fixed inset-0 z-50 flex items-center justify-center px-4">

    <div class="w-full max-w-md rounded-xl bg-white p-5 shadow-xl dark:bg-gray-900" @click.stop>

        <!-- Header -->
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                Create Item
            </h2>

            <button @click="createOpen = false"
                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                ✕
            </button>
        </div>

        <!-- Form -->
        <form @submit.prevent="submitCreate()" class="space-y-4">

            <div>
                <label class="block text-sm mb-1">Gambar</label>
                <input type="file"
                    @change="handleImage($event)"
                    accept="image/*"
                    class="w-full text-sm border rounded-lg px-3 py-2">

            </div>

            <div x-show="form.image"
                class="mt-3 flex items-center gap-3 rounded-lg border p-2 bg-gray-50 dark:bg-gray-800">

                <img :src="previewImage"
                    class="w-20 h-20 rounded-lg object-cover border">

                <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                    <template x-if="form.image">
                        <div class="font-semibold truncate max-w-[150px]"
                            x-text="form.image.name"></div>
                    </template>

                    <template x-if="form.image">
                        <div x-text="(form.image.size / 1024).toFixed(1) + ' KB'"></div>
                    </template>

                </div>

            </div>

            <!-- Item Name -->
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    Item Name
                </label>

                <input type="text" x-model="form.name" required
                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm
                           dark:border-gray-700 dark:bg-gray-900 dark:text-white">
            </div>

            <!-- Category -->
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    Category
                </label>

                <select x-model="form.category_id" required
                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm
                           dark:border-gray-700 dark:bg-gray-900 dark:text-white">

                    <option value="">-- Select Category --</option>

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

                <input type="number" min="0" x-model="form.stock" required
                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm
                           dark:border-gray-700 dark:bg-gray-900 dark:text-white">
            </div>
            <!-- Actions -->
            <div class="flex justify-end gap-2 pt-4">
                <button type="button" @click="createOpen = false"
                    class="rounded-lg border px-4 py-2 text-sm text-gray-600 dark:text-gray-300">
                    Cancel
                </button>

                <button type="submit"
                    class="rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary/90">
                    Save
                </button>
            </div>

        </form>

    </div>
</div>