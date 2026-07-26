import { useEffect, useState, useCallback } from 'react';
import { ReactFlowProvider } from '@xyflow/react';
import { api } from './api.js';
import WorkflowSidebar from './components/WorkflowSidebar.jsx';
import FlowCanvas from './components/FlowCanvas.jsx';
import StepEditorPanel from './components/StepEditorPanel.jsx';
import WorkflowPanel from './components/WorkflowPanel.jsx';

export default function App() {
    const [workflows, setWorkflows] = useState([]);
    const [stepTypes, setStepTypes] = useState([]);
    const [selectedWorkflow, setSelectedWorkflow] = useState(null);
    const [steps, setSteps] = useState([]);
    const [selectedStepId, setSelectedStepId] = useState(null);
    const [error, setError] = useState('');

    useEffect(() => {
        api.listWorkflows().then(setWorkflows).catch((e) => setError(e.message));
        api.stepTypes().then(setStepTypes).catch((e) => setError(e.message));
    }, []);

    const selectWorkflow = useCallback((workflow) => {
        setSelectedStepId(null);
        api.getWorkflow(workflow.id)
            .then((full) => {
                setSelectedWorkflow(full);
                setSteps(full.steps ?? []);
            })
            .catch((e) => setError(e.message));
    }, []);

    async function handleCreateWorkflow(payload) {
        try {
            const workflow = await api.createWorkflow(payload);
            setWorkflows((prev) => [workflow, ...prev]);
            selectWorkflow(workflow);
        } catch (e) {
            setError(e.message);
        }
    }

    async function handleSaveWorkflow(id, payload) {
        try {
            const updated = await api.updateWorkflow(id, payload);
            setSelectedWorkflow((prev) => ({ ...prev, ...updated }));
            setWorkflows((prev) => prev.map((w) => (w.id === id ? { ...w, ...updated } : w)));
        } catch (e) {
            setError(e.message);
        }
    }

    async function handleDeleteWorkflow(id) {
        if (! confirm('Delete this workflow and all its steps/run history? This cannot be undone.')) return;

        try {
            await api.deleteWorkflow(id);
            setWorkflows((prev) => prev.filter((w) => w.id !== id));
            setSelectedWorkflow(null);
            setSteps([]);
        } catch (e) {
            setError(e.message);
        }
    }

    async function handleAddStep(type) {
        if (! selectedWorkflow) return;

        try {
            const step = await api.createStep(selectedWorkflow.id, {
                key: `${type}_${steps.length + 1}`,
                type,
                config: {},
            });
            setSteps((prev) => [...prev, step]);
        } catch (e) {
            setError(e.message);
        }
    }

    async function handleSaveStep(id, payload) {
        try {
            const updated = await api.updateStep(id, payload);
            setSteps((prev) => prev.map((s) => (s.id === id ? updated : s)));
        } catch (e) {
            setError(e.message);
        }
    }

    async function handleDeleteStep(id) {
        if (! confirm('Delete this step?')) return;

        try {
            await api.deleteStep(id);
            setSteps((prev) => prev.filter((s) => s.id !== id));
            if (selectedStepId === id) setSelectedStepId(null);
        } catch (e) {
            setError(e.message);
        }
    }

    async function handleReorderStep(id, direction) {
        const sorted = [...steps].sort((a, b) => a.order - b.order);
        const index = sorted.findIndex((s) => s.id === id);
        const swapWith = index + direction;

        if (swapWith < 0 || swapWith >= sorted.length) return;

        [sorted[index], sorted[swapWith]] = [sorted[swapWith], sorted[index]];
        const order = sorted.map((s) => s.id);

        try {
            const updated = await api.reorderSteps(selectedWorkflow.id, order);
            setSteps(updated.steps ?? []);
        } catch (e) {
            setError(e.message);
        }
    }

    async function handleNodeMoved(id, position) {
        try {
            const updated = await api.updateStep(id, { canvas_position: position });
            setSteps((prev) => prev.map((s) => (s.id === id ? updated : s)));
        } catch (e) {
            setError(e.message);
        }
    }

    function handleLogout() {
        const url = document.querySelector('meta[name="logout-url"]')?.content;
        const token = document.querySelector('meta[name="csrf-token"]')?.content;

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = url;
        form.innerHTML = `<input type="hidden" name="_token" value="${token}">`;
        document.body.appendChild(form);
        form.submit();
    }

    const selectedStep = steps.find((s) => s.id === selectedStepId) ?? null;

    return (
        <div className="ws-app">
            <div className="ws-header">
                <h1>Workflow Studio {selectedWorkflow ? `— ${selectedWorkflow.name}` : ''}</h1>
                <button className="ws-btn secondary" onClick={handleLogout}>Log out</button>
            </div>

            {error && <div className="ws-error" style={{ margin: 12 }}>{error}</div>}

            <div className="ws-body">
                <WorkflowSidebar
                    workflows={workflows}
                    selectedWorkflow={selectedWorkflow}
                    onSelectWorkflow={selectWorkflow}
                    onCreateWorkflow={handleCreateWorkflow}
                    steps={steps}
                    selectedStepId={selectedStepId}
                    onSelectStep={setSelectedStepId}
                    onDeleteStep={handleDeleteStep}
                    onReorderStep={handleReorderStep}
                    stepTypes={stepTypes}
                    onAddStep={handleAddStep}
                />

                <div className="ws-canvas">
                    {selectedWorkflow ? (
                        <ReactFlowProvider>
                            <FlowCanvas
                                steps={steps}
                                selectedStepId={selectedStepId}
                                onSelectStep={setSelectedStepId}
                                onDeselect={() => setSelectedStepId(null)}
                                onNodeMoved={handleNodeMoved}
                            />
                        </ReactFlowProvider>
                    ) : (
                        <div style={{ display: 'flex', height: '100%', alignItems: 'center', justifyContent: 'center', color: '#64748b' }}>
                            Select or create a workflow to start building.
                        </div>
                    )}
                </div>

                {selectedWorkflow && (
                    <div className="ws-panel">
                        {selectedStep ? (
                            <StepEditorPanel step={selectedStep} onSave={handleSaveStep} onDelete={handleDeleteStep} />
                        ) : (
                            <WorkflowPanel
                                workflow={selectedWorkflow}
                                onSave={handleSaveWorkflow}
                                onDelete={handleDeleteWorkflow}
                                onTestRun={api.testRun}
                            />
                        )}
                    </div>
                )}
            </div>
        </div>
    );
}
