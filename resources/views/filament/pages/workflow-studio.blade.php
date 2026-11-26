{{-- Mount point for Workflow Studio, a React app (resources/js/workflow-studio). wire:ignore keeps
     Livewire from ever morphing over the React-owned DOM. --}}
<x-filament-panels::page>
    <div id="workflow-studio-root" wire:ignore></div>

    @viteReactRefresh
    @vite('resources/js/workflow-studio/main.jsx')
</x-filament-panels::page>
