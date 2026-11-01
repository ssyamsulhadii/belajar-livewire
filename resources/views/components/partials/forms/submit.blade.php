@props([
    'target' => 'save', // Target method Livewire untuk wire:target (default: 'save')
    'isUpdate' => false, // Status apakah ini mode update atau bukan
    // 'labelSave' => 'Save', // Teks kustom untuk simpan (opsional)
    // 'labelUpdate' => 'Update', // Teks kustom untuk update (opsional)
])

<button class="btn btn-primary" type="submit" wire:loading.attr="disabled" wire:target="{{ $target }}">
    <!-- Teks & Ikon saat kondisi normal / tidak loading -->
    <span wire:loading.remove wire:target="{{ $target }}">
        <i class="bi bi-send" aria-hidden="true"></i>
        {{ $isUpdate ? 'Update' : 'Save' }}
    </span>

    <!-- Teks & Spinner saat proses loading/simpan/update berjalan -->
    <span wire:loading wire:target="{{ $target }}">
        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
        {{ $isUpdate ? 'Memperbarui...' : 'Menyimpan...' }}
    </span>
</button>
