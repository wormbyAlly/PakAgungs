<script>
function customerTable() {
    return {
        customers: [],
        search: '',
        currentPage: 1,
        totalPages: 1,

        init() {
            this.fetchCustomers();

            this.$watch('search', () => {
                this.currentPage = 1;
                this.fetchCustomers();
            });
        },

        fetchCustomers(page = 1) {
            this.currentPage = page;

            const params = new URLSearchParams({
                page,
                search: this.search.trim()
            });

            fetch(`/admin/customers?${params}`, {
                headers: { Accept: 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                this.customers = data.data;
                this.currentPage = data.current_page;
                this.totalPages = data.last_page;
            })
            .catch(() => toast.error('Gagal memuat customer'));
        },
        

        nextPage() {
            if (this.currentPage < this.totalPages) {
                this.fetchCustomers(this.currentPage + 1);
            }
        },

        prevPage() {
            if (this.currentPage > 1) {
                this.fetchCustomers(this.currentPage - 1);
            }
        },

        goToPage(page) {
            if (page !== '...') {
                this.fetchCustomers(page);
            }
        }
    }
}
</script>
