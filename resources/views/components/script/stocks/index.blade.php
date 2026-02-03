<script>
    function stockTable() {
        return {
            // =====================
            // STATE
            // =====================
            stocks: [],
            products: [],
            productSearch: '',
            productQuery: '',
            selectedProduct: null,
            showDropdown: false,
            search: '',
            currentPage: 1,
            totalPages: 1,

            // =====================
            // MODAL STATE
            // =====================
            createOpen: false,
            editOpen: false,

            editStock: {
                id: null,
                product_id: '',
                lot_number: '',
                expired: '',
                qty: '',
            },

            form: {
                id: null,
                product_id: '',
                lot_number: '',
                expired: '',
                qty: '',
            },

            // =====================
            // INIT
            // =====================
            init() {
                this.fetchStocks()
                this.fetchProducts()

                this.$watch('search', () => {
                    this.currentPage = 1
                    this.fetchStocks()
                })
            },

            // =====================
            // Search Modal
            // =====================
            get filteredProducts() {
                if (this.productQuery.trim() === '') return []

                return this.products.filter(p =>
                    p.name.toLowerCase().includes(this.productQuery.toLowerCase())
                )
            },

            // =====================
            // Auto Fill
            // =====================
            selectProduct(product) {
                this.form.product_id = product.id
                this.productQuery = product.name
                this.showDropdown = false
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
            // FETCH STOCKS
            // =====================
            fetchStocks(page = 1) {
                this.currentPage = page

                const params = new URLSearchParams({
                    page,
                    search: this.search.trim()
                })

                fetch(`/admin/stocks?${params.toString()}`, {
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
                        this.stocks = data.data
                        this.currentPage = data.current_page
                        this.totalPages = data.last_page
                    })
                    .catch(() => {
                        toast.error('Gagal memuat data stock')
                    })
            },

            // =====================
            // FETCH PRODUCTS
            // =====================
            fetchProducts() {
                fetch('/admin/products', {
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
                        this.products = data.data
                    })
                    .catch(() => {
                        toast.error('Gagal memuat produk')
                    })
            },

            // =====================
            // CREATE
            // =====================
            openCreateModal() {
                this.resetForm()
                this.createOpen = true
            },

            submitCreate() {
                if (!this.form.product_id) {
                    toast.error('Silakan pilih produk')
                    return
                }

                fetch('/admin/stocks', {
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
                        toast.success('Stock berhasil ditambahkan')
                        this.createOpen = false
                        this.fetchStocks(this.currentPage)
                        this.resetForm()
                    })
                    .catch(err => {
                        toast.error(err.message || 'Gagal menyimpan stock')
                    })
            },

            // =====================
            // EDIT
            // =====================
            openEditModal(stock) {
                this.editStock = {
                    id: stock.id,
                    product_id: stock.product.id,
                    lot_number: stock.lot_number,
                    expired: stock.expired,
                    qty: stock.qty,
                }
                this.editOpen = true
            },

            submitEdit() {
                fetch(`/admin/stocks/${this.editStock.id}`, {
                        method: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(this.editStock),
                    })
                    .then(async res => {
                        const data = await res.json()
                        if (!res.ok) throw data
                        return data
                    })
                    .then(() => {
                        toast.success('Stock berhasil diperbarui')
                        this.editOpen = false
                        this.fetchStocks(this.currentPage)
                    })
                    .catch(err => {
                        toast.error(err.message || 'Gagal update stock')
                    })
            },

            closeEditModal() {
                this.editOpen = false
                this.resetForm()
            },

            // =====================
            // DELETE
            // =====================
            deleteStock(id) {
                if (!confirm('Yakin ingin menghapus stock ini?')) return

                fetch(`/admin/stocks/${id}`, {
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
                        toast.success('Stock berhasil dihapus')
                        this.fetchStocks(this.currentPage)
                    })
                    .catch(err => {
                        toast.error(err.message || 'Gagal menghapus stock')
                    })
            },

            // =====================
            // UTILITIES
            // =====================
            resetForm() {
                this.form = {
                    id: null,
                    product_id: '',
                    lot_number: '',
                    expired: '',
                    qty: '',
                }
            },

            // =====================
            // PAGINATION
            // =====================
            nextPage() {
                if (this.currentPage < this.totalPages) {
                    this.fetchStocks(this.currentPage + 1)
                }
            },

            prevPage() {
                if (this.currentPage > 1) {
                    this.fetchStocks(this.currentPage - 1)
                }
            },

            goToPage(page) {
                if (page !== '...') {
                    this.fetchStocks(page)
                }
            },
        }
    }
</script>
