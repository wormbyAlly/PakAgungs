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
            previewImage: null,

            search: '',
            currentPage: 1,
            totalPages: 1,
            lastPage: 1,

            displayedPages() {
                let pages = []
                for (let i = 1; i <= this.lastPage; i++) {
                    pages.push(i)
                }
                return pages
            },


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
                image: null,
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

            handleImage(event) {
                const file = event.target.files[0]
                this.form.image = file

                if (file) {
                    if (this.previewImage) {
                        URL.revokeObjectURL(this.previewImage)
                    }
                    this.previewImage = URL.createObjectURL(file)
                }
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
                        this.items = data.data
                        this.currentPage = data.current_page
                        this.lastPage = data.last_page // ← TAMBAHKAN INI
                    })

                    .catch(() => toast.error('Gagal memuat data barang'))
            },

            // =====================
            // FETCH CATEGORIES
            // =====================
            fetchCategories() {
                fetch('/admin/categories', {
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

                let formData = new FormData()
                formData.append('name', this.form.name)
                formData.append('category_id', this.form.category_id)
                formData.append('stock', this.form.stock)

                if (this.form.image) {
                    formData.append('image', this.form.image)
                }

                fetch('/admin/items', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document
                                .querySelector('meta[name="csrf-token"]')
                                .getAttribute('content'),
                            'Accept': 'application/json',
                        },
                        body: formData,
                    })
                    .then(async res => {
                        const data = await res.json()
                        if (!res.ok) throw data
                        return data
                    })
                    .then(() => {
                        this.createOpen = false
                        this.fetchItems(this.currentPage)
                        this.resetForm()
                    })
                    .catch(err => {
                        console.error(err)
                        alert('Error: ' + (err.message || 'Gagal simpan'))
                    })
            },


            // =====================
            // EDIT
            // =====================
            openEditModal(item) {
                window.dispatchEvent(new Event('close-dropdowns')); // 🔥 tutup dropdown

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

                let formData = new FormData()
                formData.append('_method', 'PUT')
                formData.append('name', this.editItem.name)
                formData.append('category_id', this.editItem.category_id)
                formData.append('stock', this.editItem.stock)

                if (this.form.image) {
                    formData.append('image', this.form.image)
                }

                fetch(`/admin/items/${this.editItem.id}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: formData,
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
                    image: null,
                }

                if (this.previewImage) {
                    URL.revokeObjectURL(this.previewImage)
                    this.previewImage = null
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