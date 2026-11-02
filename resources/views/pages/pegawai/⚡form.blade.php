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
    // Properti terpisah khusus untuk menampung NIP yang mau dicari/ditarik
    public $search_nip = '';
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
            $this->form['tanggal_lahir'] = $pegawai->tanggal_lahir->isoFormat('YYYY-MM-DD');

            // Simpan file lama jika diperlukan untuk pengecekan saat update
            $this->old_foto = $pegawai->foto;
            $this->old_cv = $pegawai->cv;
        }
        $this->list_divisi = Divisi::all();
        $this->pangkat_golongan = $this->callData('pil_pangkat_golongan');
    }

    public function nextStep()
    {
        // 1. Validasi data teks di step 1
        $validated = $this->validate([
            'form.nama' => 'required|string|max:255',
            'form.nip' => 'required|size:18|unique:pegawai,nip,' . ($this->pegawai?->id ?? 'NULL'),
            'form.tanggal_lahir' => 'required|date',
            'form.pangkat_golongan' => 'required|string',
            'form.jabatan' => 'required|string|max:255',
            'form.divisi_id' => 'required|exists:divisi,id',
            'form.alamat' => 'required|string',
            'form.status' => 'required|boolean',
        ]);
        // 2. Simpan/Update data step 1 ke database sebagai draft
        // updateOrCreate akan mengecek apakah $this->pegawai sudah punya ID atau belum
        $this->pegawai = Pegawai::updateOrCreate(['id' => $this->pegawai?->id], $validated['form']);
        $this->old_foto = $this->pegawai->foto;
        $this->old_cv = $this->pegawai->cv;
        $this->step = 2;
        // $this->reset(['search_nip']);
    }
    public function save()
    {
        return to_route('pegawai.index')->with('success', 'Data pegawai berhasil disimpan!');
    }

    // Lifecycle hook utama Livewire untuk mendeteksi perubahan input
    public function updated($propertyName, $value)
    {
        // 1. Cek untuk form.foto
        if ($propertyName === 'form.foto' && $value) {
            $this->uploadFile(file: $value, validationRule: 'image|max:1200', storageFolder: 'upload/foto', dbColumn: 'foto', oldFileVariable: $this->old_foto, sessionKey: 'success_foto', successMessage: 'Foto berhasil tersimpan.');
        }

        // 2. Contoh jika nanti Anda ingin menambahkan form.cv (Dokumen PDF)
        if ($propertyName === 'form.cv' && $value) {
            $this->uploadFile(file: $value, validationRule: 'mimes:pdf|max:2048', storageFolder: 'upload/cv', dbColumn: 'cv', oldFileVariable: $this->old_cv, sessionKey: 'success_cv', successMessage: 'CV berhasil tersimpan.');
        }
    }

    /**
     * Helper function privat untuk menghandle upload, validasi, dan hapus file lama secara dinamis.
     */
    private function uploadFile($file, string $validationRule, string $storageFolder, string $dbColumn, &$oldFileVariable, string $sessionKey, string $successMessage)
    {
        // 1. Validasi file secara dinamis
        $this->validate(["form.{$dbColumn}" => $validationRule]);
        if ($file) {
            // 2. Hapus file fisik lama di storage jika sebelumnya sudah ada
            if ($oldFileVariable) {
                Storage::disk('public')->delete($storageFolder . '/' . $oldFileVariable);
            }
            // 3. Buat nama file unik menggunakan hashName
            $fileName = $file->hashName();
            // 4. Simpan file fisik ke folder tujuan
            $file->storeAs($storageFolder, $fileName, 'public');
            // 5. Update database record pegawai yang aktif
            if ($this->pegawai) {
                $this->pegawai->update([$dbColumn => $fileName]);
            }
            // 6. Perbarui variabel referensi file lama (agar tombol preview langsung mengarah ke file baru)
            $oldFileVariable = $fileName;
            // 7. Berikan session flash sukses
            session()->flash($sessionKey, $successMessage);
        }
    }

    public function fetchByNip()
    {
        $this->validate(
            ['search_nip' => 'required|size:18'],
            [
                'search_nip.required' => 'Kolom NIP pencarian wajib diisi.',
                'search_nip.size' => 'NIP harus tepat 18 digit.',
            ],
        );
        $existingPegawai = Pegawai::firstWhere('nip', $this->search_nip);
        if ($existingPegawai) {
            // Masukkan data lama ke form utama, termasuk NIP-nya
            $this->form['nip'] = $existingPegawai->nip;
            $this->form['nama'] = $existingPegawai->nama;
            $this->form['tanggal_lahir'] = $existingPegawai->tanggal_lahir->isoFormat('YYYY-MM-DD'); // Format tanggal lahir ke format Y-m-d
            $this->form['pangkat_golongan'] = $existingPegawai->pangkat_golongan;
            $this->form['jabatan'] = $existingPegawai->jabatan;
            $this->form['divisi_id'] = $existingPegawai->divisi_id;
            $this->form['alamat'] = $existingPegawai->alamat;
            $this->form['status'] = $existingPegawai->status;
            // Simpan referensi pegawai untuk mengabaikan validasi unique NIP
            $this->pegawai = $existingPegawai;
            session()->flash('success_nip', 'Data berhasil ditarik dari NIP yang pernah terdaftar!');
        } else {
            session()->flash('error_nip', 'NIP tidak ditemukan dalam database.');
        }
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

    protected function validationAttributes()
    {
        return [
            'form.nama' => 'Nama',
            'form.nip' => 'NIP',
            'form.tanggal_lahir' => 'Tanggal Lahir',
            'form.pangkat_golongan' => 'Pangkat/Golongan',
            'form.jabatan' => 'Jabatan',
            'form.divisi_id' => 'Divisi',
            'form.alamat' => 'Alamat',
            'form.status' => 'Status',
            'form.foto' => 'Foto',
            'form.cv' => 'CV',
        ];
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
                            <!-- FITUR KHUSUS CREATE UNTUK MAPPING DATA BY NIP -->
                            <div class="col-12">
                                <label class="form-label fw-bold text-primary">Tarik Data Berdasarkan
                                    NIP Jika sebelumnya pernah terdaftar</label>
                                <div class="input-group">
                                    <input type="number" wire:model="search_nip" class="form-control"
                                        placeholder="Masukkan 18 digit NIP..." maxlength="18">
                                    <button type="button" wire:click="fetchByNip" class="btn btn-outline-primary"
                                        wire:loading.attr="disabled">
                                        <span wire:loading.remove wire:target="fetchByNip">
                                            <i class="bi bi-cloud-download"></i> Tarik Data
                                        </span>
                                        <span wire:loading wire:target="fetchByNip">
                                            <span class="spinner-border spinner-border-sm" role="status"
                                                aria-hidden="true"></span> Memuat...
                                        </span>
                                    </button>
                                </div>
                                @error('search_nip')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror

                                @if (session()->has('success_nip'))
                                    <div class="text-success small mt-1"><i class="bi bi-check-circle"></i>
                                        {{ session('success_nip') }}</div>
                                @endif
                                @if (session()->has('error_nip'))
                                    <div class="text-danger small mt-1"><i class="bi bi-exclamation-circle"></i>
                                        {{ session('error_nip') }}</div>
                                @endif
                                <hr class="my-3 text-muted">
                            </div>

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
                        <div class="row g-4 mt-2">
                            <x-partials.forms.input-file name="form.foto" label="Pas Foto" accept="image/*"
                                maxSize="1.2MB" formatDesc="jpeg/jpg/png" :oldFile="$old_foto" folderPath="upload/foto"
                                icon="bi-file-earmark-image" />

                            <x-partials.forms.input-file name="form.cv" label="Curriculum Vitae" accept=".pdf"
                                maxSize="1.2MB" formatDesc="PDF" :oldFile="$old_cv" folderPath="upload/cv"
                                icon="bi-file-earmark-pdf" />
                        </div>


                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <button type="button" wire:click="$set('step', 1)" class="btn btn-outline-dark">
                                &larr; Kembali ke Data Utama
                            </button>
                            <x-partials.forms.submit target="save" :is-update="(bool) false" />
                        </div>
                    </form>
                @endif
            </div>
        </section>
    </div>
</div>
