import { useEffect, useState } from 'react';

export default function WorkflowPanel({ workflow, onSave, onDelete, onTestRun }) {
    const [name, setName] = useState(workflow.name);
    const [slug, setSlug] = useState(workflow.slug);
    const [triggerType, setTriggerType] = useState(workflow.trigger_type);
    const [triggerConfig, setTriggerConfig] = useState(JSON.stringify(workflow.trigger_config ?? {}, null, 2));
    const [isActive, setIsActive] = useState(workflow.is_active);
    const [error, setError] = useState('');

    const [payload, setPayload] = useState('{\n  "message": "Hello"\n}');
    const [running, setRunning] = useState(false);
    const [result, setResult] = useState(null);
    const [runError, setRunError] = useState('');

    useEffect(() => {
        setName(workflow.name);
        setSlug(workflow.slug);
        setTriggerType(workflow.trigger_type);
        setTriggerConfig(JSON.stringify(workflow.trigger_config ?? {}, null, 2));
        setIsActive(workflow.is_active);
        setResult(null);
        setError('');
        setRunError('');
    }, [workflow.id]);

    function handleSave() {
        let parsedConfig;

        try {
            parsedConfig = triggerConfig.trim() ? JSON.parse(triggerConfig) : null;
        } catch (e) {
            setError('Trigger config is not valid JSON: ' + e.message);
            return;
        }

        setError('');
        onSave(workflow.id, {
            name,
            slug,
            trigger_type: triggerType,
            trigger_config: parsedConfig,
            is_active: isActive,
        });
    }

    async function handleTestRun() {
        let parsedPayload;

        try {
            parsedPayload = payload.trim() ? JSON.parse(payload) : {};
        } catch (e) {
            setRunError('Trigger payload is not valid JSON: ' + e.message);
            return;
        }

        setRunError('');
        setRunning(true);
        setResult(null);

        try {
            const run = await onTestRun(workflow.id, parsedPayload);
            setResult(run);
        } catch (e) {
            setRunError(e.message);
        } finally {
            setRunning(false);
        }
    }

    return (
        <div>
            <div className="ws-section-title">Workflow settings</div>

            {error && <div className="ws-error">{error}</div>}

            <div className="ws-field">
                <label>Name</label>
                <input type="text" value={name} onChange={(e) => setName(e.target.value)} />
            </div>
            <div className="ws-field">
                <label>Slug</label>
                <input type="text" value={slug} onChange={(e) => setSlug(e.target.value)} />
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
            <div className="ws-field">
                <label>Trigger config (JSON) — e.g. {'{"cron": "*/15 * * * *"}'} for scheduled</label>
                <textarea value={triggerConfig} onChange={(e) => setTriggerConfig(e.target.value)} style={{ minHeight: 70 }} />
            </div>
            <div className="ws-field">
                <label>
                    <input type="checkbox" checked={isActive} onChange={(e) => setIsActive(e.target.checked)} style={{ width: 'auto', marginRight: 6 }} />
                    Active
                </label>
            </div>

            <div style={{ display: 'flex', gap: 8, marginBottom: 8 }}>
                <button className="ws-btn" onClick={handleSave}>Save workflow</button>
                <button className="ws-btn danger" onClick={() => onDelete(workflow.id)}>Delete</button>
            </div>

            <div className="ws-section-title">Test run</div>

            {runError && <div className="ws-error">{runError}</div>}

            <div className="ws-field">
                <label>Trigger payload (JSON)</label>
                <textarea value={payload} onChange={(e) => setPayload(e.target.value)} style={{ minHeight: 90 }} />
            </div>

            <button className="ws-btn" disabled={running} onClick={handleTestRun}>
                {running ? 'Running...' : 'Run now'}
            </button>

            {result && (
                <div style={{ marginTop: 12 }}>
                    <div className="ws-section-title">Result — {result.status}</div>
                    <div className="ws-run-log">{JSON.stringify(result.context?.steps ?? result, null, 2)}</div>
                </div>
            )}
        </div>
    );
}
