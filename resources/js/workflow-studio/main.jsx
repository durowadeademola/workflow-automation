import { createRoot } from 'react-dom/client';
import '@xyflow/react/dist/style.css';
import './style.css';
import App from './App.jsx';

const container = document.getElementById('workflow-studio-root');
createRoot(container).render(<App />);
