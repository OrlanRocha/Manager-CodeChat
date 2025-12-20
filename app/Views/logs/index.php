<section class="rounded-2xl bg-white shadow-sm border border-slate-100 p-6">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-lg font-semibold text-slate-900">Histórico de Logs</h2>
            <p class="text-sm text-slate-500">Solicitações registradas no banco e em <code>storage/logs/app.log</code>.</p>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="text-slate-500 border-b">
                <tr>
                    <th class="text-left py-2 px-3">Data</th>
                    <th class="text-left py-2 px-3">Nível</th>
                    <th class="text-left py-2 px-3">Contexto</th>
                    <th class="text-left py-2 px-3">Usuário</th>
                    <th class="text-left py-2 px-3">IP</th>
                    <th class="text-left py-2 px-3">Mensagem</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td class="py-2 px-3 text-slate-600"><?= htmlspecialchars($log['created_at'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="py-2 px-3">
                            <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold <?= $log['level'] === 'error' ? 'bg-rose-100 text-rose-600' : 'bg-emerald-100 text-emerald-600' ?>">
                                <?= htmlspecialchars($log['level'], ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        </td>
                        <td class="py-2 px-3 text-slate-600"><?= htmlspecialchars($log['context'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="py-2 px-3 text-slate-600"><?= htmlspecialchars($log['user_name'] ?? 'Sistema', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="py-2 px-3 text-slate-600"><?= htmlspecialchars($log['ip_address'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="py-2 px-3 text-slate-700"><?= htmlspecialchars($log['message'], ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($logs)): ?>
                    <tr>
                        <td colspan="6" class="py-6 text-center text-slate-500">Nenhum log registrado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
