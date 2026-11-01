<?php

use App\Models\Instansi;
use Livewire\Component;

new class extends Component {
    public $list_instansi;
    public $search = '';
    public array $form = [
        'nama' => null,
        'telepon' => null,
        'alamat' => null,
    ];

    // Properti untuk menyimpan ID data yang sedang diedit
    public ?int $instansi_id = null;

    public function mount()
    {
        $this->loadInstansi();
    }

    // Helper untuk memuat data berdasarkan keyword pencarian
    public function loadInstansi()
    {
        $this->list_instansi = Instansi::query()
            ->when($this->search, function ($query) {
                $query->where('nama', 'like', '%' . $this->search . '%');
                // ->orWhere('telepon', 'like', '%' . $this->search . '%')
                // ->orWhere('alamat', 'like', '%' . $this->search . '%');
            })
            ->get();
    }

    // Hook Livewire: Otomatis dijalankan setiap kali nilai $search berubah
    public function updatedSearch()
    {
        $this->loadInstansi();
    }

    public function save()
    {
        $validated = $this->validate([
            'form.nama' => 'required|string|max:255',
            'form.telepon' => 'required|numeric',
            'form.alamat' => 'required|string',
        ]);
        // 2. Cek apakah sedang dalam mode edit (berdasarkan $instansi_id) atau tambah baru
        if ($this->instansi_id) {
            // PROSES UPDATE: Cari data lama lalu perbarui
            $instansi = Instansi::findOrFail($this->instansi_id);
            $instansi->update($validated['form']);

            // Pesan sukses saat memperbarui data
            session()->flash('message', 'Data instansi berhasil diperbarui!');
        } else {
            // PROSES TAMBAH BARU: Buat data baru ke database
            Instansi::create($validated['form']);

            // Pesan sukses saat menambah data baru
            session()->flash('message', 'Data instansi berhasil ditambahkan!');
        }
        $this->loadInstansi();
        $this->resetForm();
    }

    public function edit(Instansi $instansi)
    {
        $this->resetForm();
        $this->instansi_id = $instansi->id;
        // Populasi data lama ke dalam array form
        $this->form = [
            'nama' => $instansi->nama,
            'telepon' => $instansi->telepon,
            'alamat' => $instansi->alamat,
        ];
    }

    public function delete(Instansi $instansi)
    {
        $instansi->delete();
        session()->flash('message', 'Data instansi berhasil dihapus!');
        $this->loadInstansi();
        $this->resetForm();
    }

    // Fungsi untuk mereset form dan ID edit
    public function resetForm()
    {
        $this->reset(['form', 'instansi_id']);
        $this->resetValidation();
    }

    protected function validationAttributes()
    {
        return [
            'form.nama' => 'Nama Instansi',
            'form.telepon' => 'Nomor Telepon',
            'form.alamat' => 'Alamat',
        ];
    }
};
?>

<div>
    <div class="container-fluid px-3 px-lg-4 py-4">
        <x-partials.display.page-title title="Data Instansi"></x-partials.display.page-title>
        <section class="row g-3">
            <div class="col-12 col-xl-5">
                <form class="panel needs-validation" wire:submit="save" method="POST">
                    <div class="panel-header">
                        <div>
                            <h2 class="h5 mb-1 section-title"><i class="bi bi-ui-checks-grid"
                                    aria-hidden="true"></i><span>Form Information</span></h2>
                            <p class="text-muted mb-0">Isi Form Berikut.</p>
                        </div>

                    </div>
                    <x-partials.feedback.alert />

                    <div class="row g-3">
                        <x-partials.forms.input name="form.nama" label="Nama Instansi"
                            wire:model="form.nama"></x-partials.forms.input>
                        <x-partials.forms.input name="form.telepon" wire:model="form.telepon" label="Nomor Telepon"
                            type="number"></x-partials.forms.input>
                        <x-partials.forms.text-area setcol="12" name="form.alamat" label="Alamat"
                            rows="3"></x-partials.forms.text-area>

                    </div>
                    <div class="d-flex justify-content-end mt-4">
                        @if ($instansi_id)
                            <button type="button" wire:click="resetForm" class="btn btn-secondary me-2">Batal</button>
                        @endif
                        <x-partials.forms.submit target="save" :is-update="(bool) $instansi_id" />
                    </div>
                </form>
            </div>

            <div class="col-12 col-xl-7">
                <section class="panel">
                    <div class="panel-header">
                        <div>
                            <h2 class="h5 mb-1 section-title"><i class="bi bi-table" aria-hidden="true"></i><span>List
                                    Instansi</span></h2>
                            <p class="text-muted mb-0">Daftar Instansi.</p>
                        </div>

                        <x-partials.forms.input-search />


                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0" id="ordersTable" data-searchable-table="">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Telepon</th>
                                    <th>Alamat</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($list_instansi as $instansi)
                                    <tr>
                                        <td class="fw-semibold">{{ $loop->iteration }}</td>
                                        <td>{{ $instansi->nama }}</td>
                                        <td>{{ $instansi->telepon }}</td>
                                        <td>{{ $instansi->alamat }}</td>
                                        <td class="text-center">
                                            <button class="btn btn-secondary btn-sm" type="button"
                                                wire:click="edit({{ $instansi->id }})">Edit</button>
                                            <button class="btn btn-danger btn-sm" type="button"
                                                wire:click="delete({{ $instansi->id }})">Delete</button>
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
</div>
