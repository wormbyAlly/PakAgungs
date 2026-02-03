<script>
    function userTable() {
        return {
            users: [],
            search: '',
            currentPage: 1,
            totalPages: 1,

            // =====================
            // EDIT MODAL STATE
            // =====================
            editOpen: false,
            editUser: {
                id: null,
                name: '',
                email: '',
                role: '',
                status: '',
                password: '',
            },

            // =====================
            // INIT
            // =====================
            init() {
                this.fetchUsers()

                this.$watch('search', () => {
                    this.currentPage = 1
                    this.fetchUsers()
                })
            },

            // =====================
            // FETCH USERS
            // =====================
            fetchUsers(page = 1) {
                this.currentPage = page

                const params = new URLSearchParams({
                    page,
                    search: this.search.trim()
                })

                fetch(`/admin/users?${params.toString()}`, {
                        headers: {
                            Accept: 'application/json'
                        }
                    })
                    .then(async res => {
                        const data = await res.json()
                        if (!res.ok) throw data
                        return data
                    })
                    .then(data => {
                        this.users = data.data
                        this.currentPage = data.current_page
                        this.totalPages = data.last_page
                    })
                    .catch(() => {
                        toast.error('Gagal memuat data user')
                    })
            },

            // =====================
            // EDIT
            // =====================
            openEditModal(user) {
                this.editUser = {
                    id: user.id,
                    name: user.name,
                    email: user.email,
                    role: user.role,
                    status: user.status,
                    password: '',
                }
                this.editOpen = true
            },

            closeEditModal() {
                this.editOpen = false
                this.editUser.password = ''
            },

            submitEdit() {
                fetch(`/admin/users/${this.editUser.id}`, {
                        method: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(this.editUser),
                    })
                    .then(async res => {
                        const data = await res.json()
                        if (!res.ok) throw data
                        return data
                    })
                    .then(() => {
                        toast.success('User berhasil diperbarui')
                        this.closeEditModal()
                        this.fetchUsers(this.currentPage)
                    })
                    .catch(err => {
                        toast.error(err.message || 'Gagal update user')
                    })
            },

            // =====================
            // TOGGLE STATUS
            // =====================
            toggleStatus(userId) {
                if (!confirm('Ubah status user ini?')) return

                fetch(`/admin/users/${userId}/toggle-status`, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(async res => {
                        const data = await res.json()
                        if (!res.ok) throw data
                        return data
                    })
                    .then(data => {
                        toast.success(`Status user menjadi ${data.status}`)
                        this.fetchUsers(this.currentPage)
                    })
                    .catch(err => {
                        toast.error(err.message || 'Gagal mengubah status')
                    })
            },

            // =====================
            // DELETE
            // =====================
            deleteUser(userId) {
                if (!confirm('Yakin ingin menghapus user ini?')) return

                fetch(`/admin/users/${userId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(async res => {
                        const data = await res.json()
                        if (!res.ok) throw data
                        return data
                    })
                    .then(data => {
                        toast.success(data.message || 'User berhasil dihapus')
                        this.fetchUsers(this.currentPage)
                    })
                    .catch(err => {
                        toast.error(err.message || 'Gagal menghapus user')
                    })
            },
            get displayedPages() {
                const pages = [];
                for (let i = 1; i <= this.totalPages; i++) {
                    if (
                        i === 1 ||
                        i === this.totalPages ||
                        (i >= this.currentPage - 1 && i <= this.currentPage + 1)
                    ) {
                        pages.push(i);
                    } else if (pages[pages.length - 1] !== '...') {
                        pages.push('...');
                    }
                }
                return pages;
            },

            // =====================
            // PAGINATION
            // =====================
            nextPage() {
                if (this.currentPage < this.totalPages) {
                    this.fetchUsers(this.currentPage + 1)
                }
            },

            prevPage() {
                if (this.currentPage > 1) {
                    this.fetchUsers(this.currentPage - 1)
                }
            },

            goToPage(page) {
                if (page !== '...') {
                    this.fetchUsers(page)
                }
            },
        }
    }
</script>
