<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Dashboard' }} | TailAdmin - Laravel Tailwind CSS Admin Dashboard Template</title>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    {{-- <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script> --}}

    <!-- Theme Store -->
    <script>
        window.toast = {
            show({
                variant = 'info',
                title = '',
                message = '',
                timeout = 3500
            }) {
                const container = document.getElementById('toast-container');
                if (!container) return;

                const variants = {
                    success: {
                        border: 'border-green-500 bg-green-50 dark:border-green-500/30 dark:bg-green-500/15',
                        icon: 'text-green-500',
                        svg: `✓`
                    },
                    error: {
                        border: 'border-red-500 bg-red-50 dark:border-red-500/30 dark:bg-red-500/15',
                        icon: 'text-red-500',
                        svg: `!`
                    },
                    warning: {
                        border: 'border-yellow-500 bg-yellow-50 dark:border-yellow-500/30 dark:bg-yellow-500/15',
                        icon: 'text-yellow-500',
                        svg: `!`
                    },
                    info: {
                        border: 'border-blue-500 bg-blue-50 dark:border-blue-500/30 dark:bg-blue-500/15',
                        icon: 'text-blue-500',
                        svg: `i`
                    },
                };

                const v = variants[variant] ?? variants.info;

                const toast = document.createElement('div');
                toast.className = `
            rounded-xl border p-4 shadow-md
            transition-all duration-300 opacity-0 translate-x-5
            ${v.border}
        `;

                toast.innerHTML = `
            <div class="flex items-start gap-3">
                <div class="-mt-0.5 ${v.icon} font-bold text-lg">
                    ${v.svg}
                </div>
                <div class="flex-1">
                    ${title ? `<h4 class="mb-1 text-sm font-semibold text-gray-800 dark:text-white/90">${title}</h4>` : ''}
                    ${message ? `<p class="text-sm text-gray-500 dark:text-gray-400">${message}</p>` : ''}
                </div>
            </div>
        `;

                container.appendChild(toast);

                requestAnimationFrame(() => {
                    toast.classList.remove('opacity-0', 'translate-x-5');
                });

                setTimeout(() => {
                    toast.classList.add('opacity-0', 'translate-x-5');
                    setTimeout(() => toast.remove(), 300);
                }, timeout);
            },

            success(message, title = 'Berhasil') {
                this.show({
                    variant: 'success',
                    title,
                    message
                });
            },

            error(message, title = 'Gagal') {
                this.show({
                    variant: 'error',
                    title,
                    message
                });
            },

            warning(message, title = 'Peringatan') {
                this.show({
                    variant: 'warning',
                    title,
                    message
                });
            },

            info(message, title = '') {
                this.show({
                    variant: 'info',
                    title,
                    message
                });
            }
        };

        document.addEventListener('alpine:init', () => {
            Alpine.store('theme', {
                init() {
                    const savedTheme = localStorage.getItem('theme');
                    const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' :
                        'light';
                    this.theme = savedTheme || systemTheme;
                    this.updateTheme();
                },
                theme: 'light',
                toggle() {
                    this.theme = this.theme === 'light' ? 'dark' : 'light';
                    localStorage.setItem('theme', this.theme);
                    this.updateTheme();
                },
                updateTheme() {
                    const html = document.documentElement;
                    const body = document.body;
                    if (this.theme === 'dark') {
                        html.classList.add('dark');
                        body.classList.add('dark', 'bg-gray-900');
                    } else {
                        html.classList.remove('dark');
                        body.classList.remove('dark', 'bg-gray-900');
                    }
                }
            });

            Alpine.store('sidebar', {
                // Initialize based on screen size
                isExpanded: window.innerWidth >= 1280, // true for desktop, false for mobile
                isMobileOpen: false,
                isHovered: false,

                toggleExpanded() {
                    this.isExpanded = !this.isExpanded;
                    // When toggling desktop sidebar, ensure mobile menu is closed
                    this.isMobileOpen = false;
                },

                toggleMobileOpen() {
                    this.isMobileOpen = !this.isMobileOpen;
                    // Don't modify isExpanded when toggling mobile menu
                },

                setMobileOpen(val) {
                    this.isMobileOpen = val;
                },

                setHovered(val) {
                    // Only allow hover effects on desktop when sidebar is collapsed
                    if (window.innerWidth >= 1280 && !this.isExpanded) {
                        this.isHovered = val;
                    }
                }
            });
        });
    </script>

    <!-- Apply dark mode immediately to prevent flash -->
  <script>
    (function() {
        const savedTheme = localStorage.getItem('theme');
        const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        const theme = savedTheme || systemTheme;

        if (theme === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    })();
</script>


</head>

<body x-data="{ 'loaded': true }" x-init="$store.sidebar.isExpanded = window.innerWidth >= 1280;
const checkMobile = () => {
    if (window.innerWidth < 1280) {
        $store.sidebar.setMobileOpen(false);
        $store.sidebar.isExpanded = false;
    } else {
        $store.sidebar.isMobileOpen = false;
        $store.sidebar.isExpanded = true;
    }
};
window.addEventListener('resize', checkMobile);">

    {{-- preloader --}}
    <x-common.preloader />
    {{-- preloader end --}}

    <div class="min-h-screen xl:flex">
        @include('layouts.backdrop')
        @include('layouts.sidebar')

        <div class="flex-1 transition-all duration-300 ease-in-out"
            :class="{
                'xl:ml-[290px]': $store.sidebar.isExpanded || $store.sidebar.isHovered,
                'xl:ml-[90px]': !$store.sidebar.isExpanded && !$store.sidebar.isHovered,
                'ml-0': $store.sidebar.isMobileOpen
            }">
            <!-- app header start -->
            @include('layouts.app-header')
            <!-- app header end -->
            @if (session('info'))
                <div id="flash-info" class="text-white px-4 py-2 rounded mb-4 relative flex justify-center"
                    style="background-color: rgba(70, 95, 255, 1);">
                    {{ session('info') }}
                </div>
            @endif

            <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
                @yield('content')
            </div>
        </div>

    </div>

    <script>
        const flash = document.getElementById('flash-info');
        if (flash) {
            setTimeout(() => {
                flash.style.transition = "opacity 0.5s";
                flash.style.opacity = 0;

                setTimeout(() => flash.remove(), 500);
            }, 2500); // tampil 2.5 detik
        }
    </script>
    <div id="toast-container"
        class="fixed top-[72px] left-1/2 -translate-x-1/2 z-[9999]
            space-y-3 w-[360px] pointer-events-none">
    </div>

</body>

@stack('scripts')

</html>
