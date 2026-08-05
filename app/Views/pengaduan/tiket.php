<?= $this->include('layout/header') ?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="mb-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm p-4">
        <?= esc(session()->getFlashdata('success')) ?>
    </div>
<?php endif; ?>

<div class="rounded-2xl overflow-hidden shadow-sm border border-slate-100 bg-white">

    <!-- Header nomor tiket -->
    <div class="bg-brand-blue px-6 py-5 flex items-center gap-4">
        <div class="w-11 h-11 rounded-full bg-white/15 flex items-center justify-center shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" />
            </svg>
        </div>
        <div>
            <p class="text-xs text-blue-100 tracking-wide">Nomor Tiket</p>
            <p class="text-xl font-extrabold text-white tracking-wide"><?= esc($pengaduan['nomor_tiket']) ?></p>
        </div>
    </div>

    <!-- Timeline tahapan -->
    <div class="px-6 py-6">
        <?php
            $iconMap = [
                'diterima'         => 'check',
                'diverifikasi'     => 'check',
                'ditangani_bidang' => 'gear',
                'selesai'          => 'flag',
            ];
            $total = count($tahapan);
        ?>
        <?php foreach ($tahapan as $i => $t): ?>
            <?php
                $isSelesai = $t['status'] === 'selesai';
                $isProses  = $t['status'] === 'proses';
                $circleClass = $isSelesai
                    ? 'bg-emerald-500'
                    : ($isProses ? 'bg-brand-orange' : 'bg-slate-200');
                $badgeClass = $isSelesai
                    ? 'bg-emerald-50 text-emerald-600 border-emerald-200'
                    : ($isProses ? 'bg-orange-50 text-brand-orange border-orange-200' : 'bg-slate-50 text-slate-400 border-slate-200');
            ?>
            <div class="flex gap-4 <?= $i < $total - 1 ? 'pb-8 relative' : '' ?>">
                <?php if ($i < $total - 1): ?>
                    <span class="absolute left-[19px] top-10 bottom-0 w-0.5 bg-slate-200"></span>
                <?php endif; ?>

                <div class="w-10 h-10 rounded-full <?= $circleClass ?> flex items-center justify-center text-white shrink-0 z-10">
                    <?php if ($isSelesai): ?>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg>
                    <?php elseif ($isProses && $iconMap[$t['tahap']] === 'gear'): ?>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12a7.5 7.5 0 0 0 15 0m-15 0a7.5 7.5 0 1 1 15 0m-15 0H3m16.5 0H21m-1.5 0H12m-8.457 3.077 1.41-.513m14.095-5.13 1.41-.513M5.106 17.785l1.15-.964m11.49-9.642 1.149-.964M7.501 19.795l.75-1.3m7.5-12.99.75-1.3m-6.063 16.658.26-1.477m2.605-14.772.26-1.477m0 17.726-.26-1.477M10.698 4.614l-.26-1.477" />
                        </svg>
                    <?php else: ?>
                        <span class="text-sm font-semibold"><?= $i + 1 ?></span>
                    <?php endif; ?>
                </div>

                <div class="flex-1 flex items-start justify-between gap-3">
                    <div>
                        <p class="font-semibold text-slate-800"><?= esc($labelTahap[$t['tahap']]) ?></p>
                        <p class="text-sm text-slate-500">
                            <?php if ($t['status'] === 'menunggu'): ?>
                                Menunggu penyelesaian
                            <?php elseif ($t['status'] === 'proses'): ?>
                                Dalam proses penanganan
                            <?php else: ?>
                                <?= $t['tanggal_selesai'] ? date('d M Y H:i', strtotime($t['tanggal_selesai'])) : '-' ?>
                            <?php endif; ?>
                        </p>
                    </div>
                    <span class="text-xs font-medium border rounded-full px-3 py-1 flex items-center gap-1 whitespace-nowrap <?= $badgeClass ?>">
                        SLA: <?= (int) $t['sla_hari'] ?> Hari
                        <?php if ($isSelesai): ?>
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                        <?php else: ?>
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2" /><circle cx="12" cy="12" r="9" stroke-width="2"/>
                            </svg>
                        <?php endif; ?>
                    </span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Kartu bawah: SLA total & audit trail -->
    <div class="grid grid-cols-2 gap-4 px-6 pb-6">
        <div class="rounded-xl bg-emerald-600 text-white p-4 flex flex-col items-center text-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            <p class="text-xs font-medium">SLA Penanganan</p>
            <p class="text-[11px] opacity-80">Total SLA Maks.</p>
            <p class="text-lg font-bold"><?= (int) $totalSla ?> Hari Kerja</p>
        </div>
        <div class="rounded-xl bg-brand-blue text-white p-4 flex flex-col items-center text-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v13.5A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V9.568c0-.596-.237-1.169-.659-1.591l-4.318-4.318A2.25 2.25 0 0 0 14.432 3H9.568Z" />
            </svg>
            <p class="text-xs font-medium">Audit Trail</p>
            <p class="text-[11px] opacity-80">Seluruh aktivitas tercatat</p>
            <p class="text-sm font-bold">Transparan &amp; Akuntabel</p>
        </div>
    </div>

    <?php if (! empty($audit)): ?>
    <div class="px-6 pb-6">
        <p class="text-sm font-semibold text-slate-700 mb-2">Riwayat Aktivitas</p>
        <ul class="space-y-2">
            <?php foreach ($audit as $a): ?>
                <li class="text-xs text-slate-500 flex justify-between border-b border-slate-100 pb-2">
                    <span><?= esc($a['aktivitas']) ?></span>
                    <span class="whitespace-nowrap ml-3"><?= date('d M Y H:i', strtotime($a['created_at'])) ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>
</div>

<div class="text-center mt-6">
    <a href="<?= site_url('pengaduan') ?>" class="text-sm text-brand-blue font-medium hover:underline">&larr; Buat aduan baru</a>
</div>

<?= $this->include('layout/footer') ?>
