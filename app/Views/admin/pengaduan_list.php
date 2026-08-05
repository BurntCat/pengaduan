<?= $this->include('layout/header') ?>

<h1 class="text-xl font-bold text-slate-800 mb-6">Kelola Pengaduan (Admin)</h1>

<?php if (session()->getFlashdata('success')): ?>
    <div class="mb-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm p-4">
        <?= esc(session()->getFlashdata('success')) ?>
    </div>
<?php endif; ?>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-500 text-left">
            <tr>
                <th class="px-4 py-3">Nomor Tiket</th>
                <th class="px-4 py-3">Kategori</th>
                <th class="px-4 py-3">Pelapor</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Tanggal</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            <?php foreach ($daftar as $d): ?>
            <tr>
                <td class="px-4 py-3 font-medium text-brand-blue"><?= esc($d['nomor_tiket']) ?></td>
                <td class="px-4 py-3"><?= esc($d['nama_kategori']) ?></td>
                <td class="px-4 py-3"><?= $d['rahasiakan_identitas'] ? 'Dirahasiakan' : esc($d['nama_pelapor']) ?></td>
                <td class="px-4 py-3 capitalize"><?= esc(str_replace('_', ' ', $d['status_akhir'])) ?></td>
                <td class="px-4 py-3"><?= date('d M Y', strtotime($d['created_at'])) ?></td>
                <td class="px-4 py-3 text-right">
                    <a href="<?= site_url('admin/pengaduan/' . $d['nomor_tiket']) ?>" class="text-brand-blue hover:underline font-medium">Atur SLA</a>
                </td>
                <td class="px-4 py-3 text-right space-x-2">
                    <a href="<?= site_url('admin/pengaduan/' . $d['nomor_tiket']) ?>" class="text-brand-blue hover:underline font-medium">Atur SLA</a>
                    
                    <!-- Ubah menjadi tautan ke halaman form tolak -->
                    <a href="<?= site_url('admin/pengaduan/' . $d['nomor_tiket'] . '/form-tolak') ?>" class="text-red-500 hover:text-red-700 font-medium ml-2">Tolak</a>
                </td>
            <?php endforeach; ?>
            <?php if (empty($daftar)): ?>
            <tr><td colspan="6" class="px-4 py-6 text-center text-slate-400">Belum ada pengaduan.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?= $this->include('layout/footer') ?>
