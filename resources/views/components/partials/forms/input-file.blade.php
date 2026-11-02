@props([
    'name', // Nama properti wire:model, misal: "form.foto" atau "form.cv"
    'label', // Label input, misal: "Pas Foto" atau "Curriculum Vitae"
    'accept' => '', // Format yang diterima, misal: "image/*" atau ".pdf,.xlsx"
    'maxSize' => '1.2MB', // Keterangan teks ukuran maksimal
    'formatDesc' => '', // Keterangan format, misal: "JPG/PNG" atau "PDF"
    'oldFile' => null, // Variabel file lama untuk tombol preview ($old_foto / $old_cv)
    'folderPath' => '', // Folder penyimpanan di storage, misal: "upload/foto"
    'icon' => 'bi-file-earmark', // Ikon default Bootstrap Icons untuk tombol preview
])

<div class="col-12">
    <label class="form-label" for="{{ $attributes->get('id', md5($name)) }}">
        {{ $label }}
        @if ($maxSize || $formatDesc)
            <span
                class="small text-danger">({{ $maxSize ? 'Maks: ' . $maxSize : '' }}{{ $maxSize && $formatDesc ? ', ' : '' }}{{ $formatDesc ? 'Format: ' . $formatDesc : '' }})</span>
        @endif
    </label>

    <div class="input-group">
        <!-- Input File menggunakan wire:model.live -->
        <input type="file" wire:model.live="{{ $name }}" class="form-control"
            id="{{ $attributes->get('id', md5($name)) }}" accept="{{ $accept }}">

        <!-- Tombol Preview (Hanya muncul jika file lama/tersedia) -->
        @if ($oldFile)
            <a href="{{ asset('storage/' . $folderPath . '/' . $oldFile) }}" target="_blank"
                class="btn btn-warning text-dark fw-bold d-flex align-items-center">
                <i class="bi {{ $icon }} me-1"></i> Preview
            </a>
        @endif
    </div>

    <!-- Pesan Error Validasi -->
    @error($name)
        <span class="text-danger small d-block mt-1">{{ $message }}</span>
    @enderror

    <!-- Indikator Loading Saat File Sedang Diunggah -->
    <div wire:loading wire:target="{{ $name }}" class="text-primary small mt-1">
        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sedang mengunggah...
    </div>

    <!-- Indikator Berhasil Tersimpan Otomatis -->
    @if (session()->has('success_' . str_replace('form.', '', $name)))
        <div class="text-success small mt-1">
            <i class="bi bi-check-circle-fill"></i> {{ session('success_' . str_replace('form.', '', $name)) }}
        </div>
    @endif
</div>
