import { useState } from 'react';

export default function WorkflowSidebar({
    workflows,
    selectedWorkflow,
    onSelectWorkflow,
    onCreateWorkflow,
    steps,
    selectedStepId,
    onSelectStep,
    onDeleteStep,
    onReorderStep,
    stepTypes,
    onAddStep,
}) {
    const [creating, setCreating] = useState(false);
    const [name, setName] = useState('');
    const [slug, setSlug] = useState('');
    const [triggerType, setTriggerType] = useState('manual');
    const [addingType, setAddingType] = useState('');

    function submitCreate(e) {
        e.preventDefault();
        onCreateWorkflow({ name, slug, trigger_type: triggerType, is_active: true });
        setCreating(false);
        setName('');
        setSlug('');
    }

    const sortedSteps = [...steps].sort((a, b) => a.order - b.order);

    return (
        <div className="ws-sidebar">
            <div className="ws-section-title">Workflows</div>

            {workflows.map((w) => (
                <div
                    key={w.id}
                    className={`ws-workflow-item${selectedWorkflow?.id === w.id ? ' active' : ''}`}
                    onClick={() => onSelectWorkflow(w)}
                >
                    <span className={`dot ${w.is_active ? 'active' : 'inactive'}`} />
                    <span style={{ flex: 1, marginLeft: 8 }}>{w.name}</span>
                    <span style={{ fontSize: '0.68rem', color: '#64748b' }}>{w.runs_count ?? 0}</span>
                </div>
            ))}

            {!creating && (
                <button className="ws-btn secondary" style={{ width: '100%', marginTop: 8 }} onClick={() => setCreating(true)}>
                    + New workflow
                </button>
            )}

            {creating && (
                <form onSubmit={submitCreate} style={{ marginTop: 8 }}>
                    <div className="ws-field">
                        <label>Name</label>
                        <input type="text" value={name} required onChange={(e) => {
                            setName(e.target.value);
                            if (!slug) setSlug(e.target.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, ''));
                        }} />
                    </div>
                    <div className="ws-field">
                        <label>Slug</label>
                        <input type="text" value={slug} required onChange={(e) => setSlug(e.target.value)} />
                    </div>
                    <div className="ws-field">
                        <label>Trigger type</label>
                        <select value={triggerType} onChange={(e) => setTriggerType(e.target.value)}>
                            <option value="manual">manual</option>
                            <option value="webhook">webhook</option>
                            <option value="scheduled">scheduled</option>
                            <option value="model_event">model_event</option>
                        </select>
                    </div>
                    <div style={{ display: 'flex', gap: 8 }}>
                        <button type="submit" className="ws-btn">Create</button>
                        <button type="button" className="ws-btn secondary" onClick={() => setCreating(false)}>Cancel</button>
                    </div>
                </form>
            )}

            {selectedWorkflow && (
                <>
                    <div className="ws-section-title">Steps (execution order)</div>

                    {sortedSteps.map((step, i) => (
                        <div
                            key={step.id}
                            className={`ws-step-item${selectedStepId === step.id ? ' selected' : ''}`}
                            onClick={() => onSelectStep(step.id)}
                        >
                            <span style={{ color: '#64748b', width: 16 }}>{i + 1}</span>
                            <span className="key">{step.key}</span>
                            <span className="type">{step.type}</span>
                            <button title="Move up" disabled={i === 0} onClick={(e) => { e.stopPropagation(); onReorderStep(step.id, -1); }}>↑</button>
                            <button title="Move down" disabled={i === sortedSteps.length - 1} onClick={(e) => { e.stopPropagation(); onReorderStep(step.id, 1); }}>↓</button>
                            <button title="Delete" onClick={(e) => { e.stopPropagation(); onDeleteStep(step.id); }}>✕</button>
                        </div>
                    ))}

                    <div style={{ display: 'flex', gap: 6, marginTop: 8 }}>
                        <select value={addingType} onChange={(e) => setAddingType(e.target.value)} style={{ flex: 1, background: '#0f172a', color: '#e2e8f0', border: '1px solid #334155', borderRadius: 6, padding: '6px 8px', fontSize: '0.78rem' }}>
                            <option value="">Pick step type...</option>
                            {stepTypes.map((t) => (
                                <option key={t.type} value={t.type}>{t.label}</option>
                            ))}
                        </select>
                        <button
                            className="ws-btn"
                            disabled={!addingType}
                            onClick={() => {
                                onAddStep(addingType);
                                setAddingType('');
                            }}
                        >
                            Add
                        </button>
                    </div>
                </>
            )}
        </div>
    );
}
