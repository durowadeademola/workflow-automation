import { useCallback, useEffect, useMemo } from 'react';
import { ReactFlow, Background, BackgroundVariant, Controls, MarkerType, useNodesState } from '@xyflow/react';
import StepNode from './StepNode.jsx';
import TriggerNode from './TriggerNode.jsx';
import { typeLabel } from '../stepMeta.js';

const nodeTypes = { step: StepNode, trigger: TriggerNode };

const COLUMN = 290;
const ROW = 150;
const PER_ROW = 3;
const TRIGGER_ID = 'trigger';

/**
 * Where each node sits. Saved canvas_positions are only used when every step
 * has one — i.e. the whole flow was arranged by hand (dragging saves them all
 * at once). Otherwise everything is laid out automatically: the trigger then
 * the steps in execution order, left to right, wrapping every PER_ROW nodes —
 * so a stray position (e.g. one left over from the old top-to-bottom studio)
 * can't scatter the flow.
 */
function layout(sortedSteps) {
    const positions = new Map();
    const arrangedByHand = sortedSteps.length > 0 && sortedSteps.every((step) => step.canvas_position);

    if (arrangedByHand) {
        sortedSteps.forEach((step) => positions.set(step.id, step.canvas_position));
        const first = positions.get(sortedSteps[0].id);

        return { positions, trigger: { x: first.x - COLUMN, y: first.y } };
    }

    const at = (index) => ({ x: (index % PER_ROW) * COLUMN, y: Math.floor(index / PER_ROW) * ROW });
    sortedSteps.forEach((step, i) => positions.set(step.id, at(i + 1)));

    return { positions, trigger: at(0) };
}

export default function FlowCanvas({ workflow, steps, stepTypes, selectedStepId, onSelectStep, onSelectTrigger, onDeselect, onArrange }) {
    const sortedSteps = useMemo(() => [...steps].sort((a, b) => a.order - b.order), [steps]);

    const builtNodes = useMemo(() => {
        const { positions, trigger } = layout(sortedSteps);

        return [
            {
                id: TRIGGER_ID,
                type: 'trigger',
                position: trigger,
                data: { triggerType: workflow.trigger_type },
                draggable: false,
                selected: selectedStepId === TRIGGER_ID,
            },
            ...sortedSteps.map((step) => ({
                id: String(step.id),
                type: 'step',
                position: positions.get(step.id),
                data: { step, kindLabel: typeLabel(stepTypes, step.type) },
                selected: step.id === selectedStepId,
            })),
        ];
    }, [sortedSteps, stepTypes, workflow.trigger_type, selectedStepId]);

    // Local node state so dragging moves nodes live; it's re-synced from the
    // steps whenever they change (saved, added, reordered, re-arranged).
    const [nodes, setNodes, onNodesChange] = useNodesState(builtNodes);
    useEffect(() => setNodes(builtNodes), [builtNodes, setNodes]);

    const edges = useMemo(() => {
        const chain = [TRIGGER_ID, ...sortedSteps.map((s) => String(s.id))];

        return chain.slice(1).map((target, i) => ({
            id: `e-${chain[i]}-${target}`,
            source: chain[i],
            target,
            type: 'smoothstep',
            pathOptions: { borderRadius: 14 },
            className: 'ws-edge',
            markerEnd: { type: MarkerType.ArrowClosed, width: 16, height: 16, color: 'var(--ws-edge)' },
        }));
    }, [sortedSteps]);

    const handleNodeClick = useCallback((_, node) => {
        if (node.id === TRIGGER_ID) {
            onSelectTrigger();
        } else {
            onSelectStep(Number(node.id));
        }
    }, [onSelectStep, onSelectTrigger]);

    // Saves every step's current position, not just the dragged one, so the
    // flow counts as arranged by hand (see layout()).
    const handleNodeDragStop = useCallback((_, dragged) => {
        onArrange(
            nodes
                .filter((node) => node.id !== TRIGGER_ID)
                .map((node) => ({ id: Number(node.id), position: node.id === dragged.id ? dragged.position : node.position })),
        );
    }, [nodes, onArrange]);

    return (
        <ReactFlow
            nodes={nodes}
            edges={edges}
            nodeTypes={nodeTypes}
            onNodesChange={onNodesChange}
            onNodeClick={handleNodeClick}
            onNodeDragStop={handleNodeDragStop}
            onPaneClick={onDeselect}
            nodesConnectable={false}
            fitView
            fitViewOptions={{ padding: 0.25, maxZoom: 1 }}
            minZoom={0.3}
            proOptions={{ hideAttribution: true }}
        >
            <Background variant={BackgroundVariant.Dots} gap={16} size={1.4} color="var(--ws-dot)" />
            <Controls showInteractive={false} className="ws-controls" />
        </ReactFlow>
    );
}
