<div class="rounded-2xl border border-gray-200 bg-white pt-4 dark:border-gray-800 dark:bg-white/[0.03]">
    <!-- Header -->
    <div class="flex flex-col gap-2 px-5 mb-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                Checkout
            </h3>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <x-admin.products.create-product-modal />
            <x-admin.products.edit-product-modal />
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
