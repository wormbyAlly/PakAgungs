<div x-data="{ open: false }">
    <!-- Trigger Button -->
    <button @click="open = true"
        class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-3 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200">
        + Create User
    </button>

    <!-- Backdrop -->
    <div x-show="open" x-transition.opacity class="fixed inset-0 z-40 bg-black/50 backdrop-blur-sm" @click="open = false">
    </div>

    <!-- Modal -->
    <div x-show="open" x-transition @keydown.escape.window="open = false"
        class="fixed inset-0 z-50 flex items-center justify-center px-4">

        <div class="w-full max-w-md rounded-xl bg-white p-5 shadow-xl dark:bg-gray-900">

            <!-- Header -->
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                    Create User
                </h2>

                <button @click="open = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    ✕
                </button>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
                @csrf


                <!-- Name -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Name
                    </label>
                    <input type="text" name="name" required
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10
                       dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300
                       bg-transparent px-4 py-2.5 text-sm text-gray-800
                       dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                </div>

                <!-- Email -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Email
                    </label>
                    <input type="email" name="email" required placeholder="user@example.com"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10
                       dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300
                       bg-transparent px-4 py-2.5 text-sm text-gray-800
                       placeholder:text-gray-400 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                </div>


                <!-- Role -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Role
                    </label>

                    <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                        <select name="role" required @change="isOptionSelected = true"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10
                       dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg
                       border border-gray-300 bg-transparent px-4 py-2.5 pr-11 text-sm
                       text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                            <option value="">Select Role</option>
                            <option value="admin">Admin</option>
                            <option value="user">User</option>
                        </select>

                        <span class="pointer-events-none absolute top-1/2 right-4 -translate-y-1/2 text-gray-500">
                            ▼
                        </span>
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Password
                    </label>

                    <div x-data="{ showPassword: false }" class="relative">
                        <input :type="showPassword ? 'text' : 'password'" name="password" required
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10
                       dark:focus:border-brand-800 h-11 w-full rounded-lg border
                       border-gray-300 bg-transparent px-4 py-2.5 pr-11 text-sm
                       text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">

                        <span @click="showPassword = !showPassword"
                            class="absolute top-1/2 right-4 -translate-y-1/2 cursor-pointer text-gray-500">
                            👁
                        </span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" @click="open = false"
                        class="rounded-lg border px-4 py-2 text-sm text-gray-600 dark:text-gray-300">
                        Cancel
                    </button>

                    <button type="submit"
                        class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">
                        Save
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
