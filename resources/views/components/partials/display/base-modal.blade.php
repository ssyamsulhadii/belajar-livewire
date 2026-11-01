@props([
    'id' => 'customModal',
    'title' => 'Modal Title',
    'maxWidth' => 'modal-md',
])

<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog {{ $maxWidth }}">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title">{{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                {{ $slot }}
            </div>

            <!-- Modal Footer -->
            @isset($footer)
                <div class="modal-footer">
                    {{ $footer }}
                </div>
            @endisset

        </div>
    </div>
</div>

<!-- Skrip Inisialisasi Modal yang Aman & Dinamis -->
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modalElement = document.getElementById('{{ $id }}');
            if (modalElement) {
                const modalInstance = new bootstrap.Modal(modalElement);

                // Mendengarkan event buka modal spesifik berdasarkan ID
                Livewire.on('open-modal-{{ $id }}', () => {
                    modalInstance.show();
                });

                // Mendengarkan event tutup modal spesifik berdasarkan ID
                Livewire.on('close-modal-{{ $id }}', () => {
                    modalInstance.hide();
                });
            }
        });
    </script>
@endpush
