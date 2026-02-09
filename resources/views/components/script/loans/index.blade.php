<script>
    function loanModal() {
        return {
            // =====================
            // STATE
            // =====================
            loanOpen: false,

            teachers: [],
            teacherQuery: '',
            selectedTeacher: null,
            showTeacherDropdown: false,

            form: {
                item_id: '{{ $item->id }}',
                teacher_id: null,
                quantity: 1,
                location: '',
                loan_date: '',
            },

            // =====================
            // INIT
            // =====================
            init() {
                this.fetchTeachers()
            },

            // =====================
            // FETCH TEACHERS
            // =====================
            fetchTeachers() {
                fetch('/admin/teachers', {
                        headers: { Accept: 'application/json' }
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.teachers = data.data ?? data
                    })
                    .catch(() => {
                        toast.error('Gagal memuat data guru')
                    })
            },

            // =====================
            // FILTER TEACHERS
            // =====================
            get filteredTeachers() {
                if (!this.teacherQuery.trim()) return []

                return this.teachers.filter(t =>
                    t.name.toLowerCase().includes(this.teacherQuery.toLowerCase())
                )
            },

            selectTeacher(teacher) {
                this.selectedTeacher = teacher
                this.teacherQuery = teacher.name
                this.form.teacher_id = teacher.id
                this.showTeacherDropdown = false
            },

            // =====================
            // SUBMIT LOAN
            // =====================
            submitLoan() {
                if (!this.form.teacher_id) {
                    toast.error('Guru penanggung jawab wajib dipilih')
                    return
                }

                fetch('/admin/loans', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            Accept: 'application/json',
                        },
                        body: JSON.stringify(this.form),
                    })
                    .then(async res => {
                        const data = await res.json()
                        if (!res.ok) throw data
                        return data
                    })
                    .then(() => {
                        toast.success('Peminjaman berhasil dicatat')
                        this.loanOpen = false
                        this.resetForm()
                        location.reload()
                    })
                    .catch(err => {
                        toast.error(err.message || 'Gagal menyimpan peminjaman')
                    })
            },

            // =====================
            // UTIL
            // =====================
            resetForm() {
                this.teacherQuery = ''
                this.selectedTeacher = null
                this.form = {
                    item_id: '{{ $item->id }}',
                    teacher_id: null,
                    quantity: 1,
                    location: '',
                    loan_date: '',
                }
            },
        }
    }
</script>
