<section class="grid gap-6 lg:grid-cols-2">
    <div class="rounded-2xl bg-white shadow-sm border border-slate-100 p-6">
        <h2 class="text-lg font-semibold text-slate-900 mb-4">Dados da conta</h2>
        <form id="profile-form" class="space-y-4">
            <div>
                <label class="text-sm text-slate-600">Nome</label>
                <input name="name" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2" value="<?= htmlspecialchars($user['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div>
                <label class="text-sm text-slate-600">E-mail</label>
                <input name="email" type="email" class="w-full rounded-xl border border-slate-200 px-3 py-2" value="<?= htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white">Salvar</button>
        </form>
    </div>

    <div class="rounded-2xl bg-white shadow-sm border border-slate-100 p-6">
        <h2 class="text-lg font-semibold text-slate-900 mb-4">Alterar senha</h2>
        <form id="password-form" class="space-y-4">
            <div>
                <label class="text-sm text-slate-600">Nova senha</label>
                <input name="password" type="password" class="w-full rounded-xl border border-slate-200 px-3 py-2">
            </div>
            <button type="submit" class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white">Atualizar senha</button>
        </form>
    </div>
</section>
