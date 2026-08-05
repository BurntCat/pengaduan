<?= $this->include('layout/header') ?>

<a href="<?= site_url('admin/pengaduan') ?>" class="text-sm text-brand-blue hover:underline">&larr; Kembali ke daftar</a>

<h1 class="text-xl font-bold text-slate-800 mt-2 mb-1">Atur SLA</h1>
<p class="text-sm text-slate-500 mb-6">Tiket: <span class="font-semibold text-slate-700"><?= esc($pengaduan['nomor_tiket']) ?></span></p>

<?php if (session()->getFlashdata('success')): ?>
    <div class="mb-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm p-4">
        <?= esc(session()->getFlashdata('success')) ?>
    </div>
<?php endif; ?>

<form action="<?= site_url('admin/pengaduan/' . $pengaduan['nomor_tiket'] . '/simpan') ?>" method="post">
    <?= csrf_field() ?>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 divide-y divide-slate-100">
        <?php foreach ($tahapan as $t): ?>
        <div class="px-6 py-4 flex flex-wrap items-center gap-4">
            <div class="w-40 shrink-0">
                <p class="font-semibold text-slate-800"><?= esc($labelTahap[$t['tahap']]) ?></p>
            </div>

            <!-- Cek apakah ini tahap pertama / 'diterima' -->
            <?php if ($t['tahap'] === 'diterima' || $t['urutan'] == 1): ?>
                <!-- PATEN: Tidak bisa diubah oleh admin -->
                <div class="flex items-center gap-2">
                    <label class="text-xs text-slate-500">SLA (hari)</label>
                    <!-- Input readonly agar tetap terkirim nilainya ke controller jika dibutuhkan -->
                    <input type="number" name="sla[<?= $t['id'] ?>]" value="<?= (int) $t['sla_hari'] ?>" readonly
                        class="w-20 rounded-lg border border-slate-200 bg-slate-100 px-3 py-2 text-sm text-slate-500 cursor-not-allowed">
                </div>

                <div class="flex items-center gap-2">
                    <label class="text-xs text-slate-500">Status</label>
                    <!-- Status dipatenkan menjadi Selesai dan dikunci -->
                    <input type="hidden" name="status[<?= $t['id'] ?>]" value="selesai">
                    <span class="px-3 py-2 text-sm font-semibold text-emerald-600 bg-emerald-50 rounded-lg border border-emerald-100">
                        Selesai
                    </span>
                </div>
            <?php else: ?>
                <!-- NORMAL: Tahap selain pertama bebas diatur admin -->
                <div class="flex items-center gap-2">
                    <label class="text-xs text-slate-500">SLA (hari)</label>
                    <input type="number" min="1" name="sla[<?= $t['id'] ?>]" value="<?= (int) $t['sla_hari'] ?>"
                        class="w-20 rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/40">
                </div>

                <div class="flex items-center gap-2">
                    <label class="text-xs text-slate-500">Status</label>
                    <select name="status[<?= $t['id'] ?>]" class="rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/40">
                        <option value="menunggu" <?= $t['status'] === 'menunggu' ? 'selected' : '' ?>>Menunggu</option>
                        <option value="proses" <?= $t['status'] === 'proses' ? 'selected' : '' ?>>Proses</option>
                        <option value="selesai" <?= $t['status'] === 'selesai' ? 'selected' : '' ?>>Selesai</option>
                    </select>
                </div>
            <?php endif; ?>

        </div>
        <?php endforeach; ?>
    </div>

    <button type="submit" class="mt-6 bg-brand-orange hover:bg-orange-500 text-white font-semibold px-6 py-3 rounded-xl transition">
        Simpan Perubahan
    </button>
</form>

<?= $this->include('layout/footer') ?>
