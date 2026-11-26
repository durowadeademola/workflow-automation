import { useState } from 'react';
import { Plus } from 'lucide-react';
import { triggerMeta } from '../stepMeta.js';

const slugify = (value) => value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');

export default function WorkflowList({ workflows, selectedId, onSelect, onCreate }) {
    const [creating, setCreating] = useState(false);
    const [name, setName] = useState('');
    const [slug, setSlug] = useState('');
    const [slugTouched, setSlugTouched] = useState(false);
    const [triggerType, setTriggerType] = useState('manual');

    function reset() {
        setCreating(false);
        setName('');
        setSlug('');
        setSlugTouched(false);
        setTriggerType('manual');
    }

    async function submit(e) {
        e.preventDefault();
        if (await onCreate({ name, slug, trigger_type: triggerType, is_active: true })) reset();
    }

    return (
        <section className="ws-card ws-list">
            <header className="ws-card-header ws-card-header-row">
                <div>
                    <h3 className="ws-card-title">Workflows</h3>
                    <p className="ws-card-desc">{workflows.length} in total</p>
                </div>
                {!creating && (
                    <button type="button" className="ws-btn ws-btn-primary ws-btn-sm" onClick={() => setCreating(true)}>
                        <Plus size={14} /> New
                    </button>
                )}
            </header>

            {creating && (
                <form className="ws-form ws-list-form" onSubmit={submit}>
                    <label className="ws-field">
                        <span>Name</span>
                        <input
                            type="text"
                            value={name}
                            required
                            autoFocus
                            onChange={(e) => {
                                setName(e.target.value);
                                if (!slugTouched) setSlug(slugify(e.target.value));
                            }}
                        />
                    </label>
                    <label className="ws-field">
                        <span>Slug</span>
                        <input type="text" value={slug} required onChange={(e) => { setSlug(e.target.value); setSlugTouched(true); }} />
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
                    <div className="ws-actions">
                        <button type="submit" className="ws-btn ws-btn-primary">Create</button>
                        <button type="button" className="ws-btn ws-btn-secondary" onClick={reset}>Cancel</button>
                    </div>
                </form>
            )}

            <div className="ws-list-items">
                {workflows.map((w) => (
                    <button
                        key={w.id}
                        type="button"
                        className={`ws-list-item${selectedId === w.id ? ' is-active' : ''}`}
                        onClick={() => onSelect(w)}
                    >
                        <span className={`ws-status-dot${w.is_active ? ' is-live' : ''}`} />
                        <span className="ws-list-item-text">
                            <span className="ws-list-item-name">{w.name}</span>
                            <span className="ws-list-item-meta">
                                {triggerMeta(w.trigger_type).label} · {w.steps_count ?? 0} steps · {(w.runs_count ?? 0).toLocaleString()} runs
                            </span>
                        </span>
                    </button>
                ))}
                {workflows.length === 0 && !creating && <p className="ws-empty">No workflows yet.</p>}
            </div>
        </section>
    );
}
