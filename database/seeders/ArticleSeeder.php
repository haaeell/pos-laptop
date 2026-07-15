<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        $authorId = User::first()?->id;

        $categories = [
            'Tips & Trik' => 'tips-trik',
            'Panduan Membeli' => 'panduan-membeli',
            'Perawatan Perangkat' => 'perawatan-perangkat',
            'Berita Teknologi' => 'berita-teknologi',
        ];

        $categoryIds = [];
        foreach ($categories as $name => $slug) {
            $category = ArticleCategory::updateOrCreate(
                ['slug' => $slug],
                ['name' => $name]
            );
            $categoryIds[$slug] = $category->id;
        }

        $articles = [
            [
                'title' => 'Cara Memilih Laptop Sesuai Kebutuhan: Panduan Lengkap untuk Pemula',
                'category' => 'panduan-membeli',
                'excerpt' => 'Bingung memilih laptop yang tepat? Simak panduan lengkap memilih laptop berdasarkan kebutuhan, budget, dan spesifikasi dari Barokah Computer Subang.',
                'content' => <<<'HTML'
                    <p>Memilih laptop yang tepat bisa jadi membingungkan dengan banyaknya merek dan spesifikasi yang tersedia di pasaran. Sebagai toko komputer terpercaya di Subang, Barokah Computer akan membantu Anda memahami hal-hal penting sebelum membeli laptop baru maupun bekas berkualitas.</p>

                    <h2>1. Tentukan Kebutuhan Utama</h2>
                    <p>Langkah pertama adalah mengenali kebutuhan Anda. Apakah laptop akan digunakan untuk pekerjaan kantor, desain grafis, editing video, atau sekadar browsing dan mengetik dokumen? Kebutuhan ini akan menentukan spesifikasi minimal yang harus dipenuhi.</p>
                    <ul>
                        <li><strong>Penggunaan ringan</strong> (mengetik, browsing, media sosial): cukup dengan RAM 4-8GB dan prosesor Intel Celeron/Core i3 atau AMD Ryzen 3.</li>
                        <li><strong>Penggunaan menengah</strong> (multitasking, Excel berat, presentasi): RAM 8GB dan prosesor Core i5/Ryzen 5 sudah lebih dari cukup.</li>
                        <li><strong>Penggunaan berat</strong> (desain grafis, editing video, gaming): RAM 16GB ke atas, prosesor Core i7/Ryzen 7, dan kartu grafis diskrit (VGA).</li>
                    </ul>

                    <h2>2. Perhatikan Kapasitas dan Jenis Penyimpanan</h2>
                    <p>Laptop dengan SSD (Solid State Drive) jauh lebih cepat dibandingkan HDD (Hard Disk Drive) konvensional. Untuk kenyamanan sehari-hari, pilih laptop dengan SSD minimal 256GB. Jika butuh ruang penyimpanan lebih besar untuk foto atau video, pertimbangkan kombinasi SSD + HDD.</p>

                    <h2>3. Sesuaikan dengan Budget</h2>
                    <p>Barokah Computer menyediakan laptop baru maupun bekas berkualitas dengan berbagai rentang harga, mulai dari laptop entry-level untuk pelajar hingga laptop workstation untuk profesional. Laptop bekas berkualitas bisa menjadi pilihan cerdas untuk mendapatkan spesifikasi tinggi dengan harga lebih terjangkau, asalkan dibeli dari toko yang bergaransi.</p>

                    <h2>4. Cek Garansi dan Layanan Purna Jual</h2>
                    <p>Pastikan laptop yang Anda beli memiliki garansi resmi, baik dari toko maupun distributor. Layanan service dan konsultasi teknisi yang responsif juga penting agar Anda tidak kesulitan jika suatu saat laptop mengalami masalah.</p>

                    <h2>Kesimpulan</h2>
                    <p>Memilih laptop yang tepat dimulai dari mengenali kebutuhan, memperhatikan spesifikasi penting, dan menyesuaikan dengan budget. Kunjungi Barokah Computer, toko komputer terpercaya di Subang, untuk konsultasi gratis dan menemukan laptop yang paling sesuai untuk Anda.</p>
                    HTML,
            ],
            [
                'title' => '7 Tips Merawat Laptop Agar Awet dan Tidak Cepat Rusak',
                'category' => 'perawatan-perangkat',
                'excerpt' => 'Laptop lambat dan cepat panas? Ikuti 7 tips perawatan laptop dari teknisi Barokah Computer agar perangkat Anda tetap awet dan performa optimal.',
                'content' => <<<'HTML'
                    <p>Laptop merupakan investasi penting untuk pekerjaan maupun studi. Agar tetap awet dan berfungsi optimal dalam jangka panjang, perawatan yang tepat sangat diperlukan. Berikut tips dari teknisi Barokah Computer.</p>

                    <h2>1. Bersihkan Debu Secara Berkala</h2>
                    <p>Debu yang menumpuk di kipas dan ventilasi dapat menyebabkan laptop cepat panas (overheat). Bersihkan bagian luar secara rutin, dan lakukan servis pembersihan bagian dalam (cleaning) minimal setiap 6 bulan sekali di teknisi terpercaya.</p>

                    <h2>2. Gunakan Cooling Pad</h2>
                    <p>Jika sering digunakan untuk aktivitas berat seperti editing atau gaming, cooling pad membantu menjaga suhu laptop tetap stabil sehingga komponen di dalamnya lebih awet.</p>

                    <h2>3. Hindari Menutup Laptop Saat Masih Menyala Panas</h2>
                    <p>Berikan waktu laptop untuk mendingin sebelum ditutup rapat, terutama setelah pemakaian intensif. Ini mencegah panas terperangkap yang dapat merusak layar dan komponen internal.</p>

                    <h2>4. Update Sistem dan Antivirus Secara Berkala</h2>
                    <p>Sistem operasi dan antivirus yang selalu diperbarui membantu mencegah malware serta menjaga performa laptop tetap optimal.</p>

                    <h2>5. Jangan Biarkan Baterai Selalu 0% atau 100%</h2>
                    <p>Untuk menjaga kesehatan baterai, usahakan mengisi daya sebelum baterai benar-benar habis dan hindari membiarkan laptop selalu dalam keadaan terisi penuh dalam waktu lama.</p>

                    <h2>6. Gunakan Tas atau Sleeve Pelindung</h2>
                    <p>Saat bepergian, gunakan tas laptop yang memiliki bantalan pelindung untuk menghindari benturan yang dapat merusak layar maupun bodi laptop.</p>

                    <h2>7. Lakukan Servis Berkala di Tempat Terpercaya</h2>
                    <p>Servis rutin membantu mendeteksi masalah kecil sebelum menjadi kerusakan besar. Barokah Computer menyediakan layanan service cepat dan konsultasi teknisi untuk menjaga laptop Anda tetap prima.</p>

                    <h2>Kesimpulan</h2>
                    <p>Merawat laptop dengan baik akan memperpanjang usia pakai dan menjaga performanya tetap maksimal. Jika laptop Anda membutuhkan servis atau pembersihan menyeluruh, kunjungi Barokah Computer untuk konsultasi teknisi berpengalaman.</p>
                    HTML,
            ],
            [
                'title' => 'Laptop Baru vs Laptop Bekas Berkualitas: Mana yang Lebih Cocok untuk Anda?',
                'category' => 'panduan-membeli',
                'excerpt' => 'Masih ragu memilih antara laptop baru atau bekas berkualitas? Simak perbandingan lengkap kelebihan dan kekurangan keduanya di artikel ini.',
                'content' => <<<'HTML'
                    <p>Salah satu pertanyaan paling umum yang sering diajukan pelanggan Barokah Computer adalah, "Lebih baik beli laptop baru atau bekas?" Jawabannya tergantung kebutuhan, budget, dan prioritas masing-masing. Berikut perbandingannya.</p>

                    <h2>Kelebihan Laptop Baru</h2>
                    <ul>
                        <li>Garansi resmi dari distributor, biasanya 1-2 tahun.</li>
                        <li>Kondisi 100% baru tanpa riwayat pemakaian sebelumnya.</li>
                        <li>Spesifikasi dan model terbaru tersedia.</li>
                    </ul>

                    <h2>Kelebihan Laptop Bekas Berkualitas</h2>
                    <ul>
                        <li>Harga jauh lebih terjangkau untuk spesifikasi yang sama.</li>
                        <li>Bisa mendapatkan laptop dengan spesifikasi tinggi (misalnya laptop workstation atau laptop gaming) dengan budget terbatas.</li>
                        <li>Di Barokah Computer, semua unit bekas sudah melalui pengecekan menyeluruh dan tetap diberikan garansi toko.</li>
                    </ul>

                    <h2>Tips Membeli Laptop Bekas dengan Aman</h2>
                    <p>Jika memilih laptop bekas, pastikan membeli dari toko terpercaya yang melakukan pengecekan kondisi hardware, baterai, dan layar sebelum dijual kembali. Tanyakan juga riwayat penggunaan dan garansi yang diberikan oleh toko.</p>

                    <h2>Kesimpulan</h2>
                    <p>Baik laptop baru maupun bekas berkualitas memiliki kelebihan masing-masing. Jika budget terbatas namun menginginkan spesifikasi tinggi, laptop bekas berkualitas dari Barokah Computer bisa jadi solusi tepat dengan tetap mendapatkan garansi dan layanan purna jual yang jelas.</p>
                    HTML,
            ],
            [
                'title' => 'Kenali Penyebab Laptop Lemot dan Cara Mengatasinya',
                'category' => 'tips-trik',
                'excerpt' => 'Laptop Anda terasa lambat? Cari tahu penyebab umum laptop lemot dan solusi praktisnya agar performa kembali optimal.',
                'content' => <<<'HTML'
                    <p>Laptop yang tiba-tiba terasa lambat sering membuat pekerjaan terganggu. Sebelum memutuskan untuk membeli laptop baru, kenali dulu penyebab umum laptop lemot berikut ini.</p>

                    <h2>1. Terlalu Banyak Program Berjalan di Latar Belakang</h2>
                    <p>Program yang berjalan otomatis saat startup dapat membebani RAM dan memperlambat kinerja laptop. Nonaktifkan aplikasi yang tidak diperlukan melalui Task Manager.</p>

                    <h2>2. Penyimpanan Hampir Penuh</h2>
                    <p>Ruang penyimpanan yang hampir penuh, terutama pada drive sistem (C:), dapat memperlambat laptop secara signifikan. Rutin hapus file yang tidak diperlukan atau pindahkan ke penyimpanan eksternal.</p>

                    <h2>3. Masih Menggunakan HDD, Belum Upgrade ke SSD</h2>
                    <p>Ini adalah penyebab paling umum. Upgrade dari HDD ke SSD dapat meningkatkan kecepatan laptop secara drastis, bahkan pada laptop yang sudah berumur 5 tahun sekalipun. Barokah Computer melayani jasa upgrade SSD dengan harga bersahabat.</p>

                    <h2>4. Debu dan Overheat</h2>
                    <p>Laptop yang jarang dibersihkan cenderung mengalami overheat, yang membuat prosesor otomatis menurunkan performanya (thermal throttling). Servis pembersihan rutin dapat mengatasi masalah ini.</p>

                    <h2>5. Virus atau Malware</h2>
                    <p>Infeksi virus dapat membuat laptop berjalan sangat lambat bahkan tidak responsif. Pastikan menggunakan antivirus terpercaya dan selalu update.</p>

                    <h2>Kesimpulan</h2>
                    <p>Sebagian besar masalah laptop lemot bisa diatasi tanpa harus membeli unit baru, misalnya dengan upgrade SSD atau servis pembersihan. Konsultasikan masalah laptop Anda dengan teknisi Barokah Computer untuk solusi yang tepat dan hemat biaya.</p>
                    HTML,
            ],
            [
                'title' => 'Rekomendasi Spesifikasi Laptop untuk Pelajar dan Mahasiswa',
                'category' => 'panduan-membeli',
                'excerpt' => 'Cari laptop untuk kebutuhan sekolah atau kuliah? Ini rekomendasi spesifikasi laptop yang pas untuk pelajar dan mahasiswa dengan budget terjangkau.',
                'content' => <<<'HTML'
                    <p>Bagi pelajar dan mahasiswa, laptop menjadi kebutuhan penting untuk mengerjakan tugas, presentasi, hingga riset. Namun tidak semua laptop mahal cocok untuk kebutuhan ini. Berikut rekomendasi spesifikasi dari Barokah Computer.</p>

                    <h2>Spesifikasi Minimal yang Disarankan</h2>
                    <ul>
                        <li><strong>Prosesor:</strong> Intel Core i3/i5 generasi terbaru atau AMD Ryzen 3/5.</li>
                        <li><strong>RAM:</strong> Minimal 8GB agar multitasking antara browser, Word, dan aplikasi kuliah lancar.</li>
                        <li><strong>Penyimpanan:</strong> SSD minimal 256GB untuk kecepatan booting dan loading aplikasi.</li>
                        <li><strong>Baterai:</strong> Tahan minimal 6-8 jam untuk aktivitas seharian di kampus.</li>
                        <li><strong>Bobot:</strong> Ringan (di bawah 1.5kg) agar nyaman dibawa setiap hari.</li>
                    </ul>

                    <h2>Pertimbangkan Kebutuhan Jurusan</h2>
                    <p>Mahasiswa jurusan desain, arsitektur, atau teknik biasanya membutuhkan spesifikasi lebih tinggi dengan kartu grafis diskrit untuk menjalankan software seperti AutoCAD, SketchUp, atau Adobe Creative Suite. Sedangkan mahasiswa jurusan umum biasanya sudah cukup dengan laptop spesifikasi standar.</p>

                    <h2>Pilihan Laptop Bekas Berkualitas untuk Hemat Budget</h2>
                    <p>Bagi yang memiliki budget terbatas, laptop bekas berkualitas di Barokah Computer bisa menjadi solusi untuk mendapatkan spesifikasi lebih tinggi dengan harga terjangkau, lengkap dengan garansi toko.</p>

                    <h2>Kesimpulan</h2>
                    <p>Pilih laptop sesuai kebutuhan jurusan dan aktivitas kuliah sehari-hari. Datang langsung ke Barokah Computer Subang untuk konsultasi gratis menemukan laptop terbaik sesuai budget pelajar dan mahasiswa.</p>
                    HTML,
            ],
            [
                'title' => 'Perbedaan SSD dan HDD: Mana yang Lebih Baik untuk Laptop Anda?',
                'category' => 'tips-trik',
                'excerpt' => 'SSD atau HDD, mana yang lebih baik untuk laptop Anda? Simak perbedaan kecepatan, ketahanan, dan harga keduanya di sini.',
                'content' => <<<'HTML'
                    <p>SSD (Solid State Drive) dan HDD (Hard Disk Drive) adalah dua jenis media penyimpanan yang paling umum digunakan pada laptop. Keduanya memiliki karakteristik berbeda yang perlu Anda pahami sebelum memutuskan upgrade atau membeli laptop baru.</p>

                    <h2>Kecepatan</h2>
                    <p>SSD jauh lebih cepat dibandingkan HDD, baik dalam hal booting sistem operasi, membuka aplikasi, maupun transfer file. Laptop dengan SSD bisa menyala penuh hanya dalam hitungan detik, sedangkan HDD membutuhkan waktu lebih lama.</p>

                    <h2>Ketahanan</h2>
                    <p>SSD tidak memiliki komponen bergerak (mekanis) seperti HDD, sehingga lebih tahan terhadap guncangan dan benturan. Ini menjadikan SSD pilihan lebih aman untuk laptop yang sering dibawa bepergian.</p>

                    <h2>Harga dan Kapasitas</h2>
                    <p>HDD umumnya menawarkan kapasitas lebih besar dengan harga lebih murah per gigabyte, sementara SSD memiliki harga lebih tinggi namun performa jauh lebih baik. Banyak pengguna kini memilih kombinasi SSD untuk sistem operasi dan HDD untuk penyimpanan data tambahan.</p>

                    <h2>Rekomendasi Barokah Computer</h2>
                    <p>Jika laptop Anda masih menggunakan HDD dan terasa lambat, kami menyarankan upgrade ke SSD. Perbedaan performa akan sangat terasa signifikan, bahkan pada laptop yang sudah berumur beberapa tahun. Barokah Computer melayani jasa upgrade dan migrasi data dengan aman.</p>

                    <h2>Kesimpulan</h2>
                    <p>SSD unggul dalam kecepatan dan ketahanan, sementara HDD unggul dalam kapasitas dan harga. Untuk kenyamanan penggunaan sehari-hari, SSD adalah investasi yang sangat direkomendasikan.</p>
                    HTML,
            ],
        ];

        foreach ($articles as $index => $data) {
            Article::updateOrCreate(
                ['slug' => Str::slug($data['title'])],
                [
                    'article_category_id' => $categoryIds[$data['category']],
                    'user_id' => $authorId,
                    'title' => $data['title'],
                    'excerpt' => $data['excerpt'],
                    'content' => $data['content'],
                    'status' => 'published',
                    'published_at' => now()->subDays(count($articles) - $index),
                ]
            );
        }
    }
}
