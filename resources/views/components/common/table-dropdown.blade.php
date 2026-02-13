<div 
    x-data="{
        isOpen: false,
        popperInstance: null,
        init() {
            this.$nextTick(() => {
                this.popperInstance = createPopper(this.$refs.button, this.$refs.content, {
                    placement: 'bottom-end',
                    strategy: 'fixed',
                    modifiers: [
                        {
                            name: 'offset',
                            options: { offset: [0, 6] },
                        },
                        {
                            name: 'preventOverflow',
                            options: { boundary: 'viewport' },
                        },
                    ],
                });
            });

            // 🔥 LISTEN GLOBAL CLOSE EVENT
            window.addEventListener('close-dropdowns', () => {
                this.isOpen = false;
            });
        },
        toggle() {
            this.isOpen = !this.isOpen;
            if (this.popperInstance) {
                this.popperInstance.update();
            }
        }
    }"
    @click.away="isOpen = false"
>

    {{-- BUTTON --}}
    <div @click="toggle()" x-ref="button" class="cursor-pointer">
        {{ $button }}
    </div>

    {{-- DROPDOWN --}}
    <div 
        x-ref="content"
        x-show="isOpen"
        x-cloak
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="fixed z-[99999]"
    >
        <div class="p-2 bg-white border border-gray-200 rounded-2xl shadow-xl 
                    dark:border-gray-800 dark:bg-gray-900 w-40">
            <div class="space-y-1" role="menu">
                {{ $content }}
            </div>
        </div>
    </div>
</div>
