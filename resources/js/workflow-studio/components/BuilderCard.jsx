import { useState } from 'react';
import { ReactFlowProvider } from '@xyflow/react';
import { LayoutGrid, Plus } from 'lucide-react';
import FlowCanvas from './FlowCanvas.jsx';
import { triggerMeta, typeLabel } from '../stepMeta.js';

function timeAgo(iso) {
    if (!iso) return null;
    const seconds = Math.max(0, (Date.now() - new Date(iso).getTime()) / 1000);
    const units = [['y', 31536000], ['mo', 2592000], ['d', 86400], ['h', 3600], ['m', 60]];
    for (const [label, size] of units) {
        if (seconds >= size) return `${Math.floor(seconds / size)}${label} ago`;
    }
    return 'just now';
}

function Stat({ label, children }) {
    return (
        <div className="ws-stat">
            <p className="ws-stat-label">{label}</p>
            <div className="ws-stat-value">{children}</div>
        </div>
    );
}

/**
 * The selected workflow, laid out like the homepage hero's card: a breadcrumb
 * header with a Live/Paused pill, the dotted flow canvas, and a stats strip.
 */
export default function BuilderCard({
    workflow,
    steps,
    stepTypes,
    selectedStepId,
    onSelectStep,
    onSelectTrigger,
    onDeselect,
    onArrange,
    onAddStep,
    onAutoArrange,
}) {
    const [addingType, setAddingType] = useState('');
    const latestRun = workflow.latest_run;

    return (
        <section className="ws-card ws-builder">
            <header className="ws-builder-header">
                <div className="ws-crumbs">
                    <span className="ws-crumb-muted">Workflows</span>
                    <span className="ws-crumb-sep">/</span>
                    <span className="ws-crumb-current">{workflow.name}</span>
                    <span className={`ws-pill ${workflow.is_active ? 'ws-pill-green' : 'ws-pill-gray'}`}>
                        <span className="ws-pill-dot" />
                        {workflow.is_active ? 'Live' : 'Paused'}
                    </span>
                </div>

                <div className="ws-toolbar">
                    <button type="button" className="ws-btn ws-btn-secondary" onClick={onAutoArrange} title="Line the steps up left to right in execution order">
                        <LayoutGrid size={15} /> Auto-arrange
                    </button>
                    <select className="ws-select" value={addingType} onChange={(e) => setAddingType(e.target.value)}>
                        <option value="">Add a step…</option>
                        {stepTypes.map((t) => (
                            <option key={t.type} value={t.type}>{typeLabel(stepTypes, t.type)}</option>
                        ))}
                    </select>
                    <button
                        type="button"
                        className="ws-btn ws-btn-primary"
                        disabled={!addingType}
                        onClick={() => {
                            onAddStep(addingType);
                            setAddingType('');
                        }}
                    >
                        <Plus size={15} /> Add
                    </button>
                </div>
            </header>

            <div className="ws-canvas">
                <ReactFlowProvider key={workflow.id}>
                    <FlowCanvas
                        workflow={workflow}
                        steps={steps}
                        stepTypes={stepTypes}
                        selectedStepId={selectedStepId}
                        onSelectStep={onSelectStep}
                        onSelectTrigger={onSelectTrigger}
                        onDeselect={onDeselect}
                        onArrange={onArrange}
                    />
                </ReactFlowProvider>
                {steps.length === 0 && (
                    <p className="ws-canvas-hint">No steps yet. Pick one from “Add a step…” above to start the flow.</p>
                )}
            </div>

            <div className="ws-stats">
                <Stat label="Steps">{steps.length}</Stat>
                <Stat label="Total runs">{(workflow.runs_count ?? 0).toLocaleString()}</Stat>
                <Stat label="Last run">
                    {latestRun ? (
                        <span className="ws-stat-run">
                            <span className={`ws-pill ${latestRun.status === 'completed' ? 'ws-pill-green' : latestRun.status === 'failed' ? 'ws-pill-red' : 'ws-pill-amber'}`}>
                                {latestRun.status}
                            </span>
                            <span className="ws-muted">{timeAgo(latestRun.created_at)}</span>
                        </span>
                    ) : (
                        <span className="ws-muted">Never</span>
                    )}
                </Stat>
                <Stat label="Trigger">{triggerMeta(workflow.trigger_type).label}</Stat>
            </div>
        </section>
    );
}
