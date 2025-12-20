<section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 mb-6">
    <div class="rounded-2xl bg-white p-5 shadow-sm border border-slate-100">
        <p class="text-sm text-slate-500">Instâncias cadastradas</p>
        <p class="text-2xl font-semibold text-slate-900" id="total-instances"><?= count($instances ?? []) ?></p>
    </div>
    <div class="rounded-2xl bg-white p-5 shadow-sm border border-slate-100">
        <p class="text-sm text-slate-500">Instâncias conectadas</p>
        <p class="text-2xl font-semibold text-emerald-600" id="connected-instances"><?= $connectedCount ?? 0 ?></p>
    </div>
    <div class="rounded-2xl bg-white p-5 shadow-sm border border-slate-100">
        <p class="text-sm text-slate-500">Mensagens não lidas</p>
        <p class="text-2xl font-semibold text-indigo-600" id="total-unread">0</p>
    </div>
</section>

<section class="rounded-2xl bg-white shadow-sm border border-slate-100 p-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-6">
        <div>
            <h2 class="text-lg font-semibold text-slate-900">Instâncias</h2>
            <p class="text-sm text-slate-500">Gerencie conexões e monitore status em tempo real.</p>
        </div>
        <button id="open-create" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-500">
            <i class="fa-solid fa-plus"></i>
            Nova instância
        </button>
    </div>

    <div id="instances-grid" class="grid gap-4 md:grid-cols-2 xl:grid-cols-3"></div>
</section>

<div id="loading-bar" class="fixed top-0 left-0 w-full h-1 bg-indigo-100 hidden">
    <div class="h-full bg-indigo-600 animate-pulse" style="width: 100%;"></div>
</div>

<!-- Modal criar instância -->
<div id="create-modal" class="fixed inset-0 bg-slate-900/40 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-lg w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-slate-900">Nova instância</h3>
            <button class="text-slate-500 hover:text-slate-700" data-close-modal>
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="space-y-3">
            <label class="text-sm text-slate-600">Nome da instância</label>
            <input id="instance-name" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="ex: vendas-suporte">
        </div>
        <div class="flex justify-end gap-3 mt-6">
            <button class="px-4 py-2 rounded-xl text-sm text-slate-600 hover:bg-slate-100" data-close-modal>Cancelar</button>
            <button id="create-instance" class="px-4 py-2 rounded-xl text-sm font-semibold bg-indigo-600 text-white hover:bg-indigo-500">Criar</button>
        </div>
    </div>
</div>

<!-- Modal QR Code -->
<div id="qr-modal" class="fixed inset-0 bg-slate-900/40 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-lg w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-slate-900">Conectar instância</h3>
            <button class="text-slate-500 hover:text-slate-700" data-close-modal>
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="text-center space-y-4">
            <div id="qr-placeholder" class="h-56 w-56 mx-auto bg-slate-100 rounded-xl flex items-center justify-center">
                <span class="text-sm text-slate-500">Aguardando QR Code...</span>
            </div>
            <p class="text-sm text-slate-500">Escaneie o QR Code para conectar. Ele será atualizado automaticamente.</p>
        </div>
    </div>
</div>

<!-- Modal teste mensagem -->
<div id="test-modal" class="fixed inset-0 bg-slate-900/40 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-lg w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-slate-900">Enviar mensagem de teste</h3>
            <button class="text-slate-500 hover:text-slate-700" data-close-modal>
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="space-y-3">
            <div>
                <label class="text-sm text-slate-600">Número (com DDI)</label>
                <input id="test-number" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="551199999999">
            </div>
            <div>
                <label class="text-sm text-slate-600">Mensagem</label>
                <textarea id="test-message" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" rows="3" placeholder="Olá! Mensagem de teste."></textarea>
            </div>
        </div>
        <div class="flex justify-end gap-3 mt-6">
            <button class="px-4 py-2 rounded-xl text-sm text-slate-600 hover:bg-slate-100" data-close-modal>Cancelar</button>
            <button id="send-test" class="px-4 py-2 rounded-xl text-sm font-semibold bg-emerald-600 text-white hover:bg-emerald-500">Enviar</button>
        </div>
    </div>
</div>
