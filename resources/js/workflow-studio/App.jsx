import { useCallback, useEffect, useState } from 'react';
import { api } from './api.js';
import WorkflowList from './components/WorkflowList.jsx';
import BuilderCard from './components/BuilderCard.jsx';
import WorkflowPanel from './components/WorkflowPanel.jsx';
import StepPanel from './components/StepPanel.jsx';

export default function App() {
    const [workflows, setWorkflows] = useState([]);
    const [stepTypes, setStepTypes] = useState([]);
    const [workflow, setWorkflow] = useState(null);
    const [steps, setSteps] = useState([]);
    // A step id, 'trigger' (the trigger node), or null (nothing selected).
    const [selectedStepId, setSelectedStepId] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState('');

    const fail = (e) => setError(e.message);

    const openWorkflow = useCallback(async (id) => {
        setSelectedStepId(null);
        try {
            const full = await api.getWorkflow(id);
            setWorkflow(full);
            setSteps(full.steps ?? []);
        } catch (e) {
            fail(e);
        }
    }, []);

    useEffect(() => {
        Promise.all([api.listWorkflows(), api.stepTypes()])
            .then(([list, types]) => {
                setWorkflows(list ?? []);
                setStepTypes(types ?? []);
                if (list?.length) openWorkflow(list[0].id);
            })
            .catch(fail)
            .finally(() => setLoading(false));
    }, [openWorkflow]);

    // Keeps the list's step/run counts in line with the open workflow.
    const syncListCounts = (id, patch) => setWorkflows((prev) => prev.map((w) => (w.id === id ? { ...w, ...patch } : w)));

    async function handleCreateWorkflow(payload) {
        try {
            const created = await api.createWorkflow(payload);
            setWorkflows((prev) => [{ ...created, steps_count: 0, runs_count: 0 }, ...prev]);
            openWorkflow(created.id);
            return true;
        } catch (e) {
            fail(e);
            return false;
        }
    }

    async function handleSaveWorkflow(id, payload) {
        try {
            const updated = await api.updateWorkflow(id, payload);
            setWorkflow((prev) => ({ ...prev, ...updated }));
            syncListCounts(id, updated);
        } catch (e) {
            fail(e);
        }
    }

    async function handleDeleteWorkflow(id) {
        if (!confirm('Delete this workflow and all its steps and run history? This cannot be undone.')) return;

        try {
            await api.deleteWorkflow(id);
            const remaining = workflows.filter((w) => w.id !== id);
            setWorkflows(remaining);
            setWorkflow(null);
            setSteps([]);
            if (remaining.length) openWorkflow(remaining[0].id);
        } catch (e) {
            fail(e);
        }
    }

    async function handleTestRun(id, payload) {
        const run = await api.testRun(id, payload);
        // Pick up the new run in the stats strip and the list.
        const fresh = await api.getWorkflow(id);
        setWorkflow((prev) => ({ ...prev, runs_count: fresh.runs_count, latest_run: fresh.latest_run }));
        syncListCounts(id, { runs_count: fresh.runs_count });
        return run;
    }

    async function handleAddStep(type) {
        if (!workflow) return;

        try {
            const existing = new Set(steps.map((s) => s.key));
            let n = steps.length + 1;
            while (existing.has(`${type}_${n}`)) n += 1;

            const step = await api.createStep(workflow.id, { key: `${type}_${n}`, type, config: {} });
            setSteps((prev) => [...prev, step]);
            syncListCounts(workflow.id, { steps_count: steps.length + 1 });
            setSelectedStepId(step.id);
        } catch (e) {
            fail(e);
        }
    }

    async function handleSaveStep(id, payload) {
        try {
            const updated = await api.updateStep(id, payload);
            setSteps((prev) => prev.map((s) => (s.id === id ? updated : s)));
        } catch (e) {
            fail(e);
        }
    }

    async function handleDeleteStep(id) {
        if (!confirm('Delete this step?')) return;

        try {
            await api.deleteStep(id);
            setSteps((prev) => prev.filter((s) => s.id !== id));
            syncListCounts(workflow.id, { steps_count: steps.length - 1 });
            if (selectedStepId === id) setSelectedStepId(null);
        } catch (e) {
            fail(e);
        }
    }

    async function handleMoveStep(id, direction) {
        const sorted = [...steps].sort((a, b) => a.order - b.order);
        const index = sorted.findIndex((s) => s.id === id);
        const swapWith = index + direction;

        if (swapWith < 0 || swapWith >= sorted.length) return;

        [sorted[index], sorted[swapWith]] = [sorted[swapWith], sorted[index]];

        try {
            const updated = await api.reorderSteps(workflow.id, sorted.map((s) => s.id));
            setSteps(updated.steps ?? []);
        } catch (e) {
            fail(e);
        }
    }

    // After a drag: persists every step whose position changed (all of them,
    // the first time a flow is arranged by hand).
    const handleArrange = useCallback(async (placed) => {
        const current = new Map(steps.map((s) => [s.id, s.canvas_position]));
        const changed = placed.filter(({ id, position }) => {
            const saved = current.get(id);
            return !saved || saved.x !== position.x || saved.y !== position.y;
        });

        try {
            const updated = await Promise.all(changed.map(({ id, position }) => api.updateStep(id, { canvas_position: position })));
            const byId = new Map(updated.map((s) => [s.id, s]));
            setSteps((prev) => prev.map((s) => byId.get(s.id) ?? s));
        } catch (e) {
            fail(e);
        }
    }, [steps]);

    // Clears every hand-placed position so the canvas falls back to the
    // automatic left-to-right layout.
    async function handleAutoArrange() {
        try {
            const placed = steps.filter((s) => s.canvas_position);
            const updated = await Promise.all(placed.map((s) => api.updateStep(s.id, { canvas_position: null })));
            const byId = new Map(updated.map((s) => [s.id, s]));
            setSteps((prev) => prev.map((s) => byId.get(s.id) ?? s));
        } catch (e) {
            fail(e);
        }
    }

    const sortedSteps = [...steps].sort((a, b) => a.order - b.order);
    const selectedStep = typeof selectedStepId === 'number' ? steps.find((s) => s.id === selectedStepId) : null;

    return (
        <div className="ws-root">
            {error && (
                <div className="ws-error ws-error-banner">
                    <span>{error}</span>
                    <button type="button" className="ws-btn ws-btn-secondary ws-btn-sm" onClick={() => setError('')}>Dismiss</button>
                </div>
            )}

            <div className="ws-layout">
                <WorkflowList
                    workflows={workflows}
                    selectedId={workflow?.id}
                    onSelect={(w) => openWorkflow(w.id)}
                    onCreate={handleCreateWorkflow}
                />

                {workflow ? (
                    <>
                        <BuilderCard
                            workflow={workflow}
                            steps={steps}
                            stepTypes={stepTypes}
                            selectedStepId={selectedStepId}
                            onSelectStep={setSelectedStepId}
                            onSelectTrigger={() => setSelectedStepId('trigger')}
                            onDeselect={() => setSelectedStepId(null)}
                            onArrange={handleArrange}
                            onAddStep={handleAddStep}
                            onAutoArrange={handleAutoArrange}
                        />

                        <div className="ws-side">
                            {selectedStep ? (
                                <StepPanel
                                    step={selectedStep}
                                    position={sortedSteps.findIndex((s) => s.id === selectedStep.id) + 1}
                                    total={sortedSteps.length}
                                    stepTypes={stepTypes}
                                    onSave={handleSaveStep}
                                    onDelete={handleDeleteStep}
                                    onMove={handleMoveStep}
                                    onClose={() => setSelectedStepId(null)}
                                />
                            ) : (
                                <WorkflowPanel
                                    workflow={workflow}
                                    onSave={handleSaveWorkflow}
                                    onDelete={handleDeleteWorkflow}
                                    onTestRun={handleTestRun}
                                />
                            )}
                        </div>
                    </>
                ) : (
                    <section className="ws-card ws-placeholder">
                        <p>{loading ? 'Loading workflows…' : 'Create a workflow on the left to start building.'}</p>
                    </section>
                )}
            </div>
        </div>
    );
}
