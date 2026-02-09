@props(['categories'])

<div class="rounded-2xl border border-gray-200 bg-white pt-4 dark:border-gray-800 dark:bg-white/[0.03]">
    <!-- Header -->
    <div class="flex flex-col gap-2 px-5 mb-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                List Barang
            </h3>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            {{-- Modal Create --}}
            <x-admin.items.create-item-modal :categories="$categories" />

            {{-- Modal Edit --}}
            <x-admin.items.edit-item-modal :categories="$categories" />

            {{-- Search --}}
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

    {{-- Pagination --}}
    {{ $pagination }}
</div>
