<h2>Daftar Seluruh Pengaduan Masuk</h2>
<p>Berikut adalah daftar riwayat seluruh pengaduan yang tercatat di dalam sistem.</p>

<?php if(empty($riwayat)): ?>
    <div style="padding: 20px; background: #fff3cd; color: #856404; border-radius: 5px;">
        Belum ada data pengaduan yang tercatat di sistem.
    </div>
<?php else: ?>
    <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse; margin-top: 15px;">
        <thead>
            <tr style="background-color: #f2f2f2; text-align: left;">
                <th>Nomor Tiket</th>
                <th>Tanggal Kirim</th>
                <th>Nama Pelapor</th>
                <th>Status Terakhir</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($riwayat as $r): ?>
            <tr>
                <td><b><?= $r['nomor_tiket'] ?></b></td>
                <td><?= date('d M Y, H:i', strtotime($r['created_at'])) ?></td>
                <td><?= $r['rahasiakan_identitas'] ? 'Hamba Allah' : $r['nama_pelapor'] ?></td>
                <td>
                    <span style="padding: 4px 8px; background: #e2e3e5; border-radius: 4px; font-size: 12px;">
                        <?= strtoupper($r['status_akhir']) ?>
                    </span>
                </td>
                <td>
                    <!-- Tombol untuk melihat detail tiket -->
                    <a href="/pengaduan/status/<?= $r['nomor_tiket'] ?>" style="padding: 6px 12px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; display: inline-block;">
                        Lihat Tiket
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<br>
<a href="/pengaduan">← Kirim Pengaduan Baru</a>