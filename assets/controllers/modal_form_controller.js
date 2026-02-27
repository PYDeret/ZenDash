import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        this.element.addEventListener('turbo:submit-end', (event) => {
            if (event.detail.success) {
                const modalElement = document.getElementById('create-widget-modal');
                const instance = M.Modal.getInstance(modalElement);
                if (instance) {
                    instance.close();
                }

                this.element.reset();
            }
        });
    }
}
