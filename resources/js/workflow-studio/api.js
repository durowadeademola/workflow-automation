const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.content;

async function request(path, options = {}) {
    const res = await fetch(`/workflow-studio/api${path}`, {
        credentials: 'same-origin',
        ...options,
        headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            'X-CSRF-TOKEN': csrfToken(),
            ...(options.headers || {}),
        },
        body: options.body !== undefined ? JSON.stringify(options.body) : undefined,
    });

    if (res.status === 401 || res.status === 419) {
        window.location.href = '/workflow-studio/login';
        return null;
    }

    const data = await res.json().catch(() => null);

    if (!res.ok) {
        const message = data?.errors
            ? Object.values(data.errors).flat().join(' ')
            : data?.message || `Request failed (${res.status})`;

        throw new Error(message);
    }

    return data;
}

export const api = {
    listWorkflows: () => request('/workflows'),
    createWorkflow: (payload) => request('/workflows', { method: 'POST', body: payload }),
    getWorkflow: (id) => request(`/workflows/${id}`),
    updateWorkflow: (id, payload) => request(`/workflows/${id}`, { method: 'PUT', body: payload }),
    deleteWorkflow: (id) => request(`/workflows/${id}`, { method: 'DELETE' }),
    testRun: (id, payload) => request(`/workflows/${id}/test-run`, { method: 'POST', body: payload }),
    stepTypes: () => request('/step-types'),
    createStep: (workflowId, payload) => request(`/workflows/${workflowId}/steps`, { method: 'POST', body: payload }),
    updateStep: (id, payload) => request(`/steps/${id}`, { method: 'PUT', body: payload }),
    deleteStep: (id) => request(`/steps/${id}`, { method: 'DELETE' }),
    reorderSteps: (workflowId, order) => request(`/workflows/${workflowId}/steps/reorder`, { method: 'POST', body: { order } }),
};
