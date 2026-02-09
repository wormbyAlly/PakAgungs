<script>
function teacherTable() {
    return {
        teachers: [],
        search: '',
        currentPage: 1,
        totalPages: 1,

        createOpen: false,
        editOpen: false,

        form: {
            name: ''
        },

        editTeacher: {
            id: null,
            name: ''
        },

        /* ================= CREATE ================= */
        submitCreateTeacher() {
            fetch('/admin/teachers', {
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
                this.fetchTeachers(this.currentPage);
            })
            
        this.createOpen = false;


            .catch(err => {
                toast.error(err.message || 'Gagal menambahkan guru');
            });
        },

        /* ================= EDIT ================= */
        openEditModal(teacher) {
            this.editTeacher = { ...teacher };
            this.editOpen = true;
        },

        closeEditModal() {
            this.editOpen = false;
            this.editTeacher = {
                id: null,
                name: ''
            };
        },

        submitEditTeacher() {
            fetch(`/admin/teachers/${this.editTeacher.id}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    name: this.editTeacher.name
                }),
            })
            .then(async res => {
                const data = await res.json();
                if (!res.ok) throw data;
                return data;
            })
            .then(() => {
                toast.success('Guru berhasil diperbarui');
                this.closeEditModal();
                this.fetchTeachers(this.currentPage);
            })
            .catch(err => {
                toast.error(err.message || 'Gagal memperbarui guru');
            });
        },

        /* ================= FETCH ================= */
        fetchTeachers(page = 1) {
            this.currentPage = page;

            const params = new URLSearchParams({
                page,
                search: this.search.trim()
            });

            fetch(`/admin/teachers?${params.toString()}`, {
                headers: { 'Accept': 'application/json' }
            })
            .then(async res => {
                const data = await res.json();
                if (!res.ok) throw data;
                return data;
            })
            .then(data => {
                this.teachers = data.data;
                this.currentPage = data.current_page;
                this.totalPages = data.last_page;
            })
            .catch(() => {
                toast.error('Gagal memuat data guru');
            });
        },

        /* ================= DELETE ================= */
        deleteTeacher(teacherId) {
            if (!confirm('Yakin ingin menghapus guru ini?')) return;

            fetch(`/admin/teachers/${teacherId}`, {
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
                this.fetchTeachers(this.currentPage);
            })
            .catch(err => {
                toast.error(err.message || 'Gagal menghapus guru');
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
                this.fetchTeachers(this.currentPage + 1);
            }
        },

        prevPage() {
            if (this.currentPage > 1) {
                this.fetchTeachers(this.currentPage - 1);
            }
        },

        goToPage(page) {
            if (page !== '...') this.fetchTeachers(page);
        },

        /* ================= INIT ================= */
        init() {
            this.fetchTeachers();
            this.$watch('search', () => {
                this.currentPage = 1;
                this.fetchTeachers();
            });
        }
    }
}
</script>
