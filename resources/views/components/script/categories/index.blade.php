<script>
function categoryTable() {
    return {
        categories: [],
        search: '',
        currentPage: 1,
        totalPages: 1,

        createOpen: false,
        editOpen: false,

        form: {
            name: ''
        },

        editCategory: {
            id: null,
            name: ''
        },

        /* ================= CREATE ================= */
        submitCreate() {
            fetch('/admin/categories', {
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
                this.form.name = '';
                this.createOpen = false;
                this.fetchCategories(this.currentPage);
            })
            .catch(err => {
                toast.error(err.message || 'Gagal menambahkan kategori');
            });
        },

        /* ================= EDIT ================= */
        openEditModal(category) {
            this.editCategory = { ...category };
            this.editOpen = true;
        },

        closeEditModal() {
            this.editOpen = false;
            this.editCategory = {
                id: null,
                name: ''
            };
        },

        submitEdit() {
            fetch(`/admin/categories/${this.editCategory.id}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    name: this.editCategory.name
                }),
            })
            .then(async res => {
                const data = await res.json();
                if (!res.ok) throw data;
                return data;
            })
            .then(() => {
                toast.success('Kategori berhasil diperbarui');
                this.closeEditModal();
                this.fetchCategories(this.currentPage);
            })
            .catch(err => {
                toast.error(err.message || 'Gagal memperbarui kategori');
            });
        },

        /* ================= FETCH ================= */
        fetchCategories(page = 1) {
            this.currentPage = page;

            const params = new URLSearchParams({
                page,
                search: this.search.trim()
            });

            fetch(`/admin/categories?${params.toString()}`, {
                headers: { 'Accept': 'application/json' }
            })
            .then(async res => {
                const data = await res.json();
                if (!res.ok) throw data;
                return data;
            })
            .then(data => {
                this.categories = data.data;
                this.currentPage = data.current_page;
                this.totalPages = data.last_page;
            })
            .catch(() => {
                toast.error('Gagal memuat data kategori');
            });
        },

        /* ================= DELETE ================= */
        deleteCategory(categoryId) {
            if (!confirm('Yakin ingin menghapus kategori ini?')) return;

            fetch(`/admin/categories/${categoryId}`, {
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
                this.fetchCategories(this.currentPage);
            })
            .catch(err => {
                toast.error(err.message || 'Gagal menghapus kategori');
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
                this.fetchCategories(this.currentPage + 1);
            }
        },

        prevPage() {
            if (this.currentPage > 1) {
                this.fetchCategories(this.currentPage - 1);
            }
        },

        goToPage(page) {
            if (page !== '...') this.fetchCategories(page);
        },

        /* ================= INIT ================= */
        init() {
            this.fetchCategories();
            this.$watch('search', () => {
                this.currentPage = 1;
                this.fetchCategories();
            });
        }
    }
}
</script>
