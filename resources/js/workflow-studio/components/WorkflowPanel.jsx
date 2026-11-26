import { useEffect, useState } from 'react';
import { Play } from 'lucide-react';

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
            setResult(await onTestRun(workflow.id, parsedPayload));
        } catch (e) {
            setRunError(e.message);
        } finally {
            setRunning(false);
        }
    }

    return (
        <>
            <section className="ws-card">
                <header className="ws-card-header">
                    <h3 className="ws-card-title">Workflow settings</h3>
                    <p className="ws-card-desc">Click a step on the canvas to edit it instead.</p>
                </header>

                <div className="ws-form">
                    {error && <div className="ws-error">{error}</div>}

                    <label className="ws-field">
                        <span>Name</span>
                        <input type="text" value={name} onChange={(e) => setName(e.target.value)} />
                    </label>
                    <label className="ws-field">
                        <span>Slug</span>
                        <input type="text" value={slug} onChange={(e) => setSlug(e.target.value)} />
                    </label>
                    <label className="ws-field">
                        <span>Trigger</span>
                        <select value={triggerType} onChange={(e) => setTriggerType(e.target.value)}>
                            <option value="manual">Manual</option>
                            <option value="webhook">Webhook</option>
                            <option value="scheduled">Scheduled</option>
                            <option value="model_event">Model event</option>
                        </select>
                    </label>
                    <label className="ws-field">
                        <span>Trigger config (JSON), e.g. {'{"cron": "*/15 * * * *"}'} for scheduled</span>
                        <textarea value={triggerConfig} onChange={(e) => setTriggerConfig(e.target.value)} rows={4} />
                    </label>
                    <label className="ws-check">
                        <input type="checkbox" checked={isActive} onChange={(e) => setIsActive(e.target.checked)} />
                        <span>Live (runs on its trigger)</span>
                    </label>

                    <div className="ws-actions">
                        <button type="button" className="ws-btn ws-btn-primary" onClick={handleSave}>Save workflow</button>
                        <button type="button" className="ws-btn ws-btn-link-danger" onClick={() => onDelete(workflow.id)}>Delete</button>
                    </div>
                </div>
            </section>

            <section className="ws-card">
                <header className="ws-card-header">
                    <h3 className="ws-card-title">Test run</h3>
                    <p className="ws-card-desc">A real run: any action steps really happen.</p>
                </header>

                <div className="ws-form">
                    {runError && <div className="ws-error">{runError}</div>}

                    <label className="ws-field">
                        <span>Trigger payload (JSON)</span>
                        <textarea value={payload} onChange={(e) => setPayload(e.target.value)} rows={5} />
                    </label>

                    <div className="ws-actions">
                        <button type="button" className="ws-btn ws-btn-primary" disabled={running} onClick={handleTestRun}>
                            <Play size={14} /> {running ? 'Running…' : 'Run now'}
                        </button>
                    </div>

                    {result && (
                        <div className="ws-result">
                            <p className="ws-result-title">
                                Result
                                <span className={`ws-pill ${result.status === 'completed' ? 'ws-pill-green' : 'ws-pill-red'}`}>{result.status}</span>
                            </p>
                            <pre className="ws-code">{JSON.stringify(result.context?.steps ?? result, null, 2)}</pre>
                        </div>
                    )}
                </div>
            </section>
        </>
    );
}
