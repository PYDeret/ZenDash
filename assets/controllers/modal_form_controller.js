import { Controller } from '@hotwired/stimulus';
import M from 'materialize-css';

export default class extends Controller {

    connect() {
        document.querySelectorAll('.lean-overlay, .modal-overlay').forEach(el => el.remove());
        this.modalInstance = M.Modal.getInstance(this.element);
        if (!this.modalInstance) {
            this.modalInstance = M.Modal.init(this.element);
        }

        this.modalInstance.open();

        const selects = this.element.querySelectorAll('select');
        if (selects.length > 0) {
            M.FormSelect.init(selects);
        }

        M.updateTextFields();
    }

    disconnect() {
        if (this.modalInstance) {
            this.modalInstance.destroy();
        }

        document.querySelectorAll('.modal-overlay').forEach(el => el.remove());
    }

    handleSubmitEnd(event) {
        if (event.detail.success) {
            if (this.modalInstance) {
                this.modalInstance.close();
            }

            this.element.querySelector('form')?.reset();
        }
    }
}
