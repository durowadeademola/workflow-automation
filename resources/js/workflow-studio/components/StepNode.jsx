import { Handle, Position } from '@xyflow/react';

export default function StepNode({ data, selected }) {
    const { step } = data;

    return (
        <div className={`ws-step-node${selected ? ' selected' : ''}`}>
            <Handle type="target" position={Position.Top} />
            <div className="key">{step.key}</div>
            <div className="type">{step.type}</div>
            {step.run_if && (
                <div className="badge">
                    if {step.run_if.field} == {String(step.run_if.equals)}
                </div>
            )}
            <Handle type="source" position={Position.Bottom} />
        </div>
    );
}
