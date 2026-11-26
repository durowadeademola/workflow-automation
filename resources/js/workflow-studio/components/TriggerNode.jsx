import { Handle, Position } from '@xyflow/react';
import { triggerMeta } from '../stepMeta.js';

/**
 * The start of every flow. Not a stored step — it reflects the workflow's own
 * trigger_type, and clicking it opens the workflow's settings.
 */
export default function TriggerNode({ data, selected }) {
    const meta = triggerMeta(data.triggerType);
    const Icon = meta.icon;

    return (
        <div className={`ws-node ws-node-trigger${selected ? ' is-selected' : ''}`}>
            <span className="ws-node-icon ws-tint-indigo">
                <Icon size={18} strokeWidth={2} />
            </span>
            <span className="ws-node-text">
                <span className="ws-node-kind">Trigger</span>
                <span className="ws-node-title">{meta.label}</span>
            </span>
            <Handle type="source" position={Position.Right} className="ws-handle" />
        </div>
    );
}
