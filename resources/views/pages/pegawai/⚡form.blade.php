<?php

use App\Models\Pegawai;
use App\Models\Divisi;
use Livewire\WithFileUploads;
use Livewire\Component;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    use WithFileUploads;
    public ?Pegawai $pegawai = null;
    public $list_divisi;
    public $pangkat_golongan;
    // Properti form yang merepresentasikan kolom tabel
    public array $form = [
        'nama' => '',
        'nip' => '',
        'tanggal_lahir' => '',
        'pangkat_golongan' => '',
        'jabatan' => '',
        'divisi_id' => '',
        'alamat' => '',
        'status' => true,
        'foto' => '',
        'cv' => '',
    ];
    public $old_foto = null;
    public $old_cv = null;
    public int $step = 1;

    function mount(Pegawai $pegawai = null)
    {
        if ($pegawai && $pegawai->exists) {
            $this->pegawai = $pegawai;

            // Mengisi array form secara otomatis menggunakan method only() dari Eloquent
            $this->form = $pegawai->only(array_keys($this->form));

            // Simpan file lama jika diperlukan untuk pengecekan saat update
            $this->old_foto = $pegawai->foto;
            $this->old_cv = $pegawai->cv;
        }
        $this->list_divisi = Divisi::all();
        $this->pangkat_golongan = $this->callData('pil_pangkat_golongan');
    }

    public function nextStep()
    {
        // Pindah ke step berikutnya (Upload Dokumen)
        $this->step = 2;
        // $this->validate([
        //     'form.nama' => 'required|string|max:255',
        //     'form.nip' => 'required|size:18|unique:pegawai,nip,' . ($this->pegawai?->id ?? 'NULL'),
        //     'form.tanggal_lahir' => 'required|date',
        //     'form.pangkat_golongan' => 'required|string',
        //     'form.jabatan' => 'required|string|max:255',
        //     'form.divisi_id' => 'required|exists:divisi,id',
        //     'form.alamat' => 'required|string',
        //     'form.status' => 'required|boolean',
        //     'form.foto' => 'nullable|image|max:2048', // Max 2MB
        //     'form.cv' => 'nullable|mimes:pdf|max:5120', // Max 5MB PDF
        // ]);
    }

    private function callData(?string $key = null)
    {
        $toObject = fn(array $items) => collect($items)->map(
            fn($item) => (object) [
                'id' => $item['id'],
                'nama' => $item['nama'],
            ],
        );

        $data = [
            'pil_pangkat_golongan' => $toObject([
                // untuk PNS
                ['id' => 'Pembina Utama Madya - IV/d', 'nama' => 'Pembina Utama Madya - IV/d'],
                ['id' => 'Pembina Utama Muda - IV/c', 'nama' => 'Pembina Utama Muda - IV/c'],
                ['id' => 'Pembina Tingkat I - IV/b', 'nama' => 'Pembina Tingkat I - IV/b'],
                ['id' => 'Pembina - IV/a', 'nama' => 'Pembina - IV/a'],
                ['id' => 'Penata Tingkat I - III/d', 'nama' => 'Penata Tingkat I - III/d'],
                ['id' => 'Penata - III/c', 'nama' => 'Penata - III/c'],
                ['id' => 'Penata Muda Tingkat I - III/b', 'nama' => 'Penata Muda Tingkat I - III/b'],
                ['id' => 'Penata Muda - III/a', 'nama' => 'Penata Muda - III/a'],
                ['id' => 'Pengatur Tingkat I - II/d', 'nama' => 'Pengatur Tingkat I - II/d'],
                ['id' => 'Pengatur - II/c', 'nama' => 'Pengatur - II/c'],
                ['id' => 'Pengatur Muda Tingkat I - II/b', 'nama' => 'Pengatur Muda Tingkat I - II/b'],
                ['id' => 'Pengatur Muda - II/a', 'nama' => 'Pengatur Muda - II/a'],
            ]),
        ];

        // 👉 Jika parameter dikirim, kembalikan spesifik
        if ($key !== null) {
            return $data[$key] ?? collect();
        }

        // 👉 Jika tidak, kembalikan semua
        return $data;
    }
};
?>

<div>
    <div class="container-fluid px-3 px-lg-4 py-4">
        <x-partials.display.page-title
            title="{{ $pegawai ? 'Edit' : 'Tambah' }} Pegawai"></x-partials.display.page-title>
        <section class="row g-3">
            <div class="col-12 col-xl-7">
                <div class="panel-header">
                    <div>
                        <h2 class="h5 mb-1 section-title">
                            <i class="bi bi-ui-checks-grid" aria-hidden="true"></i>
                            <span>Form Pegawai</span>
                        </h2>
                    </div>
                </div>
                <!-- ================= STEP 1: DATA UTAMA ================= -->
                @if ($step == 1)
                    <div class="panel needs-validation">
                        <span class="mb-3 bg-warning h5 py-1 px-2 rounded">Data Utama</span>
                        <div class="row g-3 mt-1">
                            <x-partials.forms.input name="form.nama" label="Nama"
                                wire:model="form.nama"></x-partials.forms.input>
                            <x-partials.forms.input name="form.nip" label="NIP" wire:model="form.nip"
                                type="number"></x-partials.forms.input>
                            <x-partials.forms.input name="form.tanggal_lahir" label="Tanggal Lahir"
                                wire:model="form.tanggal_lahir" type="date"></x-partials.forms.input>
                            <x-partials.forms.input name="form.jabatan" label="Jabatan"
                                wire:model="form.jabatan"></x-partials.forms.input>
                            <x-partials.forms.select name="form.pangkat_golongan" label="Pangkat / Golongan"
                                placeholder="-- Pilih Pangkat / Golongan --" :options="$pangkat_golongan"
                                wire:model="form.pangkat_golongan" />
                            <x-partials.forms.select name="form.divisi_id" label="Divisi"
                                placeholder="-- Pilih Divisi --" :options="$list_divisi" wire:model="form.divisi_id" />

                            <x-partials.forms.text-area name="form.alamat" label="Alamat" wire:model="form.alamat"
                                rows="3"></x-partials.forms.text-area>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="{{ route('pegawai.index') }}" class="btn btn-secondary">Batal</a>
                            <button type="button" wire:click="nextStep" class="btn btn-primary">
                                Lanjut ke Unggah Dokumen &rarr;
                            </button>
                        </div>
                    </div>
                @endif
                <!-- ================= STEP 2: UPLOAD DOKUMEN ================= -->
                @if ($step == 2)
                    <form class="panel needs-validation" wire:submit="save" method="POST">
                        <span class="mb-3 bg-warning h5 py-1 px-2 rounded">Unggah Dokumen</span>
                        <div class="row g-3 mt-2">
                            <x-partials.forms.input name="form.foto" label="Foto" wire:model="form.foto"
                                type="file"></x-partials.forms.input>
                            <x-partials.forms.input name="form.cv" wire:model="form.cv" label="Curriculum Vitae"
                                type="file"></x-partials.forms.input>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <button type="button" wire:click="$set('step', 1)" class="btn btn-outline-dark">
                                &larr; Kembali ke Data Utama
                            </button>
                            <x-partials.forms.submit target="save" :is-update="(bool) $pegawai?->id" />
                        </div>
                    </form>
                @endif
            </div>
        </section>
    </div>
</div>
