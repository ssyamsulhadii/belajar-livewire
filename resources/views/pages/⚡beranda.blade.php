<?php

use Livewire\Component;

new class extends Component {
    public $total_instansi;
    public $total_divisi;
    public $total_pegawai;

    public function mount()
    {
        $this->total_instansi = \App\Models\Instansi::count();
        $this->total_divisi = \App\Models\Divisi::count();
        $this->total_pegawai = \App\Models\Pegawai::count();
    }
};
?>

<div>
    <div class="container-fluid px-3 px-lg-4 py-4">
        <x-partials.display.page-title title="Tampil Data"></x-partials.display.page-title>
        <section class="row g-3 mt-1" aria-label="Dashboard metrics">
            <div class="col-12 col-sm-6 col-xl-3">
                <article class="metric-card metric-primary">
                    <div class="metric-top">
                        <span class="metric-label">Instansi</span>
                        <span class="metric-icon"><i class="bi bi-bank"></i></span>
                    </div>
                    <div class="fw-bold fs-1">{{ $total_instansi }}</div>
                </article>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <article class="metric-card metric-success">
                    <div class="metric-top">
                        <span class="metric-label">Divisi</span>
                        <span class="metric-icon"><i class="bi bi-buildings"></i></span>
                    </div>
                    <div class="fw-bold fs-1">{{ $total_divisi }}</div>
                </article>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <article class="metric-card metric-warning">
                    <div class="metric-top">
                        <span class="metric-label">Pegawai</span>
                        <span class="metric-icon"><i class="bi bi-people" aria-hidden="true"></i></span>
                    </div>
                    <div class="fw-bold fs-1">{{ $total_pegawai }}</div>
                </article>
            </div>
        </section>
    </div>
</div>
