<script>
    function penjualan() {
        return {
            search: '',
            products: [],
            cart: [],
            subtotal: 0,

            showInvoiceModal: false,
            invoiceNumber: null,
            saleId: null,

            loadingSearch: false,

            /* =====================
             * INIT
             * ===================== */
            init() {
                this.loadCart()

                this.$watch('search', (value) => {
                    if (value.length >= 2) {
                        this.fetchProducts()
                    } else {
                        this.products = []
                    }
                })
            },

            /* =====================
             * LOAD CART
             * ===================== */
            loadCart() {
                fetch('{{ route('cart.current') }}')
                    .then(r => r.json())
                    .then(data => {
                        this.cart = data.items
                        this.subtotal = data.subtotal
                    })
            },

            handleProductClick(product) {
                if (product.out_of_stock) {
                    toast.error('Produk tidak memiliki stok tersedia')
                    return
                }

                this.addToCart(product)
                this.products = []
                this.search = ''
            },


            /* =====================
             * SEARCH PRODUCT
             * ===================== */
            fetchProducts() {
                if (this.search.length < 2) {
                    this.products = []
                    return
                }

                this.loadingSearch = true

                fetch(`/products/search?search=${encodeURIComponent(this.search)}`)
                    .then(async res => {
                        const data = await res.json()
                        if (!res.ok) throw data
                        return data
                    })
                    .then(list => {
                        this.products = list.map(p => ({
                            id: p.id,
                            name: p.name,
                            price: p.price,
                            available_stock: Number(p.available_stock),
                            out_of_stock: Number(p.available_stock) <= 0,
                            qty: 1
                        }))
                    })
                    .catch(() => {
                        this.products = []
                    })
                    .finally(() => {
                        this.loadingSearch = false
                    })
            },
            /* =====================
             * ADD TO CART
             * ===================== */
            addToCart(product) {
                fetch('{{ route('cart.add') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document
                                .querySelector('meta[name=csrf-token]').content
                        },
                        body: JSON.stringify({
                            product_id: product.id,
                            qty: 1
                        })
                    })
                    .then(async r => {
                        const data = await r.json()
                        if (!r.ok) throw data
                        return data
                    })
                    .then(data => {
                        this.cart = data.items
                        this.subtotal = data.subtotal

                        // 🔥 reset search & dropdown
                        this.search = ''
                        this.products = []
                    })
                    .catch(err => {
                        toast.error(err.message ?? 'Gagal menambahkan produk')
                    })
            },

            /* =====================
             * UPDATE QTY
             * ===================== */
            updateQty(item, qty) {
                fetch('{{ route('cart.update') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document
                                .querySelector('meta[name=csrf-token]').content
                        },
                        body: JSON.stringify({
                            product_id: item.product_id,
                            qty: qty
                        })
                    })
                    .then(async r => {
                        const data = await r.json()
                        if (!r.ok) throw data
                        return data
                    })
                    .then(data => {
                        this.cart = data.items
                        this.subtotal = data.subtotal
                    })
                    .catch(err => {
                        toast.error(err.message ?? 'Qty melebihi stok')
                        this.loadCart()
                    })
            },

            /* =====================
             * REMOVE ITEM
             * ===================== */
            removeItem(item) {
                fetch('{{ route('cart.remove') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document
                                .querySelector('meta[name=csrf-token]').content
                        },
                        body: JSON.stringify({
                            product_id: item.product_id
                        })
                    })
                    .then(r => r.json())
                    .then(data => {
                        toast.success('Produk berhasil dihapus');
                        this.cart = data.items
                        this.subtotal = data.subtotal
                    })
            },

            /* =====================
             * UTILITIES
             * ===================== */
            format(n) {
                return new Intl.NumberFormat('id-ID').format(n)
            },

            /* =====================
             * CHECKOUT
             * ===================== */

            checkout() {
                fetch('{{ route('checkout.process') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document
                                .querySelector('meta[name=csrf-token]').content
                        }
                    })
                    .then(async r => {
                        const data = await r.json()
                        if (!r.ok) throw data
                        return data
                    })
                    .then(res => {
                        this.invoiceNumber = res.invoice_number
                        this.showInvoicePopup = true
                        this.cart = []
                        this.subtotal = 0
                    })
                    .catch(err => {
                        alert(err.message ?? 'Checkout gagal')
                    })
            },
        }
    }
</script>
