import { useMemo, useCallback } from 'react';
import { ReactFlow, Background, Controls, MiniMap } from '@xyflow/react';
import StepNode from './StepNode.jsx';

const nodeTypes = { stepNode: StepNode };

export default function FlowCanvas({ steps, selectedStepId, onSelectStep, onDeselect, onNodeMoved }) {
    const nodes = useMemo(() => steps.map((step, i) => ({
        id: String(step.id),
        type: 'stepNode',
        position: step.canvas_position ?? { x: 250, y: i * 130 + 40 },
        data: { step },
        selected: step.id === selectedStepId,
    })), [steps, selectedStepId]);

    const edges = useMemo(() => {
        const sorted = [...steps].sort((a, b) => a.order - b.order);

        return sorted.slice(1).map((step, i) => ({
            id: `e-${sorted[i].id}-${step.id}`,
            source: String(sorted[i].id),
            target: String(step.id),
            animated: false,
        }));
    }, [steps]);

    const handleNodeClick = useCallback((_, node) => {
        onSelectStep(Number(node.id));
    }, [onSelectStep]);

    const handleNodeDragStop = useCallback((_, node) => {
        onNodeMoved(Number(node.id), node.position);
    }, [onNodeMoved]);

    return (
        <ReactFlow
            nodes={nodes}
            edges={edges}
            nodeTypes={nodeTypes}
            onNodeClick={handleNodeClick}
            onNodeDragStop={handleNodeDragStop}
            onPaneClick={onDeselect}
            fitView
            proOptions={{ hideAttribution: true }}
        >
            <Background color="#1e293b" gap={20} />
            <Controls />
            <MiniMap pannable zoomable style={{ background: '#1e293b' }} />
        </ReactFlow>
    );
}
