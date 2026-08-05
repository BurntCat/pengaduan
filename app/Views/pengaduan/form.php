<?= $this->include('layout/header') ?>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8">

    <!-- Header -->
    <div class="flex items-center gap-4 mb-8">
        <div class="w-12 h-12 rounded-full bg-brand-blue flex items-center justify-center shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3-15H6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 6 21h10.5a2.25 2.25 0 0 0 2.25-2.25V9L14.25 3z" />
            </svg>
        </div>
        <div>
            <h1 class="text-xl font-bold text-slate-800">Form Pengaduan</h1>
            <p class="text-sm text-slate-500">Sampaikan aduan Anda dengan mudah dan aman.</p>
        </div>
    </div>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="mb-6 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm p-4">
            <ul class="list-disc list-inside space-y-1">
                <?php foreach (session()->getFlashdata('errors') as $err): ?>
                    <li><?= esc($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?= site_url('pengaduan/kirim') ?>" method="post" enctype="multipart/form-data" class="space-y-6">
        <?= csrf_field() ?>

        <!-- Kategori Pengaduan -->
        <div class="flex gap-4 items-start">
            <div class="w-10 h-10 rounded-lg bg-brand-blueLt flex items-center justify-center shrink-0 mt-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-brand-blue" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                </svg>
            </div>
            <div class="flex-1">
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Kategori Pengaduan</label>
                <select name="kategori_id" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-slate-600 focus:outline-none focus:ring-2 focus:ring-brand-blue/40 focus:border-brand-blue" required>
                    <option value="">Pilih kategori pengaduan</option>
                    <?php foreach ($kategori as $k): ?>
                        <option value="<?= $k['id'] ?>" <?= old('kategori_id') == $k['id'] ? 'selected' : '' ?>>
                            <?= esc($k['nama_kategori']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Lokasi Kejadian -->
        <div class="flex gap-4 items-start">
            <div class="w-10 h-10 rounded-lg bg-brand-blueLt flex items-center justify-center shrink-0 mt-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-brand-blue" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                </svg>
            </div>
            <div class="flex-1">
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Lokasi Kejadian</label>
                <select name="lokasi_id" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-slate-600 focus:outline-none focus:ring-2 focus:ring-brand-blue/40 focus:border-brand-blue" required>
                    <option value="">Pilih lokasi kejadian</option>
                    <?php foreach ($lokasi as $l): ?>
                        <option value="<?= $l['id'] ?>" <?= old('lokasi_id') == $l['id'] ? 'selected' : '' ?>>
                            <?= esc($l['nama_lokasi']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Kronologi -->
        <div class="flex gap-4 items-start">
            <div class="w-10 h-10 rounded-lg bg-brand-blueLt flex items-center justify-center shrink-0 mt-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-brand-blue" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z" />
                </svg>
            </div>
            <div class="flex-1">
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Kronologi</label>
                <textarea name="kronologi" rows="4" placeholder="Uraikan kronologi kejadian secara jelas"
                    class="w-full rounded-xl border border-slate-200 px-4 py-3 text-slate-600 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-blue/40 focus:border-brand-blue resize-y"
                    required><?= old('kronologi') ?></textarea>
            </div>
        </div>

        <!-- Lampiran Bukti -->
        <div class="flex gap-4 items-start">
            <div class="w-10 h-10 rounded-lg bg-brand-blueLt flex items-center justify-center shrink-0 mt-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-brand-blue" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m18.375 12.739-7.693 7.693a4.5 4.5 0 0 1-6.364-6.364l10.94-10.94A3 3 0 1 1 19.5 7.372L8.552 18.32m.009-.01-.01.01m5.699-9.941-7.81 7.81a1.5 1.5 0 0 0 2.112 2.13" />
                </svg>
            </div>
            <div class="flex-1">
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Lampiran Bukti</label>
                <label for="bukti" class="flex flex-col items-center justify-center gap-2 border-2 border-dashed border-slate-200 rounded-xl px-4 py-8 cursor-pointer hover:border-brand-blue/50 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-brand-blue" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0 3 3m-3-3-3 3M6.75 19.5a4.5 4.5 0 0 1-1.41-8.775 5.25 5.25 0 0 1 10.233-2.33 3 3 0 0 1 3.758 3.848A3.752 3.752 0 0 1 18 19.5H6.75Z" />
                    </svg>
                    <span class="text-sm text-slate-600"><span class="font-medium">Klik untuk unggah file</span></span>
                    <span class="text-xs text-slate-400">Maks. 10 MB (PDF, JPG, PNG)</span>
                    <span id="fileName" class="text-xs text-brand-blue font-medium hidden"></span>
                </label>
                <input type="file" name="bukti" id="bukti" accept=".pdf,.jpg,.jpeg,.png" class="hidden"
                       onchange="document.getElementById('fileName').textContent = this.files[0]?.name || ''; document.getElementById('fileName').classList.toggle('hidden', !this.files[0]);">
            </div>
        </div>

        <!-- Identitas Pelapor -->
        <div class="flex gap-4 items-start">
            <div class="w-10 h-10 rounded-lg bg-brand-blueLt flex items-center justify-center shrink-0 mt-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-brand-blue" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                </svg>
            </div>
            <div class="flex-1 space-y-3">
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Identitas Pelapor</label>
                <input type="text" name="nama_pelapor" placeholder="Nama lengkap" value="<?= old('nama_pelapor') ?>"
                    class="w-full rounded-xl border border-slate-200 px-4 py-3 text-slate-600 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-blue/40 focus:border-brand-blue"
                    required>

                <input type="email" name="email_pelapor" placeholder="Email (opsional)" value="<?= old('email_pelapor') ?>"
                    class="w-full rounded-xl border border-slate-200 px-4 py-3 text-slate-600 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-blue/40 focus:border-brand-blue">

                <input type="text" name="no_hp_pelapor" placeholder="No. HP (opsional)" value="<?= old('no_hp_pelapor') ?>"
                    class="w-full rounded-xl border border-slate-200 px-4 py-3 text-slate-600 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-blue/40 focus:border-brand-blue">

                <label class="flex items-center gap-3 pt-1 cursor-pointer select-none">
                    <input type="checkbox" name="rahasiakan_identitas" value="1" checked class="hidden"
                        onchange="
                            document.getElementById('toggleTrack').classList.toggle('bg-emerald-400', this.checked);
                            document.getElementById('toggleTrack').classList.toggle('bg-slate-200', !this.checked);
                            document.getElementById('toggleKnob').classList.toggle('translate-x-5', this.checked);
                        ">
                    <span id="toggleTrack" class="w-11 h-6 rounded-full bg-emerald-400 relative transition shrink-0">
                        <span id="toggleKnob" class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition translate-x-5"></span>
                    </span>
                    <span class="text-sm text-slate-500">
                        <span class="font-medium text-slate-700">Rahasiakan identitas</span><br>
                        Identitas Anda tidak akan ditampilkan kepada publik.
                    </span>
                </label>
            </div>
        </div>

        <!-- Submit -->
        <button type="submit"
            class="w-full flex items-center justify-center gap-2 bg-brand-orange hover:bg-orange-500 text-white font-semibold py-3.5 rounded-xl transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.126A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.876L5.999 12Zm0 0h7.5" />
            </svg>
            Kirim Aduan
        </button>
    </form>
</div>

<?= $this->include('layout/footer') ?>
