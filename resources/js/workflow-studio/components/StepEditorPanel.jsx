import { useEffect, useState } from 'react';

export default function StepEditorPanel({ step, onSave, onDelete }) {
    const [key, setKey] = useState(step.key);
    const [config, setConfig] = useState(JSON.stringify(step.config ?? {}, null, 2));
    const [hasCondition, setHasCondition] = useState(!!step.run_if);
    const [field, setField] = useState(step.run_if?.field ?? '');
    const [equalsValue, setEqualsValue] = useState(step.run_if ? String(step.run_if.equals) : 'true');
    const [error, setError] = useState('');

    useEffect(() => {
        setKey(step.key);
        setConfig(JSON.stringify(step.config ?? {}, null, 2));
        setHasCondition(!!step.run_if);
        setField(step.run_if?.field ?? '');
        setEqualsValue(step.run_if ? String(step.run_if.equals) : 'true');
        setError('');
    }, [step.id]);

    function coerce(value) {
        if (value === 'true') return true;
        if (value === 'false') return false;
        if (value !== '' && !isNaN(Number(value))) return Number(value);
        return value;
    }

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
        });
    }

    return (
        <div>
            <div className="ws-section-title">Step</div>

            {error && <div className="ws-error">{error}</div>}

            <div className="ws-field">
                <label>Key</label>
                <input type="text" value={key} onChange={(e) => setKey(e.target.value)} />
            </div>

            <div className="ws-field">
                <label>Type</label>
                <input type="text" value={step.type} disabled />
            </div>

            <div className="ws-field">
                <label>Config (JSON) — use {'{{trigger.field}}'} or {'{{steps.key.field}}'} to reference other data</label>
                <textarea value={config} onChange={(e) => setConfig(e.target.value)} />
            </div>

            <div className="ws-field">
                <label>
                    <input
                        type="checkbox"
                        checked={hasCondition}
                        onChange={(e) => setHasCondition(e.target.checked)}
                        style={{ width: 'auto', marginRight: 6 }}
                    />
                    Only run if a condition is met
                </label>
            </div>

            {hasCondition && (
                <>
                    <div className="ws-field">
                        <label>Field (dot path, e.g. steps.extract.wantsAppointment)</label>
                        <input type="text" value={field} onChange={(e) => setField(e.target.value)} />
                    </div>
                    <div className="ws-field">
                        <label>Equals</label>
                        <input type="text" value={equalsValue} onChange={(e) => setEqualsValue(e.target.value)} placeholder="true / false / a string / a number" />
                    </div>
                </>
            )}

            <div style={{ display: 'flex', gap: 8 }}>
                <button className="ws-btn" onClick={handleSave}>Save step</button>
                <button className="ws-btn danger" onClick={() => onDelete(step.id)}>Delete</button>
            </div>
        </div>
    );
}
