<script>
function itemTable() {
    return {
        // =====================
        // STATE
        // =====================
        items: [],
        categories: [],
        categoryQuery: '',
        selectedCategory: null,
        showDropdown: false,

        search: '',
        currentPage: 1,
        totalPages: 1,

        // =====================
        // MODAL STATE
        // =====================
        createOpen: false,
        editOpen: false,

        editItem: {
            id: null,
            name: '',
            category_id: '',
            stock: '',
        },

        form: {
            name: '',
            category_id: '',
            stock: '',
        },

        // =====================
        // INIT
        // =====================
        init() {
            this.fetchItems()
            this.fetchCategories()

            this.$watch('search', () => {
                this.currentPage = 1
                this.fetchItems()
            })
        },

        // =====================
        // CATEGORY SEARCH
        // =====================
        get filteredCategories() {
            if (this.categoryQuery.trim() === '') return []

            return this.categories.filter(c =>
                c.name.toLowerCase().includes(this.categoryQuery.toLowerCase())
            )
        },

        selectCategory(category) {
            this.form.category_id = category.id
            this.categoryQuery = category.name
            this.showDropdown = false
        },

        // =====================
        // FETCH ITEMS
        // =====================
        fetchItems(page = 1) {
            this.currentPage = page

            const params = new URLSearchParams({
                page,
                search: this.search.trim()
            })

            fetch(`/admin/items?${params.toString()}`, {
                headers: { Accept: 'application/json' }
            })
            .then(async res => {
                const data = await res.json()
                if (!res.ok) throw data
                return data
            })
            .then(data => {
                this.items = data.data
                this.currentPage = data.current_page
                this.totalPages = data.last_page
            })
            .catch(() => toast.error('Gagal memuat data barang'))
        },

        // =====================
        // FETCH CATEGORIES
        // =====================
        fetchCategories() {
            fetch('/admin/categories', {
                headers: { Accept: 'application/json' }
            })
            .then(async res => {
                const data = await res.json()
                if (!res.ok) throw data
                return data
            })
            .then(data => {
                this.categories = data.data
            })
            .catch(() => toast.error('Gagal memuat kategori'))
        },

        // =====================
        // CREATE
        // =====================
        openCreateModal() {
            this.resetForm()
            this.createOpen = true
        },

        submitCreate() {
            if (!this.form.name || !this.form.category_id) {
                toast.error('Nama dan kategori wajib diisi')
                return
            }

            fetch('/admin/items', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify(this.form),
            })
            .then(async res => {
                const data = await res.json()
                if (!res.ok) throw data
                return data
            })
            .then(() => {
                toast.success('Barang berhasil ditambahkan')
                this.createOpen = false
                this.fetchItems(this.currentPage)
                this.resetForm()
            })
            .catch(err => {
                toast.error(err.message || 'Gagal menyimpan barang')
            })
        },

        // =====================
        // EDIT
        // =====================
        openEditModal(item) {
            this.editItem = {
                id: item.id,
                name: item.name,
                category_id: item.category.id,
                stock: item.stock,
            }
            this.categoryQuery = item.category.name
            this.editOpen = true
        },

        submitEdit() {
            fetch(`/admin/items/${this.editItem.id}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify(this.editItem),
            })
            .then(async res => {
                const data = await res.json()
                if (!res.ok) throw data
                return data
            })
            .then(() => {
                toast.success('Barang berhasil diperbarui')
                this.editOpen = false
                this.fetchItems(this.currentPage)
            })
            .catch(err => {
                toast.error(err.message || 'Gagal update barang')
            })
        },

        closeEditModal() {
            this.editOpen = false
            this.resetForm()
        },

        // =====================
        // DELETE
        // =====================
        deleteItem(id) {
            if (!confirm('Yakin ingin menghapus barang ini?')) return

            fetch(`/admin/items/${id}`, {
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
            .then(() => {
                toast.success('Barang berhasil dihapus')
                this.fetchItems(this.currentPage)
            })
            .catch(err => {
                toast.error(err.message || 'Gagal menghapus barang')
            })
        },

        // =====================
        // UTIL
        // =====================
        resetForm() {
            this.form = {
                name: '',
                category_id: '',
                stock: '',
            }
            this.categoryQuery = ''
        },

        // =====================
        // PAGINATION
        // =====================
        nextPage() {
            if (this.currentPage < this.totalPages) {
                this.fetchItems(this.currentPage + 1)
            }
        },

        prevPage() {
            if (this.currentPage > 1) {
                this.fetchItems(this.currentPage - 1)
            }
        },

        goToPage(page) {
            if (page !== '...') {
                this.fetchItems(page)
            }
        },
    }
}
</script>
