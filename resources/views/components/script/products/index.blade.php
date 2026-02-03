<script>
    function productTable() {
        return {
            products: [],
            search: '',
            currentPage: 1,
            totalPages: 1,

            createOpen: false,
            editOpen: false,
            editProduct: {
                id: null,
                name: '',
                price: '',
                code: ''
            },
            form: {
                code: '',
                name: '',
                price: ''
            },

            formatPrice(value) {
                return new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(value);
            },



            /* ================= CREATE ================= */
            submitCreate() {
                fetch('/admin/products', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(this.form),
                    })
                    .then(async res => {
                        const data = await res.json();
                        if (!res.ok) throw data;
                        return data;
                    })
                    .then(data => {
                        toast.success(data.message);
                        this.form = {
                            code: '',
                            name: '',
                            price: ''
                        };
                        this.fetchProducts(this.currentPage);
                        this.open = false;

                    })
                    .catch(err => {
                        toast.error(err.message || 'Gagal menambahkan produk');
                    });
            },

            /* ================= EDIT ================= */
            openEditModal(product) {
                this.editProduct = {
                    ...product
                };
                this.editOpen = true;
            },

            closeEditModal() {
                this.editOpen = false;
                this.editProduct = {
                    id: null,
                    code: '',
                    name: '',
                    price: ''
                };
            },

            submitEdit() {
                fetch(`/admin/products/${this.editProduct.id}`, {
                        method: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(this.editProduct),
                    })
                    .then(async res => {
                        const data = await res.json();
                        if (!res.ok) throw data;
                        return data;
                    })
                    .then(() => {
                        toast.success('Produk berhasil diperbarui');
                        this.closeEditModal();
                        this.fetchProducts(this.currentPage);
                    })
                    .catch(err => {
                        toast.error(err.message || 'Gagal memperbarui produk');
                    });
            },

            /* ================= FETCH ================= */
            fetchProducts(page = 1) {
                this.currentPage = page;

                const params = new URLSearchParams({
                    page,
                    search: this.search.trim()
                });

                fetch(`/admin/products?${params.toString()}`, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    })
                    .then(async res => {
                        const data = await res.json();
                        if (!res.ok) throw data;
                        return data;
                    })
                    .then(data => {
                        this.products = data.data;
                        this.currentPage = data.current_page;
                        this.totalPages = data.last_page;
                    })
                    .catch(() => {
                        toast.error('Gagal memuat data produk');
                    });
            },

            /* ================= DELETE ================= */
            deleteProduct(productId) {
                if (!confirm('Yakin ingin menghapus produk ini?')) return;

                fetch(`/admin/products/${productId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(async res => {
                        const data = await res.json();
                        if (!res.ok) throw data;
                        return data;
                    })
                    .then(data => {
                        toast.success(data.message);
                        this.fetchProducts(this.currentPage);
                    })
                    .catch(err => {
                        toast.error(err.message || 'Gagal menghapus produk');
                    });
            },

            /* ================= PAGINATION ================= */
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

            nextPage() {
                if (this.currentPage < this.totalPages) {
                    this.fetchProducts(this.currentPage + 1);
                }
            },

            prevPage() {
                if (this.currentPage > 1) {
                    this.fetchProducts(this.currentPage - 1);
                }
            },

            goToPage(page) {
                if (page !== '...') this.fetchProducts(page);
            },

            /* ================= INIT ================= */
            init() {
                this.fetchProducts();
                this.$watch('search', () => {
                    this.currentPage = 1;
                    this.fetchProducts();
                });
            }
        }
    }
</script>
