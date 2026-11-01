<div class="position-relative">
    <!-- Menggunakan type="text" agar kita bisa mengontrol tombol 'x' dan loading secara presisi -->
    <input class="form-control form-control-sm table-search pe-5" type="text" placeholder="Cari data..."
        wire:model.live="search" aria-label="Search Data">

    <!-- 1. TAMPIL SAAT TIDAK LOADING & ADA TEKS: Tombol 'x' buatan sendiri untuk menghapus pencarian -->
    @if (!empty($search))
        <button type="button" wire:click="$set('search', '')" wire:loading.remove wire:target="search"
            class="btn btn-link position-absolute top-50 end-0 translate-middle-y text-muted text-decoration-none p-0 me-3"
            style="font-size: 1rem; line-height: 1;" title="Bersihkan">
            <i class="bi bi-x-lg" style="-webkit-text-stroke: 2px currentColor;"></i>
        </button>
    @endif
    <!-- 2. TAMPIL SAAT PROSES PENCARIAN BERJALAN: Spinner loading menggantikan posisi tombol 'x' -->
    <div wire:loading wire:target="search" class="position-absolute top-50 end-0 translate-middle-y me-3 text-muted">
        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
    </div>
</div>
