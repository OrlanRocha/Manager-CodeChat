<?php
/** @var string $pageTitle */
/** @var bool $isAuthPage */
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'CodeChat Manager', ENT_QUOTES, 'UTF-8') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-slate-50 text-slate-800">
<?php if (!empty($isAuthPage)): ?>
    <?php require $viewPath; ?>
<?php else: ?>
    <div class="min-h-screen flex">
        <?php require $this->config['app']['base_path'] . '/app/Views/layouts/sidebar.php'; ?>
        <main class="flex-1 p-6 lg:p-8">
            <header class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between mb-6">
                <div>
                    <p class="text-sm text-slate-500">Bem-vindo, <?= htmlspecialchars($userName ?? 'Usuário', ENT_QUOTES, 'UTF-8') ?></p>
                    <h1 class="text-2xl font-semibold text-slate-900"><?= htmlspecialchars($pageTitle ?? 'Dashboard', ENT_QUOTES, 'UTF-8') ?></h1>
                </div>
                <form action="/logout" method="POST">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-slate-800">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        Sair
                    </button>
                </form>
            </header>

            <?php require $viewPath; ?>
        </main>
    </div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<script src="/assets/js/app.js"></script>
</body>
</html>
