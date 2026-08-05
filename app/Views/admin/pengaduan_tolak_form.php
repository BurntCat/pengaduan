<?= $this->include('layout/header') ?>

<div class="max-w-xl mx-auto my-8 bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
    <a href="<?= site_url('admin/pengaduan') ?>" class="text-sm text-brand-blue hover:underline">&larr; Batal / Kembali</a>

    <h1 class="text-xl font-bold text-slate-800 mt-4 mb-1">Form Penolakan Pengaduan</h1>
    <p class="text-sm text-slate-500 mb-6">Nomor Tiket: <span class="font-semibold text-slate-700"><?= esc($pengaduan['nomor_tiket']) ?></span></p>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm p-4">
            <?= esc(session()->getFlashdata('error')) ?>
        </div>
    <?php endif; ?>

    <form action="<?= site_url('admin/pengaduan/' . $pengaduan['nomor_tiket'] . '/tolak') ?>" method="post">
        <?= csrf_field() ?>

        <div class="mb-4">
            <label class="block text-xs font-semibold text-slate-600 mb-2">Alasan Penolakan (Akan dibaca oleh Pelapor):</label>
            <textarea name="alasan_penolakan" rows="4" required class="w-full rounded-xl border border-slate-200 p-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-400" placeholder="Tuliskan alasan yang jelas mengapa pengaduan ini ditolak..."><?= old('alasan_penolakan') ?></textarea>
        </div>

        <div class="flex justify-end gap-3">
            <a href="<?= site_url('admin/pengaduan') ?>" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-semibold text-sm hover:bg-slate-50 transition">Kembali</a>
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-red-500 hover:bg-red-600 text-white font-semibold text-sm transition">Konfirmasi & Tolak Pengaduan</button>
        </div>
    </form>
</div>

<?= $this->include('layout/footer') ?>