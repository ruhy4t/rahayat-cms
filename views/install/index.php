<?php
$errors = $errors ?? [];
$success = $success ?? false;
$defaults = $defaults ?? [];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?= Security::csrfMeta() ?>
    <title>Install Rahayat CMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-slate-100 text-slate-800">
    <main class="max-w-3xl mx-auto px-4 py-10">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-200">
                <h1 class="text-2xl font-bold">Install Rahayat CMS</h1>
                <p class="text-sm text-slate-500 mt-1">Isi konfigurasi awal website sekolah. Installer akan terkunci setelah selesai.</p>
            </div>

            <?php if ($success): ?>
                <div class="p-6">
                    <div class="p-4 rounded-lg bg-green-50 border border-green-200 text-green-800">
                        Instalasi berhasil. Silakan masuk ke halaman admin.
                    </div>
                    <a href="/login" class="inline-flex mt-4 px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">Login Admin</a>
                </div>
            <?php else: ?>
                <?php if (!empty($errors)): ?>
                    <div class="mx-6 mt-6 p-4 rounded-lg bg-red-50 border border-red-200 text-red-700">
                        <?php foreach ($errors as $error): ?>
                            <div><?= e($error) ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="p-6 space-y-8">
                    <?= Security::csrfInput() ?>

                    <section>
                        <h2 class="font-semibold mb-4">Website</h2>
                        <div class="grid md:grid-cols-2 gap-4">
                            <label class="block">
                                <span class="text-sm font-medium">URL Website</span>
                                <input name="app_url" required value="<?= e($defaults['app_url'] ?? '') ?>" placeholder="https://nama-sekolah.sch.id" class="mt-1 w-full rounded-lg border-slate-300">
                            </label>
                            <label class="block">
                                <span class="text-sm font-medium">Akhiran Domain</span>
                                <input name="required_domain_suffix" required value=".sch.id" class="mt-1 w-full rounded-lg border-slate-300">
                            </label>
                            <label class="block">
                                <span class="text-sm font-medium">Nama Sekolah</span>
                                <input name="school_name" required value="Sekolah Rahayat" class="mt-1 w-full rounded-lg border-slate-300">
                            </label>
                            <label class="block">
                                <span class="text-sm font-medium">Email Sekolah</span>
                                <input type="email" name="school_email" required value="info@rahayat.sch.id" class="mt-1 w-full rounded-lg border-slate-300">
                            </label>
                        </div>
                    </section>

                    <section>
                        <h2 class="font-semibold mb-4">Database</h2>
                        <div class="grid md:grid-cols-2 gap-4">
                            <label class="block">
                                <span class="text-sm font-medium">Host</span>
                                <input name="db_host" required value="<?= e($defaults['db_host'] ?? 'localhost') ?>" class="mt-1 w-full rounded-lg border-slate-300">
                            </label>
                            <label class="block">
                                <span class="text-sm font-medium">Port</span>
                                <input name="db_port" required value="<?= e($defaults['db_port'] ?? '3306') ?>" class="mt-1 w-full rounded-lg border-slate-300">
                            </label>
                            <label class="block">
                                <span class="text-sm font-medium">Database</span>
                                <input name="db_name" required value="<?= e($defaults['db_name'] ?? '') ?>" class="mt-1 w-full rounded-lg border-slate-300">
                            </label>
                            <label class="block">
                                <span class="text-sm font-medium">User</span>
                                <input name="db_user" required value="<?= e($defaults['db_user'] ?? '') ?>" class="mt-1 w-full rounded-lg border-slate-300">
                            </label>
                            <label class="block md:col-span-2">
                                <span class="text-sm font-medium">Password Database</span>
                                <input type="password" name="db_pass" class="mt-1 w-full rounded-lg border-slate-300">
                            </label>
                        </div>
                    </section>

                    <section>
                        <h2 class="font-semibold mb-4">Admin Awal</h2>
                        <div class="grid md:grid-cols-2 gap-4">
                            <label class="block">
                                <span class="text-sm font-medium">Nama Admin</span>
                                <input name="admin_name" required value="Administrator" class="mt-1 w-full rounded-lg border-slate-300">
                            </label>
                            <label class="block">
                                <span class="text-sm font-medium">Username</span>
                                <input name="admin_username" required value="admin" class="mt-1 w-full rounded-lg border-slate-300">
                            </label>
                            <label class="block md:col-span-2">
                                <span class="text-sm font-medium">Email Admin</span>
                                <input type="email" name="admin_email" required value="admin@rahayat.sch.id" class="mt-1 w-full rounded-lg border-slate-300">
                            </label>
                            <label class="block">
                                <span class="text-sm font-medium">Password</span>
                                <input type="password" name="admin_password" required minlength="8" class="mt-1 w-full rounded-lg border-slate-300">
                            </label>
                            <label class="block">
                                <span class="text-sm font-medium">Konfirmasi Password</span>
                                <input type="password" name="admin_password_confirm" required minlength="8" class="mt-1 w-full rounded-lg border-slate-300">
                            </label>
                        </div>
                    </section>

                    <div class="flex justify-end">
                        <button type="submit" class="px-5 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">Install Sekarang</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </main>
</body>

</html>
