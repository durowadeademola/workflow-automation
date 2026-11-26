import { Handle, Position } from '@xyflow/react';
import { stepMeta, humanize } from '../stepMeta.js';

/** One workflow step on the canvas, styled like the homepage hero's flow cards. */
export default function StepNode({ data, selected }) {
    const { step, kindLabel } = data;
    const meta = stepMeta(step.type);
    const Icon = meta.icon;

    return (
        <div className={`ws-node${selected ? ' is-selected' : ''}`}>
            <Handle type="target" position={Position.Left} className="ws-handle" />
            <span className={`ws-node-icon ws-tint-${meta.tint}`}>
                <Icon size={18} strokeWidth={2} />
            </span>
            <span className="ws-node-text">
                <span className="ws-node-kind">{kindLabel}</span>
                <span className="ws-node-title">{humanize(step.key)}</span>
                {(step.run_if || step.max_attempts > 1) && (
                    <span className="ws-node-badges">
                        {step.run_if && (
                            <span className="ws-pill ws-pill-amber">
                                if {step.run_if.field} = {String(step.run_if.equals)}
                            </span>
                        )}
                        {step.max_attempts > 1 && <span className="ws-pill ws-pill-sky">retry ×{step.max_attempts}</span>}
                    </span>
                )}
            </span>
            <Handle type="source" position={Position.Right} className="ws-handle" />
        </div>
    );
}
