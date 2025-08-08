<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Langganan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Admin
        User::create([
            'name'       => 'Admin',
            'email'      => 'admin@material.com',
            'password'   => ('secret'),
            'location'   => 'Jl. Sukabumi No. 1',
            'phone'      => '081234567890',
            'role'       => 'admin',
            'status'     => 'aktif',
        ]);

        // Pelanggan
        DB::table('users')->insert([
            [
                'name'       => 'Hendri Wibowo',
                'email'      => 'banawi24@hotmail.com',
                'password'   => ('password'),
                'location'   => 'Jl. Anggrek No. 7',
                'phone'      => '082112345678',
                'role'       => 'pelanggan',
                'status'     => 'aktif',
            ],
            [
                'name'       => 'R.A. Kayla Hastuti, M.Pd',
                'email'      => 'ysaptono@yahoo.com',
                'password'   => ('password'),
                'location'   => 'Jl. Melati No. 10',
                'phone'      => '082113456789',
                'role'       => 'pelanggan',
                'status'     => 'aktif',
            ],
        ]);

        DB::table('paket_internets')->insert([
            ['nama_paket' => 'Paket 75 Mbps', 'kecepatan' => '75 Mbps', 'harga' => 387068],
            ['nama_paket' => 'Paket 18 Mbps', 'kecepatan' => '18 Mbps', 'harga' => 271187],
        ]);

        DB::table('langganans')->insert([
               [
                    'user_id' => 2,
                    'paket_id' => 1,
                    'status_langganan' => 'aktif',
                ],
                [
                    'user_id' => 3,
                    'paket_id' => 2,
                    'status_langganan' => 'nonaktif',
                ],

        ]);


        $langgananUser2 = Langganan::where('user_id', 2)->first();
        $langgananUser3 = Langganan::where('user_id', 3)->first();

        DB::table('tagihans')->insert([
             [
                'user_id' => 2,
                'langganan_id' => $langgananUser2?->id,
                'status_pembayaran' => 'Belum Lunas',
                'metode_pembayaran' => 'Transfer Bank',
                'jumlah_tagihan' => 311689,
                'tgl_jatuh_tempo' => '2024-12-31',
                'periode_tagihan' => '2024-12',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 3,
                'langganan_id' => $langgananUser3?->id,
                'status_pembayaran' => 'Belum Lunas',
                'metode_pembayaran' => 'Midtrans',
                'jumlah_tagihan' => 171939,
                'tgl_jatuh_tempo' => '2023-11-30',
                'periode_tagihan' => '2023-11',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('histori_pembayarans')->insert([
            [
                'tagihan_id' => 1,
                'user_id' => 2,
                'metode_pembayaran' => 'Transfer Bank',
                'status_pembayaran' => 'Lunas',
                'jumlah_dibayar' => 311689,
                'tanggal_pembayaran' => '2025-05-02',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tagihan_id' => 2,
                'user_id' => 3,
                'metode_pembayaran' => 'Offline',
                'status_pembayaran' => 'Lunas',
                'jumlah_dibayar' => 171939,
                'tanggal_pembayaran' => '2025-04-01',
                'created_at' => now(),
                'updated_at' => now(),
            ],   ]);

        DB::table('tikets')->insert([
            [
                'user_id' => 2,
                'category' => 'Gangguan Internet',
                'status' => 'menunggu',
                'description' => 'Internet mati total sejak pagi.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 3,
                'category' => 'Layanan Tambahan',
                'status' => 'selesai',
                'description' => 'Permintaan upgrade kecepatan.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('detail_tikets')->insert([
            [
                'tiket_id' => 1,
                'tasks' => json_encode([
                    ['task' => 'Teknisi akan dijadwalkan untuk kunjungan', 'completed' => false],
                    ['task' => 'Konfirmasi jadwal dengan pelanggan', 'completed' => false],
                    ['task' => 'Persiapan peralatan servis', 'completed' => false]
                ]),
                'isDone' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tiket_id' => 2,
                'tasks' => json_encode([
                    ['task' => 'Permintaan sudah diproses', 'completed' => true],
                    ['task' => 'Dokumentasi pekerjaan selesai', 'completed' => true],
                    ['task' => 'Laporan telah dikirim', 'completed' => true]
                ]),
                'isDone' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
