import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        this.modal = M.Modal.init(this.element, {
            onCloseEnd: () => this.element.remove()
        });

        if (!this.element.classList.contains('open')) {
            this.modal.open();
        }

        this.reinitializeMaterialize();
    }

    reinitializeMaterialize() {
        M.FormSelect.init(this.element.querySelectorAll('select'));
        M.updateTextFields();
    }
}
