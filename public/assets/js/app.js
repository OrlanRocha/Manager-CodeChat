const loadingBar = document.getElementById('loading-bar');

const showLoading = () => {
    if (loadingBar) {
        loadingBar.classList.remove('hidden');
    }
};

const hideLoading = () => {
    if (loadingBar) {
        loadingBar.classList.add('hidden');
    }
};

const toast = (message, color = '#4f46e5') => {
    if (!window.Toastify) {
        alert(message);
        return;
    }
    Toastify({
        text: message,
        duration: 3000,
        gravity: 'top',
        position: 'right',
        style: {
            background: color,
        },
    }).showToast();
};

const fetchJson = async (url, options = {}) => {
    showLoading();
    try {
        const response = await fetch(url, options);
        const data = await response.json();
        if (!response.ok) {
            throw new Error(data.message || 'Erro inesperado.');
        }
        return data;
    } finally {
        hideLoading();
    }
};

const instancesGrid = document.getElementById('instances-grid');
const totalInstancesEl = document.getElementById('total-instances');
const connectedInstancesEl = document.getElementById('connected-instances');
const totalUnreadEl = document.getElementById('total-unread');

let qrInterval = null;
let statusInterval = null;
let unreadInterval = null;
let activeInstanceId = null;

const renderInstances = (instances = []) => {
    if (!instancesGrid) return;
    instancesGrid.innerHTML = '';

    let connected = 0;
    let totalUnread = 0;

    instances.forEach((instance) => {
        const statusColor = instance.status === 'connected' ? 'bg-emerald-500' : 'bg-rose-500';
        if (instance.status === 'connected') {
            connected += 1;
        }

        const description = instance.description ? `<p class="text-xs text-slate-400 mt-1">${instance.description}</p>` : '';
        const card = document.createElement('div');
        card.className = 'rounded-2xl border border-slate-100 bg-slate-50 p-5 shadow-sm flex flex-col gap-4';
        card.innerHTML = `
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-slate-900">${instance.instance_name}</h3>
                    ${description}
                    <div class="flex items-center gap-2 text-sm text-slate-500">
                        <span class="h-2 w-2 rounded-full ${statusColor}"></span>
                        <span class="instance-status" data-id="${instance.id}">${instance.status}</span>
                    </div>
                </div>
                <span class="text-xs bg-white rounded-full px-3 py-1 text-slate-500">ID #${instance.id}</span>
            </div>
            <div class="flex items-center justify-between text-sm">
                <div class="text-slate-500">Não lidas</div>
                <div class="font-semibold text-indigo-600" data-unread="${instance.id}">0</div>
            </div>
            <div class="flex flex-wrap gap-2">
                <button class="connect-instance flex-1 rounded-xl bg-indigo-600 text-white px-3 py-2 text-sm font-semibold hover:bg-indigo-500" data-id="${instance.id}">
                    Conectar
                </button>
                <button class="test-message flex-1 rounded-xl bg-emerald-600 text-white px-3 py-2 text-sm font-semibold hover:bg-emerald-500" data-id="${instance.id}">
                    Testar
                </button>
                <button class="delete-instance flex-1 rounded-xl bg-rose-600 text-white px-3 py-2 text-sm font-semibold hover:bg-rose-500" data-id="${instance.id}">
                    Excluir
                </button>
            </div>
        `;
        instancesGrid.appendChild(card);
    });

    if (totalInstancesEl) totalInstancesEl.textContent = instances.length;
    if (connectedInstancesEl) connectedInstancesEl.textContent = connected;
    if (totalUnreadEl) totalUnreadEl.textContent = totalUnread;
};

const loadInstances = async () => {
    try {
        const response = await fetchJson('/api/instances');
        renderInstances(response.data);
    } catch (error) {
        toast(error.message, '#ef4444');
    }
};

const openModal = (modal) => {
    if (!modal) return;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
};

const closeModal = (modal) => {
    if (!modal) return;
    modal.classList.add('hidden');
    modal.classList.remove('flex');
};

const bindModalClosers = () => {
    document.querySelectorAll('[data-close-modal]').forEach((button) => {
        button.addEventListener('click', () => {
            const modal = button.closest('.fixed');
            closeModal(modal);
            if (modal === qrModal) {
                clearInterval(qrInterval);
            }
        });
    });
};

const createModal = document.getElementById('create-modal');
const qrModal = document.getElementById('qr-modal');
const testModal = document.getElementById('test-modal');
const qrPlaceholder = document.getElementById('qr-placeholder');

const fetchQrCode = async (id) => {
    try {
        const response = await fetchJson(`/api/instances/${id}/connect`);
        const qr = response.data.qr;
        if (qr) {
            qrPlaceholder.innerHTML = `<img src="${qr}" alt="QR Code" class="h-56 w-56 rounded-xl object-contain">`;
        } else {
            qrPlaceholder.innerHTML = '<span class="text-sm text-slate-500">QR Code indisponível.</span>';
        }
    } catch (error) {
        toast(error.message, '#ef4444');
    }
};

const startQrPolling = (id) => {
    clearInterval(qrInterval);
    fetchQrCode(id);
    qrInterval = setInterval(() => fetchQrCode(id), 10000);
};

const startStatusPolling = () => {
    clearInterval(statusInterval);
    statusInterval = setInterval(async () => {
        const statusEls = document.querySelectorAll('.instance-status');
        for (const statusEl of statusEls) {
            const id = statusEl.dataset.id;
            try {
                const response = await fetchJson(`/api/instances/${id}/status`);
                const status = response.data.status;
                statusEl.textContent = status;
                const dot = statusEl.previousElementSibling;
                if (dot) {
                    dot.className = `h-2 w-2 rounded-full ${status === 'connected' ? 'bg-emerald-500' : 'bg-rose-500'}`;
                }
                if (status === 'connected' && qrModal && !qrModal.classList.contains('hidden') && activeInstanceId === id) {
                    closeModal(qrModal);
                    clearInterval(qrInterval);
                    toast('Conectado com sucesso!', '#10b981');
                }
            } catch (error) {
                console.warn(error.message);
            }
        }
    }, 10000);
};

const startUnreadPolling = () => {
    clearInterval(unreadInterval);
    unreadInterval = setInterval(async () => {
        const unreadEls = document.querySelectorAll('[data-unread]');
        let total = 0;
        for (const unreadEl of unreadEls) {
            const id = unreadEl.dataset.unread;
            try {
                const response = await fetchJson(`/api/instances/${id}/unread`);
                const count = response.data.count || 0;
                unreadEl.textContent = count;
                total += count;
            } catch (error) {
                console.warn(error.message);
            }
        }
        if (totalUnreadEl) totalUnreadEl.textContent = total;
    }, 12000);
};

const handleInstanceActions = () => {
    if (!instancesGrid) return;
    instancesGrid.addEventListener('click', async (event) => {
        const target = event.target.closest('button');
        if (!target) return;

        const id = target.dataset.id;
        if (!id) return;

        if (target.classList.contains('connect-instance')) {
            activeInstanceId = id;
            qrPlaceholder.innerHTML = '<span class="text-sm text-slate-500">Carregando QR Code...</span>';
            openModal(qrModal);
            startQrPolling(id);
            return;
        }

        if (target.classList.contains('test-message')) {
            activeInstanceId = id;
            openModal(testModal);
            return;
        }

        if (target.classList.contains('delete-instance')) {
            const result = await Swal.fire({
                title: 'Remover instância?',
                text: 'Essa ação não poderá ser desfeita.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Excluir',
            });

            if (!result.isConfirmed) return;

            try {
                const formData = new FormData();
                const response = await fetchJson(`/api/instances/${id}/delete`, {
                    method: 'POST',
                    body: formData,
                });
                toast(response.message || 'Instância removida.');
                await loadInstances();
            } catch (error) {
                toast(error.message, '#ef4444');
            }
        }
    });
};

const bindCreateInstance = () => {
    const openCreateBtn = document.getElementById('open-create');
    const createBtn = document.getElementById('create-instance');
    const nameInput = document.getElementById('instance-name');
    const descriptionInput = document.getElementById('instance-description');

    if (openCreateBtn) {
        openCreateBtn.addEventListener('click', () => {
            if (nameInput) nameInput.value = '';
            if (descriptionInput) descriptionInput.value = '';
            openModal(createModal);
        });
    }

    if (createBtn) {
        createBtn.addEventListener('click', async () => {
            const name = nameInput?.value.trim();
            if (!name) {
                toast('Informe o nome da instância.', '#ef4444');
                return;
            }

            const formData = new FormData();
            formData.append('name', name);
            const description = descriptionInput?.value.trim();
            if (description) {
                formData.append('description', description);
            }

            try {
                const response = await fetchJson('/api/instances', {
                    method: 'POST',
                    body: formData,
                });
                toast(response.message || 'Instância criada.');
                closeModal(createModal);
                await loadInstances();
            } catch (error) {
                toast(error.message, '#ef4444');
            }
        });
    }
};

const bindTestMessage = () => {
    const sendBtn = document.getElementById('send-test');
    const numberInput = document.getElementById('test-number');
    const messageInput = document.getElementById('test-message');

    if (!sendBtn) return;

    sendBtn.addEventListener('click', async () => {
        if (!activeInstanceId) return;
        const number = numberInput?.value.trim();
        const message = messageInput?.value.trim();

        if (!number || !message) {
            toast('Preencha número e mensagem.', '#ef4444');
            return;
        }

        const formData = new FormData();
        formData.append('to', number);
        formData.append('message', message);

        try {
            const response = await fetchJson(`/api/instances/${activeInstanceId}/test-message`, {
                method: 'POST',
                body: formData,
            });
            toast(response.message || 'Mensagem enviada.', '#10b981');
            closeModal(testModal);
        } catch (error) {
            toast(error.message, '#ef4444');
        }
    });
};

const init = () => {
    if (!instancesGrid) return;
    bindModalClosers();
    bindCreateInstance();
    handleInstanceActions();
    bindTestMessage();
    loadInstances();
    startStatusPolling();
    startUnreadPolling();
};

document.addEventListener('DOMContentLoaded', init);
