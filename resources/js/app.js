import './bootstrap';

// Initialize any global JavaScript functionality
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Lucide icons when the page loads
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
    
    // Re-initialize icons when Alpine.js re-renders components
    document.addEventListener('alpine:init', () => {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
});
