<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class PegawaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID'); // Menggunakan format data Indonesia
        // Daftar contoh pangkat/golongan PNS/Pegawai
        $pangkatGolonganList = [
            'Juru Muda - I/a',
            'Juru Muda Tingkat I - I/b',
            'Juru - I/c',
            'Juru Tingkat I - I/d',
            'Pengatur Muda - II/a',
            'Pengatur Muda Tingkat I - II/b',
            'Pengatur - II/c',
            'Pengatur Tingkat I - II/d',
            'Penata Muda - III/a',
            'Penata Muda Tingkat I - III/b',
            'Penata - III/c',
            'Penata Tingkat I - III/d',
            'Pembina - IV/a',
            'Pembina Tingkat I - IV/b',
            'Pembina Utama Muda - IV/c'
        ];

        // Daftar contoh jabatan umum
        $jabatanList = [
            'Analis Sistem',
            'Pranata Komputer',
            'Administrator Database',
            'Staf Administrasi Umum',
            'Kepala Seksi Operasional',
            'Bendahara Pengeluaran',
            'Pengelola Keuangan',
            'Sekretaris Pimpinan',
            'Pengawas Lapangan',
            'Fungsional Umum'
        ];

        $dataPegawai = [];

        for ($i = 0; $i < 50; $i++) {
            // Membuat NIP acak format umum (Contoh: TahunBulanTanggal + 6 digit angka random)
            $tahun = $faker->numberBetween(1975, 2000);
            $bulan = str_pad($faker->numberBetween(1, 12), 2, '0', STR_PAD_LEFT);
            $tanggal = str_pad($faker->numberBetween(1, 28), 2, '0', STR_PAD_LEFT);
            $nip = $tahun . $bulan . $tanggal . $faker->numerify('######') . $faker->numberBetween(10, 99);

            $dataPegawai[] = [
                'nama'             => $faker->name,
                'nip'              => $nip,
                'tanggal_lahir'    => $faker->dateTimeBetween('-50 years', '-22 years')->format('Y-m-d'),
                'pangkat_golongan' => $faker->randomElement($pangkatGolonganList),
                'jabatan'          => $faker->randomElement($jabatanList),
                'alamat'           => $faker->address,
                'divisi_id'        => $faker->numberBetween(1, 3), // Random angka 1 sampai 3
                'created_at'       => now(),
                'updated_at'       => now(),
            ];
        }

        // Masukkan data ke database secara massal (batch insert) agar lebih cepat
        DB::table('pegawai')->insert($dataPegawai);
    }
}
