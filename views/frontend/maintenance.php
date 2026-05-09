<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sedang Dalam Perawatan -
        <?= e($profile['name'] ?? 'Sekolah') ?>
    </title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-xl w-full text-center">
        <div class="mb-8 flex justify-center">
            <?php if (!empty($profile['logo'])): ?>
                <img src="/storage/<?= e($profile['logo']) ?>" alt="Logo" class="h-24 w-auto">
            <?php else: ?>
                <div
                    class="w-24 h-24 bg-primary-600 rounded-2xl flex items-center justify-center text-white text-4xl font-bold bg-indigo-600">
                    <?= substr($profile['name'] ?? 'S', 0, 1) ?>
                </div>
            <?php endif; ?>
        </div>

        <h1 class="text-4xl font-bold text-gray-900 mb-4">Website Sedang Dalam Perawatan</h1>

        <div class="prose prose-lg mx-auto text-gray-600 mb-8">
            <p>
                <?= nl2br(e($settings['maintenance_message'] ?? 'Kami sedang melakukan peningkatan sistem untuk memberikan layanan yang lebih baik. Mohon kembali lagi nanti.')) ?>
            </p>
        </div>

        <div
            class="bg-blue-50 text-blue-800 px-6 py-4 rounded-xl border border-blue-100 inline-flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>Silahkan hubungi kami jika ada keperluan mendesak.</span>
        </div>

        <?php if (isset($settings['maintenance_end_time']) && !empty($settings['maintenance_end_time'])): ?>
            <p class="text-sm text-gray-500 mt-8">
                Perkiraan selesai:
                <?= e($settings['maintenance_end_time']) ?>
            </p>
        <?php endif; ?>

        <div class="mt-8 text-sm text-gray-400">
            &copy;
            <?= date('Y') ?>
            <?= e($profile['name'] ?? 'Sekolah') ?>.
        </div>
    </div>
</body>

</html>