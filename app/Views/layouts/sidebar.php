<aside class="hidden lg:flex lg:flex-col lg:w-64 bg-white border-r border-slate-200 p-6">
    <div class="flex items-center gap-3 mb-8">
        <div class="h-10 w-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center shadow">
            <i class="fa-solid fa-comments"></i>
        </div>
        <div>
            <p class="text-sm font-semibold text-slate-900">CodeChat Manager</p>
            <p class="text-xs text-slate-500">Painel SaaS</p>
        </div>
    </div>

    <nav class="flex-1 space-y-2">
        <a href="/dashboard" class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
            <i class="fa-solid fa-chart-line"></i>
            Dashboard
        </a>
        <a href="/logs" class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
            <i class="fa-solid fa-clipboard-list"></i>
            Logs
        </a>
    </nav>

    <div class="mt-auto rounded-xl bg-slate-900 text-white p-4 shadow">
        <p class="text-xs uppercase tracking-wide text-slate-300">Status</p>
        <p class="text-sm font-semibold">Conecte e gerencie instâncias</p>
    </div>
</aside>

<div class="lg:hidden w-full bg-white border-b border-slate-200 px-4 py-3 flex items-center justify-between">
    <div class="flex items-center gap-2">
        <div class="h-9 w-9 rounded-xl bg-indigo-600 text-white flex items-center justify-center">
            <i class="fa-solid fa-comments"></i>
        </div>
        <span class="text-sm font-semibold">CodeChat Manager</span>
    </div>
    <a href="/dashboard" class="text-sm font-medium text-indigo-600">Dashboard</a>
</div>
