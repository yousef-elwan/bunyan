import { translate } from "./helpers";

export class MyGlobalModal {

    constructor(modalId = 'myGlobalModal') {
        this.modalId = modalId;
        this.modalElement = document.getElementById(this.modalId);

        // If modal HTML doesn't exist, create and append it
        if (!this.modalElement) {
            this._createModalHtml();
            this.modalElement = document.getElementById(this.modalId); // Re-select after creation
        }

        if (!this.modalElement) {
            console.error(`MyGlobalModal: Failed to create or find modal element with ID '${this.modalId}'.`);
            return; // Stop if modal element is still not available
        }

        this.titleElement = this.modalElement.querySelector('.my-modal-title');
        this.bodyElement = this.modalElement.querySelector('.my-modal-body');
        this.actionsElement = this.modalElement.querySelector('.my-modal-actions');
        this.closeButton = this.modalElement.querySelector('.my-modal-close-btn');
        this.contentElement = this.modalElement.querySelector('.my-modal-content');

        this.confirmCallback = null;
        this.cancelCallback = null;
        this.currentConfig = {};
        this.autoCloseTimerId = null; // Added for timer feature

        this._bindEvents();
    }

    _createModalHtml() {
        const modalHtml = `
            <div class="my-modal-content">
                <span class="my-modal-close-btn" aria-label="Close Modal">×</span>
                <h3 class="my-modal-title">Default Title</h3>
                <div class="my-modal-body">
                    <p>Default body content.</p>
                </div>
                <div class="my-modal-actions">
                    <!-- Buttons are populated by JavaScript -->
                </div>
            </div>
        `;
        const modalContainer = document.createElement('div');
        modalContainer.id = this.modalId;
        modalContainer.className = 'my-global-modal-hidden-on-load'; // Start hidden
        modalContainer.innerHTML = modalHtml;
        document.body.appendChild(modalContainer);

        // Also ensure the initial hiding style is present if not in CSS file
        if (!document.getElementById('myGlobalModalInitialHideStyle')) {
            const style = document.createElement('style');
            style.id = 'myGlobalModalInitialHideStyle';
            style.textContent = `#${this.modalId}.my-global-modal-hidden-on-load { display: none; }`;
            document.head.appendChild(style);
        }
    }

    _bindEvents() {
        if (this.closeButton) {
            this.closeButton.addEventListener('click', () => this.close(true));
        }
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && this.modalElement && this.modalElement.style.display === 'flex') {
                this.close(true);
            }
        });
        // Optional: Close on backdrop click
        // if (this.modalElement) {
        //     this.modalElement.addEventListener('click', (event) => {
        //         if (event.target === this.modalElement) {
        //             this.close(true);
        //         }
        //     });
        // }
    }

    show(options = {}) {
        if (!this.modalElement || !this.titleElement || !this.bodyElement || !this.actionsElement || !this.contentElement) {
            console.error("MyGlobalModal: Core elements are missing. Cannot show modal.");
            return;
        }
        this.currentConfig = options;

        // Clear any existing auto-close timer
        if (this.autoCloseTimerId) {
            clearTimeout(this.autoCloseTimerId);
            this.autoCloseTimerId = null;
        }

        this.titleElement.textContent = options.title || translate('notification');
        this.bodyElement.innerHTML = options.bodyHtml || '';
        this.actionsElement.innerHTML = '';

        this.contentElement.style.maxWidth = options.maxWidth || '500px';

        if (this.closeButton) {
            this.closeButton.style.display = options.showCloseIcon !== false ? 'block' : 'none';
        }

        if (options.buttons && options.buttons.length > 0) {
            options.buttons.forEach(buttonConfig => {
                const button = document.createElement('button');
                button.textContent = buttonConfig.text;
                button.className = `my-modal-btn ${buttonConfig.class || 'my-modal-btn-outline'}`;
                if (buttonConfig.id) button.id = buttonConfig.id;

                button.addEventListener('click', () => {
                    if (buttonConfig.onClick) {
                        buttonConfig.onClick();
                    }
                    if (buttonConfig.isCloseButton !== false) {
                        this.close();
                    }
                });
                this.actionsElement.appendChild(button);
            });
        } else if (!options.buttons && options.showCloseIcon === false) {
            console.warn("MyGlobalModal: Modal shown with no buttons and no close icon. User might get stuck.");
        }


        // Ensure the class for initial hiding is removed before showing
        this.modalElement.classList.remove('my-global-modal-hidden-on-load');
        this.modalElement.style.display = 'flex';

        if (options.onOpen && typeof options.onOpen === 'function') {
            options.onOpen();
        }

        // Set up auto-close timer if specified
        if (options.timerSeconds && typeof options.timerSeconds === 'number' && options.timerSeconds > 0) {
            this.autoCloseTimerId = setTimeout(() => {
                // Ensure the modal is still open and is the one that set this timer
                if (this.modalElement.style.display === 'flex' && this.currentConfig === options) {
                    this.close(false); // false, as it's not a direct UI close action
                }
            }, options.timerSeconds * 1000);
        }
    }

    close(triggeredByUiCloseAction = false) {
        if (!this.modalElement || this.modalElement.style.display === 'none') {
            // Already closed or not available, do nothing.
            return;
        }

        // Clear any active auto-close timer
        if (this.autoCloseTimerId) {
            clearTimeout(this.autoCloseTimerId);
            this.autoCloseTimerId = null;
        }

        this.modalElement.style.display = 'none';

        const onCloseCallback = this.currentConfig.onClose; // Store before clearing currentConfig

        // It's important to clear currentConfig before calling onClose,
        // in case onClose tries to show another modal immediately.
        const closedConfig = this.currentConfig;
        this.currentConfig = {};


        if (onCloseCallback && typeof onCloseCallback === 'function') {
            onCloseCallback({ triggeredByUiCloseAction, config: closedConfig });
        }
    }
}