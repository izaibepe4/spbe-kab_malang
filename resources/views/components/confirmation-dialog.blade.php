{{-- Confirmation Dialog untuk Heuristic 3 & 5: User Control & Error Prevention --}}
<div x-data="{
    show: false,
    title: 'Konfirmasi',
    message: 'Apakah Anda yakin?',
    confirmText: 'Ya',
    cancelText: 'Batal',
    onConfirm: null,
    open(title, msg, confirmCallback, confirmBtnText = 'Ya', cancelBtnText = 'Batal') {
        this.title = title;
        this.message = msg;
        this.confirmText = confirmBtnText;
        this.cancelText = cancelBtnText;
        this.onConfirm = confirmCallback;
        this.show = true;
    },
    confirm() {
        if (this.onConfirm) {
            this.onConfirm();
        }
        this.show = false;
    },
    cancel() {
        this.show = false;
    }
}"
x-init="
    window.addEventListener('confirm-dialog', (e) => {
        open(
            e.detail.title || 'Konfirmasi',
            e.detail.message || 'Apakah Anda yakin?',
            e.detail.onConfirm || (() => {}),
            e.detail.confirmText || 'Ya',
            e.detail.cancelText || 'Batal'
        );
    });
"
x-show="show"
x-cloak
x-transition:enter="transition ease-out duration-200"
x-transition:enter-start="opacity-0"
x-transition:enter-end="opacity-100"
x-transition:leave="transition ease-in duration-150"
x-transition:leave-start="opacity-100"
x-transition:leave-end="opacity-0"
class="fixed inset-0 z-[10001] flex items-center justify-center bg-black bg-opacity-50"
style="display: none;"
@click.self="cancel()">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 p-6"
        @click.stop>
        <h3 class="text-lg font-semibold text-[#102A66] mb-2" x-text="title"></h3>
        <p class="text-gray-600 mb-6" x-text="message"></p>
        <div class="flex gap-3 justify-end">
            <button @click="cancel()" class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors font-medium">
                <span x-text="cancelText"></span>
            </button>
            <button @click="confirm()" class="px-4 py-2 bg-[#FFA84A] hover:bg-[#FF9A1F] text-white rounded-lg transition-colors font-medium">
                <span x-text="confirmText"></span>
            </button>
        </div>
    </div>
</div>




