<?php

use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\Pegawai;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    // Menggunakan state dengan penamaan flag yang spesifik
    public $search = '';

    // Reset halaman ke 1 saat kolom pencarian diketik
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function toggleStatus(Pegawai $pegawai)
    {
        if ($pegawai) {
            // Membalikkan nilai boolean status (true jadi false, false jadi true)
            $pegawai->status = !$pegawai->status;
            $pegawai->save();
            // Opsional: Anda bisa menambahkan flash message atau notifikasi sukses di sini
        }
    }

    public function delete(Pegawai $pegawai)
    {
        $pegawai->delete();
        session()->flash('message', 'Data pegawai berhasil dihapus!');
    }

    public function render()
    {
        // Mengisi flag list_pegawai dengan data hasil query
        $list_pegawai = Pegawai::with('divisi')
            ->when($this->search, function ($query) {
                $query
                    ->where('nama', 'like', '%' . $this->search . '%')
                    ->orWhere('nip', 'like', '%' . $this->search . '%')
                    ->orWhere('jabatan', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        return view('pages.pegawai.⚡index', [
            'list_pegawai' => $list_pegawai,
        ]);
    }

    public function exportCsv(): StreamedResponse
    {
        return response()->streamDownload(
            function () {
                $handle = fopen('php://output', 'w');

                // Menambahkan BOM (Byte Order Mark) UTF-8 agar karakter khusus
                // dan format teks terbaca dengan rapi saat dibuka di Microsoft Excel
                fwrite($handle, "\xEF\xBB\xBF");

                // 1. Buat Header Kolom CSV
                fputcsv($handle, ['NIP', 'Nama', 'Pangkat Golongan', 'Jabatan', 'Tanggal Lahir']);

                // 2. Ambil data database secara chunk (per 100 data) agar aman untuk performa server
                Pegawai::chunk(1000, function ($list_pegawai) use ($handle) {
                    foreach ($list_pegawai as $pegawai) {
                        fputcsv($handle, [$pegawai->nip, $pegawai->nama, $pegawai->pangkat_golongan, $pegawai->jabatan, $pegawai->tanggal_lahir->isoFormat('DD-MM-YYYY')]);
                    }
                });

                fclose($handle);
            },
            'data-pegawai-' . date('YmdHisv') . '.csv',
            [
                'Content-Type' => 'text/csv',
            ],
        );
    }
};
?>

<div>
    <div class="container-fluid px-3 px-lg-4 py-4">
        <x-partials.display.page-title title="Data Pegawai"></x-partials.display.page-title>
        <section class="row g-3">
            <div class="col-12 col-xl-12">
                <section class="panel">
                    <div class="panel-header d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="h5 mb-1 section-title">
                                <i class="bi bi-table" aria-hidden="true"></i>
                                <span>List Pegawai</span>
                            </h2>
                            <a href="{{ route('pegawai.create') }}" class="btn btn-sm btn-outline-primary"
                                title="Tambah Data">
                                <i class="bi bi-plus"></i>
                            </a>
                            <button wire:click="exportCsv" class="btn btn-sm btn-success me-2">
                                <span wire:loading.remove wire:target="exportCsv">
                                    <i class="bi bi-file-earmark-spreadsheet me-1"></i> Ekspor CSV
                                </span>
                                <span wire:loading wire:target="exportCsv">
                                    <span class="spinner-border spinner-border-sm" role="status"
                                        aria-hidden="true"></span> Menyiapkan...
                                </span>
                            </button>
                        </div>
                        <div style="width: 250px;">
                            <x-partials.forms.input-search />
                        </div>
                    </div>
                    <x-partials.feedback.alert />
                    <div class="table-responsive">
                        <table class="table align-middle mb-0" id="ordersTable">
                            <thead>
                                <tr>
                                    <th class="text-center" width="5%">No</th>
                                    <th>Nama & NIP</th>
                                    <th>Jabatan</th>
                                    <th>Divisi</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center" width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Looping menggunakan flag list_pegawai -->
                                @forelse ($list_pegawai as $index => $pegawai)
                                    <tr>
                                        <td class="text-center">{{ $list_pegawai->firstItem() + $index }}</td>
                                        <td>
                                            <span class="fw-bold text-dark d-block">{{ $pegawai->nama }}</span>
                                            <small class="text-muted">NIP: {{ $pegawai->nip }}</small>
                                        </td>
                                        <td>{{ $pegawai->jabatan }}</td>
                                        <td>
                                            <span class="badge bg-info text-dark">
                                                {{ $pegawai->divisi->nama }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" wire:click="toggleStatus({{ $pegawai->id }})"
                                                class="btn btn-link p-0 text-decoration-none border-0"
                                                title="Klik untuk mengubah status">
                                                @if ($pegawai->status)
                                                    <span class="badge bg-success-subtle text-success px-2 py-1"
                                                        style="cursor: pointer;">
                                                        <i class="bi bi-check-circle-fill me-1"></i> Aktif
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger-subtle text-danger px-2 py-1"
                                                        style="cursor: pointer;">
                                                        <i class="bi bi-x-circle-fill me-1"></i> Non-Aktif
                                                    </span>
                                                @endif
                                            </button>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm" role="group"
                                                aria-label="Aksi Pegawai">
                                                <!-- Tombol Edit -->
                                                <a href="{{ route('pegawai.edit', $pegawai->id) }}"
                                                    class="btn btn-outline-dark" title="Edit">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <!-- Tombol Detail -->
                                                <button type="button" class="btn btn-outline-info" title="Detail">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <!-- Tombol Hapus -->
                                                <button type="button" class="btn btn-outline-danger" title="Hapus"
                                                    wire:click="delete({{ $pegawai->id }})">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            Data pegawai tidak ditemukan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Bagian Pagination -->
                    <div class="panel-footer px-3 py-3 border-top">
                        {{ $list_pegawai->links('livewire::bootstrap') }}
                    </div>
                </section>
            </div>
        </section>
    </div>
</div>
