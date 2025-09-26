<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Langganan;
use App\Models\PaketInternet;
use App\Models\Tagihan;
use App\Models\Tiket;
use App\Models\DetailTiket;
use App\Models\HistoriPembayaran;

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
            'password'   => 'secret',
            'location'   => 'Jl. Sukabumi No. 1',
            'phone'      => '+6281234567890',
            'role'       => 'admin',
            'status'     => 'aktif',
        ]);

        // Paket Internet
        DB::table('paket_internets')->insert([
            ['nama_paket' => 'Paket 10 Mbps', 'kecepatan' => '10 Mbps', 'harga' => 150000],
            ['nama_paket' => 'Paket 20 Mbps', 'kecepatan' => '20 Mbps', 'harga' => 250000],
            ['nama_paket' => 'Paket 30 Mbps', 'kecepatan' => '30 Mbps', 'harga' => 350000],
            ['nama_paket' => 'Paket 50 Mbps', 'kecepatan' => '50 Mbps', 'harga' => 500000],
            ['nama_paket' => 'Paket 75 Mbps', 'kecepatan' => '75 Mbps', 'harga' => 580000],
            ['nama_paket' => 'Paket 100 Mbps', 'kecepatan' => '100 Mbps', 'harga' => 600000],
        ]);

        // Data 20 user pelanggan dengan nama normal
        $users = [
            [
                'name' => 'Ahmad Santoso',
                'email' => 'ahmad.santoso@gmail.com',
                'location' => 'Jl. Merdeka No. 12, Jakarta',
                'phone' => '+6281212345678'
            ],
            [
                'name' => 'Siti Rahayu',
                'email' => 'siti.rahayu@gmail.com',
                'location' => 'Jl. Diponegoro No. 45, Bandung',
                'phone' => '+6281212345679'
            ],
            [
                'name' => 'Budi Prasetyo',
                'email' => 'budi.prasetyo@gmail.com',
                'location' => 'Jl. Gatot Subroto No. 78, Surabaya',
                'phone' => '+6281212345680'
            ],
            [
                'name' => 'Dewi Lestari',
                'email' => 'dewi.lestari@gmail.com',
                'location' => 'Jl. Sudirman No. 23, Medan',
                'phone' => '+6281212345681'
            ],
            [
                'name' => 'Joko Widodo',
                'email' => 'joko.widodo@gmail.com',
                'location' => 'Jl. Thamrin No. 56, Yogyakarta',
                'phone' => '+6281212345682'
            ],
            [
                'name' => 'Rina Wijaya',
                'email' => 'rina.wijaya@gmail.com',
                'location' => 'Jl. Asia Afrika No. 34, Bandung',
                'phone' => '+6281212345683'
            ],
            [
                'name' => 'Hendra Setiawan',
                'email' => 'hendra.setiawan@gmail.com',
                'location' => 'Jl. Pemuda No. 67, Semarang',
                'phone' => '+6281212345684'
            ],
            [
                'name' => 'Maya Sari',
                'email' => 'maya.sari@gmail.com',
                'location' => 'Jl. Ahmad Yani No. 89, Malang',
                'phone' => '+6281212345685'
            ],
            [
                'name' => 'Agus Suparman',
                'email' => 'agus.suparman@gmail.com',
                'location' => 'Jl. Pahlawan No. 11, Bali',
                'phone' => '+6281212345686'
            ],
            [
                'name' => 'Lina Marlina',
                'email' => 'lina.marlina@gmail.com',
                'location' => 'Jl. Merak No. 22, Bogor',
                'phone' => '+6281212345687'
            ],
            [
                'name' => 'Rudi Hartono',
                'email' => 'rudi.hartono@gmail.com',
                'location' => 'Jl. Kenanga No. 33, Depok',
                'phone' => '+6281212345688'
            ],
            [
                'name' => 'Dian Pertiwi',
                'email' => 'dian.pertiwi@gmail.com',
                'location' => 'Jl. Mawar No. 44, Tangerang',
                'phone' => '+6281212345689'
            ],
            [
                'name' => 'Fajar Nugroho',
                'email' => 'fajar.nugroho@gmail.com',
                'location' => 'Jl. Melati No. 55, Bekasi',
                'phone' => '+6281212345690'
            ],
            [
                'name' => 'Nina Safitri',
                'email' => 'nina.safitri@gmail.com',
                'location' => 'Jl. Anggrek No. 66, Solo',
                'phone' => '+6281212345691'
            ],
            [
                'name' => 'Eko Pratama',
                'email' => 'eko.pratama@gmail.com',
                'location' => 'Jl. Flamboyan No. 77, Makassar',
                'phone' => '+6281212345692'
            ],
            [
                'name' => 'Rina Astuti',
                'email' => 'rina.astuti@gmail.com',
                'location' => 'Jl. Cendana No. 88, Palembang',
                'phone' => '+6281212345693'
            ],
            [
                'name' => 'Ari Wibowo',
                'email' => 'ari.wibowo@gmail.com',
                'location' => 'Jl. Teratai No. 99, Samarinda',
                'phone' => '+6281212345694'
            ],
            [
                'name' => 'Mira Handayani',
                'email' => 'mira.handayani@gmail.com',
                'location' => 'Jl. Kamboja No. 10, Banjarmasin',
                'phone' => '+6281212345695'
            ],
            [
                'name' => 'Bayu Kurniawan',
                'email' => 'bayu.kurniawan@gmail.com',
                'location' => 'Jl. Dahlia No. 20, Pontianak',
                'phone' => '+6281212345696'
            ],
            [
                'name' => 'Tuti Alawiyah',
                'email' => 'tuti.alawiyah@gmail.com',
                'location' => 'Jl. Sakura No. 30, Manado',
                'phone' => '+6281212345697'
            ]
        ];

        // Buat 20 user pelanggan
        foreach ($users as $index => $userData) {
            $user = User::create([
                'name'       => $userData['name'],
                'email'      => $userData['email'],
                'password'   => 'password',
                'location'   => $userData['location'],
                'phone'      => $userData['phone'],
                'role'       => 'pelanggan',
                'status'     => 'aktif',
            ]);

            // Setiap user memiliki satu langganan
            $paketId = rand(1, 6); // Random pilih paket internet
            $langganan = Langganan::create([
                'user_id' => $user->id,
                'paket_id' => $paketId,
                'status_langganan' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Setiap user memiliki 20 tagihan (19 lunas, 1 belum lunas)
            for ($j = 1; $j <= 20; $j++) {
                $paket = PaketInternet::find($paketId);
                $isLunas = $j < 20; // 19 tagihan pertama lunas, yang ke-20 belum lunas

                $tagihan = Tagihan::create([
                    'user_id' => $user->id,
                    'langganan_id' => $langganan->id,
                    'status_pembayaran' => $isLunas ? 'lunas' : 'belum_lunas',
                    'metode_pembayaran' => $isLunas ? (rand(0, 1) ? 'Transfer Bank' : 'Midtrans') : 'Transfer Bank',
                    'jumlah_tagihan' => $paket->harga,
                    'tgl_jatuh_tempo' => now()->subMonths(20 - $j)->endOfMonth(),
                    'periode_tagihan' => now()->subMonths(20 - $j)->format('Y-m'),
                    'created_at' => now()->subMonths(20 - $j),
                    'updated_at' => now()->subMonths(20 - $j),
                ]);

                // Untuk tagihan yang lunas, buat histori pembayaran
                if ($isLunas) {
                    HistoriPembayaran::create([
                        'tagihan_id' => $tagihan->id,
                        'user_id' => $user->id,
                        'metode_pembayaran' => $tagihan->metode_pembayaran,
                        'status_pembayaran' => 'Lunas',
                        'jumlah_dibayar' => $tagihan->jumlah_tagihan,
                        'tanggal_pembayaran' => $tagihan->tgl_jatuh_tempo->subDays(rand(1, 10)),
                        'created_at' => $tagihan->tgl_jatuh_tempo->subDays(rand(1, 10)),
                        'updated_at' => $tagihan->tgl_jatuh_tempo->subDays(rand(1, 10)),
                    ]);
                }
            }

            // Setiap user memiliki 10 tiket
            $categories = ['Gangguan Internet', 'Layanan Tambahan', 'Billing', 'Teknis', 'Lainnya'];
            $statuses = ['menunggu', 'selesai'];
            $allTeknisi = User::where('role', 'teknisi')->pluck('id')->toArray();

            for ($k = 1; $k <= 10; $k++) {
                $category = $categories[array_rand($categories)];
                $status = $statuses[array_rand($statuses)];

                // Deskripsi tiket yang lebih variatif
                $descriptions = [
                    'Internet sering putus-putus dalam beberapa hari terakhir.',
                    'Kecepatan internet tidak sesuai dengan paket yang dijanjikan.',
                    'Minta penjelasan detail tagihan bulan ini.',
                    'Permintaan upgrade paket internet ke kecepatan yang lebih tinggi.',
                    'Kendala akses wifi di beberapa titik di rumah.',
                    'Internet mati total sejak pagi hari.',
                    'Permintaan pemindahan instalasi ke ruangan lain.',
                    'Kendala teknis saat mengakses beberapa website tertentu.',
                    'Laporan gangguan jaringan di area tempat tinggal.',
                    'Konsultasi mengenai perangkat wifi yang cocok untuk rumah.',
                    'Komplain mengenai tagihan yang terlihat tidak wajar.',
                    'Permintaan penjadwalan ulang instalasi.',
                    'Kendala streaming video yang sering buffer.',
                    'Laporan modem yang sering restart sendiri.',
                    'Permintaan bantuan teknis untuk setup jaringan.',
                    'Kendala gaming online dengan latency tinggi.',
                    'Laporan gangguan setelah hujan deras.',
                    'Permintaan info promo terbaru.',
                    'Kendala akses internet di malam hari.',
                    'Permintaan ganti password wifi.'
                ];

                $tiket = Tiket::create([
                    'user_id' => $user->id,
                    'category' => $category,
                    'status' => $status,
                    'description' => $descriptions[array_rand($descriptions)],
                    'created_at' => now()->subDays(rand(1, 365)),
                    'updated_at' => now()->subDays(rand(1, 365)),
                ]);

                // Jika tiket status = proses, assign teknisi 1–2 orang random
                if ($status === 'proses' && !empty($allTeknisi)) {
                    $randomTeknisi = collect($allTeknisi)->random(rand(1, 2));
                    $tiket->teknisis()->attach($randomTeknisi);
                }

                // Setiap tiket memiliki 10 detail tiket
                $isDone = $status === 'selesai';

                $tasks = [];
                for ($l = 1; $l <= 10; $l++) {
                    $taskCompleted = $isDone ? true : ($l <= rand(0, 5)); // Jika selesai, semua task completed

                    $taskDescriptions = [
                        'Pengecekan kualitas sinyal di lokasi pelanggan',
                        'Reset konfigurasi perangkat pelanggan',
                        'Koordinasi dengan teknisi lapangan',
                        'Pengecekan status pembayaran pelanggan',
                        'Konfirmasi jadwal kunjungan teknisi',
                        'Update firmware perangkat',
                        'Pengecekan gangguan di sisi provider',
                        'Penggantian perangkat yang rusak',
                        'Pemantauan kualitas jaringan',
                        'Follow up dengan pelanggan',
                        'Verifikasi data pelanggan',
                        'Pembuatan laporan teknis',
                        'Penjadwalan ulang kunjungan',
                        'Pengiriman perangkat pengganti',
                        'Koordinasi dengan departemen billing',
                        'Pemeriksaan kabel dan koneksi',
                        'Optimasi jaringan wifi',
                        'Eskalasi ke tim engineering',
                        'Pemberian kompensasi gangguan',
                        'Penutupan tiket setelah konfirmasi pelanggan'
                    ];

                    $tasks[] = [
                        'task' => $taskDescriptions[array_rand($taskDescriptions)],
                        'completed' => $taskCompleted
                    ];
                }

                DetailTiket::create([
                    'tiket_id' => $tiket->id,
                    'tasks' => json_encode($tasks),
                    'isDone' => $isDone,
                    'created_at' => $tiket->created_at,
                    'updated_at' => $tiket->updated_at,
                ]);
            }
        }

        $teknisis = [
            [
                'name' => 'Teknisi Andi',
                'email' => 'andi.teknisi@gmail.com',
                'location' => 'Jl. Kenangan No. 1, Jakarta',
                'phone' => '+628111111111'
            ],
            [
                'name' => 'Teknisi Budi',
                'email' => 'budi.teknisi@gmail.com',
                'location' => 'Jl. Mawar No. 2, Bandung',
                'phone' => '+628111111112'
            ],
            [
                'name' => 'Teknisi Citra',
                'email' => 'citra.teknisi@gmail.com',
                'location' => 'Jl. Melati No. 3, Surabaya',
                'phone' => '+628111111113'
            ],
            [
                'name' => 'Teknisi Dedi',
                'email' => 'dedi.teknisi@gmail.com',
                'location' => 'Jl. Anggrek No. 4, Yogyakarta',
                'phone' => '+628111111114'
            ],
            [
                'name' => 'Teknisi Eka',
                'email' => 'eka.teknisi@gmail.com',
                'location' => 'Jl. Flamboyan No. 5, Semarang',
                'phone' => '+628111111115'
            ],
            [
                'name' => 'Teknisi Fajar',
                'email' => 'fajar.teknisi@gmail.com',
                'location' => 'Jl. Cendana No. 6, Medan',
                'phone' => '+628111111116'
            ],
            [
                'name' => 'Teknisi Gita',
                'email' => 'gita.teknisi@gmail.com',
                'location' => 'Jl. Teratai No. 7, Bali',
                'phone' => '+628111111117'
            ],
            [
                'name' => 'Teknisi Hendra',
                'email' => 'hendra.teknisi@gmail.com',
                'location' => 'Jl. Dahlia No. 8, Makassar',
                'phone' => '+628111111118'
            ],
            [
                'name' => 'Teknisi Intan',
                'email' => 'intan.teknisi@gmail.com',
                'location' => 'Jl. Sakura No. 9, Palembang',
                'phone' => '+628111111119'
            ],
            [
                'name' => 'Teknisi Joko',
                'email' => 'joko.teknisi@gmail.com',
                'location' => 'Jl. Kamboja No. 10, Pontianak',
                'phone' => '+628111111120'
            ],
        ];

        foreach ($teknisis as $teknisiData) {
            User::create([
                'name'       => $teknisiData['name'],
                'email'      => $teknisiData['email'],
                'password'   => 'password',
                'location'   => $teknisiData['location'],
                'phone'      => $teknisiData['phone'],
                'role'       => 'teknisi',
                'status'     => 'aktif',
            ]);
        }
    }
}
