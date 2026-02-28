import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        this.modal = M.Modal.init(this.element, {
            onCloseEnd: () => {
                this.element.closest('turbo-frame').innerHTML = '';
            }
        });

        this.modal.open();
        this.reinitializeMaterialize();
    }

    disconnect() {
        document.removeEventListener('turbo:submit-end', this._handleSubmitEnd);
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
