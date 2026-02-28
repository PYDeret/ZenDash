import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        this.handler = this._handleSubmitEnd.bind(this);
        this.element.addEventListener('turbo:submit-end', this.handler);
        this.modal = M.Modal.init(this.element, {
            onCloseEnd: () => {
                const frame = this.element.closest('turbo-frame');
                if (frame) frame.innerHTML = '';
            }
        });

        this.modal.open();
        this.reinitializeMaterialize();
    }

    disconnect() {
        this.element.removeEventListener('turbo:submit-end', this.handler);
        if (this.modal) {
            this.modal.destroy();
        }
    }

    _handleSubmitEnd(event) {
        const { success, fetchResponse } = event.detail;
        if (fetchResponse.response.ok && fetchResponse.response.status === 200) {
            this.modal.close();
        } else if (fetchResponse.response.status === 422) {
            setTimeout(() => this.reinitializeMaterialize(), 10);
        }
    }

    reinitializeMaterialize() {
        M.FormSelect.init(this.element.querySelectorAll('select'));
        M.updateTextFields();
    }
}
