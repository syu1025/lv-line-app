import './bootstrap';
import React from 'react';
import ReactDOM from 'react-dom/client';
import LINEMiniApp from './components/LINEMiniApp';

const root = ReactDOM.createRoot(document.getElementById('app'));
root.render(
    <React.StrictMode>
        <LINEMiniApp />
    </React.StrictMode>
);
