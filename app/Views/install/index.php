<div class="min-h-screen bg-slate-50 flex items-center justify-center px-4">
    <div class="w-full max-w-4xl bg-white rounded-2xl shadow-lg p-8">
        <div class="flex items-center gap-3 mb-6">
            <div class="h-12 w-12 rounded-xl bg-indigo-600 text-white flex items-center justify-center">
                <i class="fa-solid fa-gear"></i>
            </div>
            <div>
                <h2 class="text-xl font-semibold text-slate-900">Instalação CodeChat Manager</h2>
                <p class="text-sm text-slate-500">Complete as etapas para configurar o sistema.</p>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-[1fr,280px]">
            <form id="install-form" class="space-y-4">
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="text-sm text-slate-600">Host MySQL</label>
                        <input name="db_host" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2" value="localhost" required>
                    </div>
                    <div>
                        <label class="text-sm text-slate-600">Database</label>
                        <input name="db_name" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2" value="codechat_manager" required>
                    </div>
                    <div>
                        <label class="text-sm text-slate-600">Usuário</label>
                        <input name="db_user" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2" required>
                    </div>
                    <div>
                        <label class="text-sm text-slate-600">Senha</label>
                        <input name="db_pass" type="password" class="w-full rounded-xl border border-slate-200 px-3 py-2">
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="text-sm text-slate-600">Charset</label>
                        <input name="db_charset" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2" value="utf8mb4">
                    </div>
                    <div>
                        <label class="text-sm text-slate-600">Evolution API URL</label>
                        <input name="api_url" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2" value="http://localhost:8084">
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="text-sm text-slate-600">Timeout API (s)</label>
                        <input name="api_timeout" type="number" class="w-full rounded-xl border border-slate-200 px-3 py-2" value="20">
                    </div>
                    <div>
                        <label class="text-sm text-slate-600">Integração</label>
                        <select name="api_integration" class="w-full rounded-xl border border-slate-200 px-3 py-2">
                            <option value="WHATSAPP-BAILEYS" selected>WHATSAPP-BAILEYS</option>
                            <option value="WHATSAPP-BUSINESS">WHATSAPP-BUSINESS</option>
                            <option value="EVOLUTION">EVOLUTION</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-sm text-slate-600">Global API Key</label>
                        <input name="api_key" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2" placeholder="Cole a chave global da API">
                    </div>
                    <div class="flex items-center gap-2 mt-6">
                        <input id="seed" name="seed" type="checkbox" class="rounded text-indigo-600" checked>
                        <label for="seed" class="text-sm text-slate-600">Importar seeds (usuário admin)</label>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-500">
                        Executar instalação
                    </button>
                </div>
            </form>

            <aside class="rounded-2xl bg-slate-50 border border-slate-100 p-5">
                <h3 class="text-sm font-semibold text-slate-700 mb-4">Etapas</h3>
                <ol class="space-y-3 text-sm text-slate-600" id="install-steps">
                    <li data-step="connect" class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-slate-300"></span>
                        Conectar ao MySQL
                    </li>
                    <li data-step="create-db" class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-slate-300"></span>
                        Criar banco
                    </li>
                    <li data-step="write-env" class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-slate-300"></span>
                        Gerar .env
                    </li>
                    <li data-step="create-tables" class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-slate-300"></span>
                        Criar tabelas
                    </li>
                    <li data-step="seed" class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-slate-300"></span>
                        Importar seeds (opcional)
                    </li>
                </ol>
            </aside>
        </div>
    </div>
</div>

<div id="loading-bar" class="fixed top-0 left-0 w-full h-1 bg-indigo-100 hidden">
    <div class="h-full bg-indigo-600 animate-pulse" style="width: 100%;"></div>
</div>

<script src="/assets/js/install.js"></script>
