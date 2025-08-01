
// Import feature-specific initializers
import { initFavoriteAction } from './favoriteAction.js';
import { initBlacklistAction } from './blacklistAction.js';
import { initReportAction } from './reportAction.js';
import { initContactAgentAction } from './contactAgentAction.js';
import { initShowMobileAction } from './showMobileAction.js';
import { initShareAction } from './shareAction.js';

/**
 * Initializes all property actions by delegating to feature-specific initializers.
 * Retrieves shared configurations like translations, CSRF token, and auth status from window objects.
 *
 * This function can be exported and called from another module or an inline script tag in HTML.
 */
export function initializeAllPropertyActions() {
    console.log('[PropertyActions Module] initializeAllPropertyActions() CALLED');

    // --- Global/Shared Variables & Configuration (still accessed from window for this example) ---
    // For even cleaner modules, these could be passed as arguments to initializeAllPropertyActions
    const apiConfig = window.AppConfig || {};

    if (Object.keys(apiConfig).length === 0) {
        console.error("[PropertyActions Module] Global 'api_urls' object is missing or empty.");
    }

    // --- Initialize Feature Modules ---

    // Favorite Action
    initFavoriteAction();

    // Blacklist Action
    initBlacklistAction();

    // Report Action
    initReportAction();

    // Contact Agent Action
    initContactAgentAction();

    initShowMobileAction();

    initShareAction();

    console.log('[PropertyActions Module] initializeAllPropertyActions() COMPLETED');
}

// --- Optional: Auto-initialize on DOMContentLoaded if this is the main entry point ---
// document.addEventListener('DOMContentLoaded', initializeAllPropertyActions);
// If you use this, the HTML script tag for propertyActions.js would just be:
// <script type="module" src="path/to/your/js/propertyActions.js"></script>
// And you wouldn't need the separate inline script to call the function.