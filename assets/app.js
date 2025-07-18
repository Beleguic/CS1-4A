import './bootstrap.js';

import './styles/app.css';

import { createIcons, icons } from 'lucide';

let isInitializing = false;
let initializationTimeout = null;

function initializeLucideIcons() {
    // Prevent multiple simultaneous initializations
    if (isInitializing) return;
    
    // Clear any pending timeout
    if (initializationTimeout) {
        clearTimeout(initializationTimeout);
        initializationTimeout = null;
    }
    
    isInitializing = true;
    
    try {
        createIcons({ icons });
    } catch (error) {
        console.error('Error initializing Lucide icons:', error);
    } finally {
        // Reset flag after a short delay
        setTimeout(() => {
            isInitializing = false;
        }, 100);
    }
}

function debouncedInitialize() {
    if (initializationTimeout) {
        clearTimeout(initializationTimeout);
    }
    initializationTimeout = setTimeout(initializeLucideIcons, 100);
}

// Initial load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeLucideIcons);
} else {
    initializeLucideIcons();
}

// Turbo navigation (if using Symfony UX Turbo)
document.addEventListener('turbo:load', debouncedInitialize);

// Public API for manual refresh
window.refreshIcons = function() {
    debouncedInitialize();
};

window.debugLucide = function() {
    console.log('Icons found:', document.querySelectorAll('[data-lucide]').length);
    initializeLucideIcons();
};