<?php

use App\Models\Divisi;
use App\Models\Instansi;
use Livewire\Component;

new class extends Component {
    public $list_instansi;
    public $search = '';
    public ?int $divisi_id = null;
    public $selectedInstansi = null;
    public array $form = [
        'kode' => null,
        'nama' => null,
        'keterangan' => null,
        'instansi_id' => null,
    ];

    public function mount()
    {
        $this->loadInstansi();
    }

    public function loadInstansi()
    {
        $this->list_instansi = Instansi::query()
            ->when($this->search, function ($query) {
                $query->where('nama', 'like', '%' . $this->search . '%');
            })
            ->get();
    }

    public function createDivisi(Instansi $instansi)
    {
        $this->resetForm();
        $this->selectedInstansi = $instansi;
        $this->form['instansi_id'] = $instansi->id;
        $this->dispatch('open-modal-divisiModal');
    }

    public function editDivisi(Divisi $divisi)
    {
        $this->resetForm();
        $this->selectedInstansi = $divisi->instansi;
        $this->divisi_id = $divisi->id;
        $this->form = [
            'kode' => $divisi->kode,
            'nama' => $divisi->nama,
            'keterangan' => $divisi->keterangan,
            'instansi_id' => $divisi->instansi_id,
        ];
        $this->dispatch('open-modal-divisiModal');
    }

    public function detailDivisi(Instansi $instansi)
    {
        // Muat relasi divisi jika belum dimuat
        $this->selectedInstansi = $instansi->load('divisi');
        // Dispatch flag untuk membuka modal detail (misal id-nya 'detailModal')
        $this->dispatch('open-modal-detailModal');
    }

    public function updatedSearch()
    {
        $this->loadInstansi();
    }

    public function save()
    {
        // 1. Validasi input form
        $validated = $this->validate([
            'form.kode' => 'required|string|max:50',
            'form.nama' => 'required|string|max:255',
            'form.keterangan' => 'required|string',
            'form.instansi_id' => 'required|exists:instansi,id',
        ]);
        // 2. Cek apakah sedang mode Edit (berdasarkan divisi_id) atau Tambah Baru
        if ($this->divisi_id) {
            // Proses Update
            $divisi = Divisi::findOrFail($this->divisi_id);
            $divisi->update($validated['form']);
            session()->flash('message', 'Data divisi berhasil diperbarui!');
        } else {
            // Proses Create / Tambah Baru
            Divisi::create($validated['form']);
            session()->flash('message', 'Data divisi berhasil ditambahkan!');
        }

        // 3. Tutup modal, muat ulang data, dan bersihkan form
        $this->dispatch('close-modal-divisiModal');
    }

    public function delete(Divisi $divisi)
    {
        $divisi->delete();
        session()->flash('message', 'Data divisi berhasil dihapus!');
        $this->loadInstansi();
        $this->resetForm();
    }

    // Helper untuk mereset form dan ID
    public function resetForm()
    {
        $this->reset(['form', 'divisi_id', 'selectedInstansi']);
        $this->resetValidation();
        // Set kembali default jika diperlukan
    }

    protected function validationAttributes()
    {
        return [
            'form.kode' => 'Kode Divisi',
            'form.nama' => 'Nama Divisi',
            'form.keterangan' => 'Keterangan',
            'form.instansi_id' => 'Instansi',
        ];
    }
};
?>

<div>
    <div class="container-fluid px-3 px-lg-4 py-4">
        <x-partials.display.page-title title="Data Divisi"></x-partials.display.page-title>
        <section class="row g-3">
            <div class="col-12 col-xl-8">
                <section class="panel">
                    <div class="panel-header">
                        <div>
                            <h2 class="h5 mb-1 section-title"><i class="bi bi-table" aria-hidden="true"></i><span>List
                                    Divisi</span></h2>
                            <p class="text-muted mb-0">Daftar Divisi.</p>
                        </div>
                        <x-partials.forms.input-search />
                    </div>
                    <x-partials.feedback.alert />
                    <div class="table-responsive">
                        <table class="table align-middle mb-0" id="ordersTable" data-searchable-table="">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Instansi</th>
                                    <th class="text-center">Divisi</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($list_instansi as $instansi)
                                    <tr>
                                        <td class="fw-semibold">{{ $loop->iteration }}</td>
                                        <td>{{ $instansi->nama }}</td>
                                        <td style="min-width: 250px;">
                                            @forelse ($instansi->divisi as $divisi)
                                                <div
                                                    class="d-flex align-items-center justify-content-between bg-light p-2 rounded mb-2 border">
                                                    <!-- Tombol Delete / Hapus -->
                                                    <button class="btn btn-outline-danger btn-sm px-2 py-0"
                                                        style="font-size: 0.75rem;" type="button"
                                                        wire:click="delete({{ $divisi->id }})" title="Hapus Divisi">
                                                        <i class="bi bi-trash"></i> Hapus
                                                    </button>

                                                    <!-- Nama Divisi di tengah -->
                                                    <span class="fw-semibold text-dark px-2 text-center text-truncate"
                                                        style="max-width: 120px;" title="{{ $divisi->nama }}">
                                                        {{ $divisi->nama }}
                                                    </span>

                                                    <!-- Tombol Edit -->
                                                    <button class="btn btn-outline-secondary btn-sm px-2 py-0"
                                                        style="font-size: 0.75rem;" type="button"
                                                        wire:click="editDivisi({{ $divisi->id }})"
                                                        title="Edit Divisi">
                                                        <i class="bi bi-pencil"></i> Edit
                                                    </button>
                                                </div>
                                            @empty
                                                <div class="text-center py-1">
                                                    <span class="badge bg-warning text-dark">Data divisi tidak
                                                        tersedia</span>
                                                </div>
                                            @endforelse
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-primary btn-sm" type="button"
                                                wire:click="createDivisi({{ $instansi->id }})">Create</button>
                                            <button class="btn btn-info btn-sm" type="button"
                                                wire:click="detailDivisi({{ $instansi->id }})">Detail</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">
                                            <h5 class="text-center text-danger">Data instansi tidak ada</h5>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </section>
    </div>


    <!-- ================= MODAL FORM CRUD ================= -->
    <x-partials.display.base-modal id="divisiModal" title="Form Divisi">
        <form wire:submit="save">
            <div class="mb-3">
                <x-partials.forms.input-disabled name="instansi" label="Instansi" :value="$selectedInstansi->nama ?? ''" />
                <x-partials.forms.input wire:model="form.kode" name="form.kode"
                    label="Kode Divisi"></x-partials.forms.input>
                <x-partials.forms.input wire:model="form.nama" name="form.nama"
                    label="Nama Divisi"></x-partials.forms.input>
                <x-partials.forms.text-area name="form.keterangan" label="Keterangan"
                    rows="3"></x-partials.forms.text-area>
                <input type="hidden" wire:model="form.instansi_id">
            </div>

            <div class="modal-footer px-0 pb-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <x-partials.forms.submit target="save" :is-update="(bool) $divisi_id" />
            </div>
        </form>
    </x-partials.display.base-modal>
    <!-- ================= END MODAL FORM CRUD ================= -->

    <!-- ================= MODAL DETAIL DIVISI ================= -->
    <x-partials.display.base-modal id="detailModal" title="Detail Divisi Instansi" maxWidth="modal-lg">
        <div class="container-fluid px-0">
            @if ($selectedInstansi)
                <!-- Info Instansi Utama -->
                <div class="mb-3">
                    <h6 class="text-muted mb-1">Instansi:</h6>
                    <h4 class="fw-bold text-primary">{{ $selectedInstansi->nama }}</h4>
                </div>

                <hr class="my-3">

                <h6 class="fw-semibold mb-3">Daftar Divisi Terkait:</h6>

                <!-- Daftar Card Divisi -->
                <div class="row g-3">
                    @forelse ($selectedInstansi->divisi as $divisi)
                        <div class="col-md-6">
                            <div class="card border shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h5 class="card-title text-dark fw-bold mb-0">{{ $divisi->nama }}</h5>
                                        <span class="badge bg-info text-dark">Kode: {{ $divisi->kode ?? '-' }}</span>
                                    </div>
                                    <p class="card-text text-muted small mb-0">
                                        <strong>Keterangan:</strong><br>
                                        {{ $divisi->keterangan ?? 'Tidak ada keterangan tambahan.' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-warning text-center mb-0" role="alert">
                                Belum ada divisi yang terdaftar pada instansi ini.
                            </div>
                        </div>
                    @endforelse
                </div>
            @else
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            @endif
        </div>

        <x-slot:footer>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
        </x-slot:footer>
    </x-partials.display.base-modal>
    <!-- ================= END MODAL DETAIL ================= -->

</div>
