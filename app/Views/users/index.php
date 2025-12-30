<section class="rounded-2xl bg-white shadow-sm border border-slate-100 p-6">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-lg font-semibold text-slate-900">Usuários</h2>
            <p class="text-sm text-slate-500">Gerencie criação, bloqueio e permissões.</p>
        </div>
        <button id="open-user-modal" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white">Novo usuário</button>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="text-slate-500 border-b">
                <tr>
                    <th class="text-left py-2 px-3">Nome</th>
                    <th class="text-left py-2 px-3">E-mail</th>
                    <th class="text-left py-2 px-3">Perfil</th>
                    <th class="text-left py-2 px-3">Status</th>
                    <th class="text-left py-2 px-3">Criado em</th>
                    <th class="text-left py-2 px-3">Atualizado em</th>
                    <th class="text-left py-2 px-3"></th>
                </tr>
            </thead>
            <tbody id="users-table" class="divide-y"></tbody>
        </table>
    </div>
</section>

<div id="user-modal" class="fixed inset-0 bg-slate-900/40 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-lg w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-slate-900" id="user-modal-title">Novo usuário</h3>
            <button class="text-slate-500 hover:text-slate-700" data-close-modal>
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form id="user-form" class="space-y-3">
            <input type="hidden" name="id" value="">
            <div>
                <label class="text-sm text-slate-600">Nome</label>
                <input name="name" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2" required>
            </div>
            <div>
                <label class="text-sm text-slate-600">E-mail</label>
                <input name="email" type="email" class="w-full rounded-xl border border-slate-200 px-3 py-2" required>
            </div>
            <div>
                <label class="text-sm text-slate-600">Perfil</label>
                <select name="role" class="w-full rounded-xl border border-slate-200 px-3 py-2">
                    <option value="admin">Admin</option>
                    <option value="user">Usuário</option>
                </select>
            </div>
            <div>
                <label class="text-sm text-slate-600">Status</label>
                <select name="status" class="w-full rounded-xl border border-slate-200 px-3 py-2">
                    <option value="active">Ativo</option>
                    <option value="blocked">Bloqueado</option>
                </select>
            </div>
            <div id="password-field">
                <label class="text-sm text-slate-600">Senha</label>
                <input name="password" type="password" class="w-full rounded-xl border border-slate-200 px-3 py-2">
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button class="px-4 py-2 rounded-xl text-sm text-slate-600 hover:bg-slate-100" data-close-modal>Cancelar</button>
                <button type="submit" class="px-4 py-2 rounded-xl text-sm font-semibold bg-indigo-600 text-white hover:bg-indigo-500">Salvar</button>
            </div>
        </form>
    </div>
</div>

<div id="password-modal" class="fixed inset-0 bg-slate-900/40 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-lg w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-slate-900">Alterar senha</h3>
            <button class="text-slate-500 hover:text-slate-700" data-close-modal>
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form id="password-user-form" class="space-y-3">
            <input type="hidden" name="id" value="">
            <div>
                <label class="text-sm text-slate-600">Nova senha</label>
                <input name="password" type="password" class="w-full rounded-xl border border-slate-200 px-3 py-2" required>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button class="px-4 py-2 rounded-xl text-sm text-slate-600 hover:bg-slate-100" data-close-modal>Cancelar</button>
                <button type="submit" class="px-4 py-2 rounded-xl text-sm font-semibold bg-emerald-600 text-white hover:bg-emerald-500">Atualizar</button>
            </div>
        </form>
    </div>
</div>
