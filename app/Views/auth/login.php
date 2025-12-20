<div class="min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8">
        <div class="flex items-center gap-3 mb-6">
            <div class="h-12 w-12 rounded-xl bg-indigo-600 text-white flex items-center justify-center">
                <i class="fa-solid fa-lock"></i>
            </div>
            <div>
                <h2 class="text-xl font-semibold text-slate-900">Acesso ao painel</h2>
                <p class="text-sm text-slate-500">Entre com suas credenciais</p>
            </div>
        </div>

        <?php if (!empty($error)): ?>
            <div class="mb-4 rounded-xl bg-rose-50 text-rose-700 text-sm px-4 py-3">
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form action="/login" method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1" for="email">E-mail</label>
                <input id="email" name="email" type="email" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1" for="password">Senha</label>
                <input id="password" name="password" type="password" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
            </div>
            <button type="submit" class="w-full rounded-xl bg-indigo-600 text-white font-semibold px-4 py-2 shadow hover:bg-indigo-500">
                Entrar
            </button>
        </form>
    </div>
</div>
