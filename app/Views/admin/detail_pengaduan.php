<!-- Tampilkan Flash Message -->
<?php if(session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>

<h2>Detail Pengaduan: <?= $pengaduan['nomor_tiket'] ?></h2>
<p>Kronologi: <?= $pengaduan['kronologi'] ?></p>

<hr>
<h3>Manajemen SLA & Tahapan</h3>
<table border="1" cellpadding="10">
    <tr>
        <th>Tahap</th>
        <th>Status</th>
        <th>SLA (Hari)</th>
        <th>Aksi Admin</th>
    </tr>
    <?php foreach($tahapan as $t): ?>
    <tr>
        <td><?= strtoupper($t['tahap']) ?></td>
        <td><?= $t['status'] ?></td>
        <td><?= $t['sla_hari'] ?> Hari</td>
        <td>
            <!-- Form untuk mengubah SLA secara individual -->
            <form action="/admin/pengaduan/update-sla" method="POST">
                <input type="hidden" name="tahapan_id" value="<?= $t['id'] ?>">
                <input type="hidden" name="pengaduan_id" value="<?= $pengaduan['id'] ?>">
                <input type="hidden" name="nama_tahap" value="<?= $t['tahap'] ?>">
                <input type="hidden" name="sla_lama" value="<?= $t['sla_hari'] ?>">
                
                <input type="number" name="sla_hari" value="<?= $t['sla_hari'] ?>" min="1" required>
                <button type="submit">Update SLA</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<hr>
<h3>Audit Trail (Riwayat Aktivitas)</h3>
<ul>
    <?php foreach($audit as $a): ?>
        <li>[<?= $a['created_at'] ?>] <b><?= $a['oleh'] ?></b>: <?= $a['aktivitas'] ?></li>
    <?php endforeach; ?>
</ul>