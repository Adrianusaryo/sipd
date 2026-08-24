<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projects = [
            [
                'title' => 'Pembangunan Pabrik Pengolahan Kelapa Sawit PT Sawit Jaya',
                'description' => 'Pengajuan dokumen AMDAL dan Izin Lingkungan untuk fasilitas pabrik CPO kapasitas 60 ton/jam.',
            ],
            [
                'title' => 'Pembangunan Gudang Logistik dan Cold Storage Maritim',
                'description' => 'Izin Persetujuan Bangunan Gedung (PBG) dan Analisis Dampak Lalu Lintas (Andalalin) area pelabuhan.',
            ],
            [
                'title' => 'Reklamasi dan Penataan Kawasan Pesisir Pantai Indah',
                'description' => 'Dokumen Kesesuaian Kegiatan Pemanfaatan Ruang Laut (KKPRL) dan AMDAL kawasan pesisir.',
            ],
            [
                'title' => 'Pengembangan Kawasan Industri Terpadu Nusantara',
                'description' => 'Perizinan Master Plan Tata Ruang, UKL-UPL, dan Sertifikat Laik Fungsi (SLF) infrastruktur utama.',
            ],
            [
                'title' => 'Pembangunan Pusat Perbelanjaan & Hotel Bintang 4',
                'description' => 'Pengajuan Izin Gangguan (HO), AMDAL, serta Sertifikat Keselamatan Kebakaran Gedung Tinggi.',
            ],
            [
                'title' => 'Pembangunan Pembangkit Listrik Tenaga Surya (PLTS) Ground-Mounted',
                'description' => 'Dokumen Kajian Lingkungan Hidup Strategis (KLHS) dan perizinan operasional kelistrikan.',
            ],
            [
                'title' => 'Proyek Perumahan Mentereng Residence Tahap 2',
                'description' => 'Izin Site Plan, Kesesuaian Pemanfaatan Ruang (KKPR), dan izin prinsip pengembang perumahan.',
            ],
            [
                'title' => 'Pembangunan Rumah Sakit Umum Daerah Tipe B',
                'description' => 'Dokumen Analisis Mengenai Dampak Lingkungan Limbah B3 dan Izin Operasional Fasilitas Kesehatan.',
            ],
            [
                'title' => 'Pengembangan Dermaga & Pelabuhan Perikanan Samudera',
                'description' => 'Izin pengerukan jalur pelayaran dan verifikasi standar keselamatan fasilitas pelabuhan.',
            ],
            [
                'title' => 'Pembangunan SPBU & Stasiun Pengisian Listrik (SPKLU)',
                'description' => 'Verifikasi Standar Keselamatan Migas, UKL-UPL, dan Izin Tempat Usaha.',
            ],
            [
                'title' => 'Pembangunan Sistem Pengolahan Air Minum (SPAM) Perkotaan',
                'description' => 'Studi Kelayakan Lingkungan, Izin Pengambilan Air Permukaan (SIPA), dan izin konstruksi pipa.',
            ],
            [
                'title' => 'Pembangunan Jembatan Penyeberangan & Bypass Antar Kota',
                'description' => 'Dokumen Andalalin, Izin Pinjam Pakai Kawasan, serta sertifikat laik fungsi jalan.',
            ],
            [
                'title' => 'Pembangunan Menara Telekomunikasi Bersama (Microcell Pole)',
                'description' => 'Izin Tempat Pengeluaran Penyiaran/Sinyal, PBG Menara, dan persetujuan warga sekitar.',
            ],
            [
                'title' => 'Pengembangan Area Pertambangan Pasir & Batuan Cadas',
                'description' => 'Izin Usaha Pertambangan (IUP) Operasi Produksi dan Dokumen Rencana Reklamasi Pascatambang.',
            ],
            [
                'title' => 'Pembangunan Pasar Modern Terpadu & Area Kuliner',
                'description' => 'Pengajuan Izin Usaha Toko Modern (IUTM), Penataan Pedagang, dan Amdal Lalu Lintas.',
            ],
            [
                'title' => 'Pembangunan Pusat Data (Data Center) Pasar Finansial',
                'description' => 'Perizinan Keandalan Kelistrikan Redundan, Sertifikat Ketahanan Gempa, dan UKL-UPL.',
            ],
            [
                'title' => 'Pengembangan Kawasan Agroturisasi & Perkebunan Organik',
                'description' => 'Izin Alih Fungsi Lahan Pertanian, Kajian Daya Dukung Lingkungan, dan Izin Usaha Pariwisata.',
            ],
            [
                'title' => 'Pembangunan Terminal Peti Kemas & Depo Kontainer',
                'description' => 'Dokumen Analisis Risiko Lingkungan, Izin Pengelolaan Area Logistik, dan Sertifikat Bangunan.',
            ],
            [
                'title' => 'Pembangunan Tempat Pengolahan Sampah Terpadu (TPST)',
                'description' => 'AMDAL Khusus Pengolahan Sampah, Kajian Emisi Gas Rumah Kaca, dan Izin Kelayakan Operasional.',
            ],
            [
                'title' => 'Pembangunan Fasilitas Olahraga & Stadion Serbaguna',
                'description' => 'Persetujuan Bangunan Gedung (PBG), Keselamatan Evakuasi Massal, dan Andalalin Lalu Lintas Utama.',
            ],
        ];

        foreach ($projects as $index => $item) {
            Project::create([
                'code_project' => 'PRJ-'.now()->format('Ymd').'-'.strtoupper(Str::random(5)),
                'title' => $item['title'],
                'description' => $item['description'],
                'is_active' => true,
                'created_at' => now()->subMinutes(20 - $index), // Supaya urutan created_at teratur
                'updated_at' => now()->subMinutes(20 - $index),
            ]);
        }
    }
}
