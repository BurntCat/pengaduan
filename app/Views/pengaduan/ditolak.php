<?= $this->include('layout/header') ?>

<div class="max-w-xl mx-auto my-12 p-8 bg-white rounded-2xl shadow-sm border border-slate-100 text-center">
    <div class="w-16 h-16 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl font-bold">
        ✕
    </div>
    
    <h1 class="text-xl font-bold text-slate-800 mb-2">Pengaduan Ditolak</h1>
    <p class="text-sm text-slate-500 mb-6">
        Nomor Tiket: <span class="font-semibold text-slate-700"><?= esc($pengaduan['nomor_tiket']) ?></span>
    </p>

<div class="p-4 bg-red-50 border border-red-100 text-red-700 text-sm rounded-xl mb-6 text-left">
    <p class="font-semibold mb-1">Informasi:</p>
    Maaf, pengaduan Anda telah ditinjau oleh admin dan dinyatakan <strong>ditolak</strong> karena:
    <p class="mt-2 italic font-medium"><?= nl2br(esc($pengaduan['alasan_penolakan'] ?? 'Tidak ada alasan spesifik yang diberikan.')) ?></p>
</div>
    <a href="<?= site_url('pengaduan/riwayat') ?>" class="inline-block bg-brand-blue text-white font-semibold px-6 py-3 rounded-xl transition hover:opacity-90">
        &larr; Kembali ke Beranda / Buat Aduan Baru
    </a>
</div>

<?= $this->include('layout/footer') ?>