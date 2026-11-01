@props(['name', 'label' => '', 'setcol' => 'col-12', 'value' => ''])

<div class="{{ $setcol }} mb-3">
    @if ($label)
        <label class="form-label">{{ $label }}</label>
    @endif

    <!-- Menggunakan atribut disabled agar nilainya TIDAK dikirim ke server oleh browser -->
    <input type="text" class="form-control @error($name) is-invalid @enderror" value="{{ $value }}" disabled>

    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
