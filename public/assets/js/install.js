const installForm = document.getElementById('install-form');
const stepsList = document.getElementById('install-steps');
const loadingBar = document.getElementById('loading-bar');

const showLoading = () => loadingBar?.classList.remove('hidden');
const hideLoading = () => loadingBar?.classList.add('hidden');

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
        style: { background: color },
    }).showToast();
};

const setStepStatus = (step, status) => {
    const item = stepsList?.querySelector(`[data-step="${step}"] span`);
    if (!item) return;

    const colors = {
        pending: 'bg-slate-300',
        running: 'bg-indigo-500',
        done: 'bg-emerald-500',
        error: 'bg-rose-500',
    };

    item.className = `h-2 w-2 rounded-full ${colors[status] || colors.pending}`;
};

const postStep = async (url, formData) => {
    showLoading();
    try {
        const response = await fetch(url, { method: 'POST', body: formData });
        const data = await response.json();
        if (!response.ok || !data.success) {
            throw new Error(data.message || 'Erro na instalação.');
        }
        return data;
    } finally {
        hideLoading();
    }
};

const runSteps = async (formData, includeSeed) => {
    const steps = [
        { key: 'connect', url: '/install/connect' },
        { key: 'create-db', url: '/install/create-db' },
        { key: 'write-env', url: '/install/write-env' },
        { key: 'create-tables', url: '/install/create-tables' },
    ];

    if (includeSeed) {
        steps.push({ key: 'seed', url: '/install/seed' });
    }

    for (const step of steps) {
        setStepStatus(step.key, 'running');
        try {
            const data = await postStep(step.url, formData);
            toast(data.message, '#10b981');
            setStepStatus(step.key, 'done');
        } catch (error) {
            setStepStatus(step.key, 'error');
            toast(error.message, '#ef4444');
            throw error;
        }
    }
};

installForm?.addEventListener('submit', async (event) => {
    event.preventDefault();

    const formData = new FormData(installForm);
    const includeSeed = formData.get('seed') === 'on';

    ['connect', 'create-db', 'write-env', 'create-tables', 'seed'].forEach((step) =>
        setStepStatus(step, 'pending')
    );

    try {
        await runSteps(formData, includeSeed);
        toast('Instalação concluída! Redirecionando...', '#4f46e5');
        setTimeout(() => {
            window.location.href = '/login';
        }, 1500);
    } catch (error) {
        console.error(error);
    }
});
