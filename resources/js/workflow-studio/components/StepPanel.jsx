import { useEffect, useState } from 'react';
import { ArrowLeft, ArrowRight } from 'lucide-react';
import { stepMeta, typeLabel } from '../stepMeta.js';

function coerce(value) {
    if (value === 'true') return true;
    if (value === 'false') return false;
    if (value !== '' && !isNaN(Number(value))) return Number(value);
    return value;
}

export default function StepPanel({ step, position, total, stepTypes, onSave, onDelete, onMove, onClose }) {
    const [key, setKey] = useState(step.key);
    const [config, setConfig] = useState(JSON.stringify(step.config ?? {}, null, 2));
    const [hasCondition, setHasCondition] = useState(!!step.run_if);
    const [field, setField] = useState(step.run_if?.field ?? '');
    const [equalsValue, setEqualsValue] = useState(step.run_if ? String(step.run_if.equals) : 'true');
    const [maxAttempts, setMaxAttempts] = useState(step.max_attempts ?? 1);
    const [retryDelayMs, setRetryDelayMs] = useState(step.retry_delay_ms ?? 500);
    const [error, setError] = useState('');

    useEffect(() => {
        setKey(step.key);
        setConfig(JSON.stringify(step.config ?? {}, null, 2));
        setHasCondition(!!step.run_if);
        setField(step.run_if?.field ?? '');
        setEqualsValue(step.run_if ? String(step.run_if.equals) : 'true');
        setMaxAttempts(step.max_attempts ?? 1);
        setRetryDelayMs(step.retry_delay_ms ?? 500);
        setError('');
    }, [step.id]);

    function handleSave() {
        let parsedConfig;

        try {
            parsedConfig = JSON.parse(config);
        } catch (e) {
            setError('Config is not valid JSON: ' + e.message);
            return;
        }

        setError('');
        onSave(step.id, {
            key,
            config: parsedConfig,
            run_if: hasCondition && field ? { field, equals: coerce(equalsValue) } : null,
            max_attempts: Number(maxAttempts) || 1,
            retry_delay_ms: Number(retryDelayMs) || 0,
        });
    }

    const meta = stepMeta(step.type);
    const Icon = meta.icon;

    return (
        <section className="ws-card">
            <header className="ws-card-header ws-card-header-row">
                <div className="ws-step-heading">
                    <span className={`ws-node-icon ws-tint-${meta.tint}`}>
                        <Icon size={18} strokeWidth={2} />
                    </span>
                    <div>
                        <h3 className="ws-card-title">{typeLabel(stepTypes, step.type)}</h3>
                        <p className="ws-card-desc">Step {position} of {total}</p>
                    </div>
                </div>
                <button type="button" className="ws-btn ws-btn-secondary ws-btn-sm" onClick={onClose}>Done</button>
            </header>

            <div className="ws-form">
                {error && <div className="ws-error">{error}</div>}

                <div className="ws-actions">
                    <button type="button" className="ws-btn ws-btn-secondary ws-btn-sm" disabled={position === 1} onClick={() => onMove(step.id, -1)}>
                        <ArrowLeft size={14} /> Run earlier
                    </button>
                    <button type="button" className="ws-btn ws-btn-secondary ws-btn-sm" disabled={position === total} onClick={() => onMove(step.id, 1)}>
                        Run later <ArrowRight size={14} />
                    </button>
                </div>

                <label className="ws-field">
                    <span>Key</span>
                    <input type="text" value={key} onChange={(e) => setKey(e.target.value)} />
                </label>

                <label className="ws-field">
                    <span>Config (JSON). Use {'{{trigger.field}}'} or {'{{steps.key.field}}'} to reference other data.</span>
                    <textarea value={config} onChange={(e) => setConfig(e.target.value)} rows={8} />
                </label>

                <label className="ws-check">
                    <input type="checkbox" checked={hasCondition} onChange={(e) => setHasCondition(e.target.checked)} />
                    <span>Only run if a condition is met</span>
                </label>

                {hasCondition && (
                    <>
                        <label className="ws-field">
                            <span>Field (dot path, e.g. steps.extract.wantsAppointment)</span>
                            <input type="text" value={field} onChange={(e) => setField(e.target.value)} />
                        </label>
                        <label className="ws-field">
                            <span>Equals</span>
                            <input type="text" value={equalsValue} onChange={(e) => setEqualsValue(e.target.value)} placeholder="true / false / a string / a number" />
                        </label>
                    </>
                )}

                <label className="ws-field">
                    <span>
                        Max attempts. 1 means no retry. Only raise this for steps with no side effect to double (an LLM call,
                        a search): retrying something that books, inserts or sends risks doing it twice if a response is merely
                        lost, not actually failed.
                    </span>
                    <input type="number" min="1" max="10" value={maxAttempts} onChange={(e) => setMaxAttempts(e.target.value)} />
                </label>

                {maxAttempts > 1 && (
                    <label className="ws-field">
                        <span>Delay between retries (ms)</span>
                        <input type="number" min="0" max="60000" value={retryDelayMs} onChange={(e) => setRetryDelayMs(e.target.value)} />
                    </label>
                )}

                <div className="ws-actions">
                    <button type="button" className="ws-btn ws-btn-primary" onClick={handleSave}>Save step</button>
                    <button type="button" className="ws-btn ws-btn-link-danger" onClick={() => onDelete(step.id)}>Delete step</button>
                </div>
            </div>
        </section>
    );
}
