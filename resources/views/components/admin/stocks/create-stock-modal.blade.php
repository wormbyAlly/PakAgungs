<!-- ===================== -->
<!-- CREATE STOCK MODAL -->
<!-- ===================== -->
<button @click="createOpen = true"
    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-3 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200">
    + Add Stock
</button>
<!-- Backdrop -->
<div x-show="createOpen" x-transition.opacity class="fixed inset-0 z-40 bg-black/50 backdrop-blur-sm"
    @click="createOpen = false">
</div>

<!-- Modal -->
<div x-show="createOpen" x-transition @keydown.escape.window="createOpen = false"
    class="fixed inset-0 z-50 flex items-center justify-center px-4">

    <div class="w-full max-w-md rounded-xl bg-white p-5 shadow-xl dark:bg-gray-900" @click.stop>

        <!-- Header -->
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                Add Stock
            </h2>

            <button @click="createOpen = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                ✕
            </button>
        </div>

        <!-- Form -->
        <form @submit.prevent="submitCreate()" class="space-y-4">

            <div class="relative">
                <input type="text" x-model="productQuery" @focus="showDropdown = true"
                    @keydown.escape="showDropdown = false" placeholder="Cari produk..."
                    class="h-11 w-full rounded-lg border px-4 text-sm  dark:text-white" />

                <div x-show="showDropdown && filteredProducts.length" x-transition @click.outside="showDropdown = false"
                    class="absolute z-50 mt-1 max-h-56 w-full overflow-auto
           rounded-lg border bg-gray-200 shadow-lg">

                    <template x-for="product in filteredProducts" :key="product.id">
                        <div @click="selectProduct(product)"
                            class="cursor-pointer px-4 py-2 text-sm
               hover:bg-gray-100">
                            <span x-text="product.name"></span>
                        </div>
                    </template>
                </div>
            </div>

            <!-- LOT -->
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    LOT Number
                </label>

                <input type="text" x-model="form.lot_number" required
                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm
                           dark:border-gray-700 dark:bg-gray-900 dark:text-white">
            </div>

            <!-- Expired -->
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    Expired Date
                </label>

                <input type="date" x-model="form.expired" required
                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm
                           dark:border-gray-700 dark:bg-gray-900 dark:text-white">
            </div>

            <!-- Qty -->
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    Quantity
                </label>

                <input type="number" min="1" x-model="form.qty" required
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
