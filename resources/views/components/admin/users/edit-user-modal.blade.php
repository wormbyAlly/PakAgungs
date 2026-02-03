<!-- ===================== -->
<!-- EDIT USER MODAL -->
<!-- ===================== -->

<div x-show="editOpen" x-transition.opacity
     class="fixed inset-0 z-40 bg-black/50 backdrop-blur-sm"
     @click="closeEditModal()">
</div>

<div x-show="editOpen" x-transition
     @keydown.escape.window="closeEditModal()"
     class="fixed inset-0 z-50 flex items-center justify-center px-4">

    <div class="w-full max-w-md rounded-xl bg-white p-5 shadow-xl dark:bg-gray-900"
         @click.stop>

        <!-- Header -->
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                Edit User
            </h2>

            <button @click="closeEditModal()"
                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                ✕
            </button>
        </div>

        <!-- Form -->
        <form @submit.prevent="submitEdit()" class="space-y-4">

            <!-- Name -->
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    Name
                </label>
                <input type="text" x-model="editUser.name" required
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
                <input type="email" x-model="editUser.email" required
                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10
                           dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300
                           bg-transparent px-4 py-2.5 text-sm text-gray-800
                           dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
            </div>

            <!-- Role -->
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    Role
                </label>

                <div class="relative z-20 bg-transparent">
                    <select x-model="editUser.role" required
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10
                               dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg
                               border border-gray-300 bg-transparent px-4 py-2.5 pr-11 text-sm
                               text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                        <option value="admin">Admin</option>
                        <option value="user">User</option>
                    </select>

                    <span class="pointer-events-none absolute top-1/2 right-4 -translate-y-1/2 text-gray-500">
                        ▼
                    </span>
                </div>
            </div>


            <!-- Password (Optional) -->
            <div x-data="{ showPassword: false }">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    Password (optional)
                </label>

                <div class="relative">
                    <input :type="showPassword ? 'text' : 'password'"
                           x-model="editUser.password"
                           placeholder="Kosongkan jika tidak diubah"
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
