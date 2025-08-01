// js/ui/modals.js

// --- General Modal Elements ---
const allModals = document.querySelectorAll('.modal-v3');

// --- Helper: Open a modal ---
export function openModal(modalElement) {
    if (modalElement) {
        modalElement.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
}

// --- Helper: Close a modal ---
export function closeModal(modalElement) {
    if (modalElement) {
        modalElement.style.display = 'none';
        document.body.style.overflow = '';
    }
}

// --- Main Initialization for Modals ---
export function initModals() {
    allModals.forEach(modal => {
        modal.addEventListener('click', (event) => {
            if (event.target === modal) closeModal(modal);
        });
    });
}
