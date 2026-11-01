@php
    // Cek apakah session 'message' ada (bisa berupa string atau array [type, message])
    // Atau kita buat mendukung session umum seperti success, error, warning, info
    $alertTypes = ['success', 'danger', 'warning', 'info', 'primary', 'secondary'];

    $activeType = 'success'; // Default type
    $message = null;

    // 1. Cek session umum secara berurutan
    foreach ($alertTypes as $type) {
        if (session()->has($type)) {
            $activeType = $type === 'danger' ? 'danger' : $type; // Sesuaikan mapping jika session-nya 'error' tapi class-nya 'danger'
            $message = session($type);
            break;
        }
    }

    // 2. Cek jika menggunakan session standar 'message'
    if (!$message && session()->has('message')) {
        $message = session('message');
        // Anda bisa menentukan tipe default atau mengambil dari atribut type
        $activeType = $attributes->get('type', 'success');
    }

    // Icon dinamis berdasarkan tipe alert
    $iconMap = [
        'success' => 'bi-check-circle-fill',
        'danger' => 'bi-exclamation-triangle-fill',
        'warning' => 'bi-exclamation-circle-fill',
        'info' => 'bi-info-circle-fill',
    ];
    $icon = $iconMap[$activeType] ?? 'bi-info-circle-fill';
@endphp

@if ($message)
    <div class="alert alert-{{ $activeType }} alert-dismissible fade show" role="alert">
        <i class="bi {{ $icon }} me-2"></i> {{ $message }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
