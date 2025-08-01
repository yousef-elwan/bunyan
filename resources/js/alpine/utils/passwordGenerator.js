export class PasswordGenerator {
    constructor(modalId) {
        this.modal = document.getElementById(modalId);
        if (!this.modal) return;

        this.generatedInput = this.modal.querySelector('#generatedPasswordInput');
        this.regenerateBtn = this.modal.querySelector('#regeneratePasswordBtn');
        this.copyBtn = this.modal.querySelector('#copyPasswordBtn');
        this.copyBtnText = this.modal.querySelector('#copyBtnText');
        this.savedCheckbox = this.modal.querySelector('#passwordSavedCheckbox');
        this.closeBtn = this.modal.querySelector('#passwordModalCloseBtn');

        this.setupEventListeners();
    }

    setupEventListeners() {
        this.regenerateBtn?.addEventListener('click', () => this.regeneratePassword());
        this.copyBtn?.addEventListener('click', () => this.copyToClipboard());
        this.closeBtn?.addEventListener('click', () => this.closeModal());
        this.savedCheckbox?.addEventListener('change', () => {
            this.copyBtn.disabled = !this.savedCheckbox.checked;
        });
    }

    // generatePassword(length = 14) {
    //     const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()';
    //     return Array.from({ length }, () => chars[Math.floor(Math.random() * chars.length)]).join('');
    // }

    generatePassword(length = 14) {
        const upper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        const lower = 'abcdefghijklmnopqrstuvwxyz';
        const numbers = '0123456789';
        const symbols = '!@#$%^&*()'; // Assicurati che questi simboli corrispondano alla regola regex di Laravel
        const allChars = upper + lower + numbers + symbols;

        let password = '';
        // Assicura che la password contenga almeno un carattere di ogni tipo
        password += upper[Math.floor(Math.random() * upper.length)];
        password += lower[Math.floor(Math.random() * lower.length)];
        password += numbers[Math.floor(Math.random() * numbers.length)];
        password += symbols[Math.floor(Math.random() * symbols.length)];

        // Riempie il resto della password con caratteri casuali
        for (let i = password.length; i < length; i++) {
            password += allChars[Math.floor(Math.random() * allChars.length)];
        }

        // Mescola la password per evitare che i primi 4 caratteri siano prevedibili
        return password.split('').sort(() => 0.5 - Math.random()).join('');
    }


    regeneratePassword() {
        this.generatedInput.value = this.generatePassword();
        this.savedCheckbox.checked = false;
        this.copyBtn.disabled = true;
        if (this.copyBtnText) this.copyBtnText.textContent = this.translate('copy_button');
    }

    async copyToClipboard() {
        if (this.copyBtn.disabled) return;
        await navigator.clipboard.writeText(this.generatedInput.value);
        if (this.copyBtnText) this.copyBtnText.textContent = this.translate('copied_button');
        setTimeout(() => this.closeModal(), 1200);
    }

    openModal() {
        this.regeneratePassword();
        this.modal.style.display = 'flex';
    }

    closeModal() {
        this.modal.style.display = 'none';
    }

    translate(key) {
        return {
            'copy_button': 'Copy',
            'copied_button': 'Copied!'
        }[key] || key;
    }
}
