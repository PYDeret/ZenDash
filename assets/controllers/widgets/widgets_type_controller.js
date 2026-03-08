import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['frame']
    static values = { url: String }

    connect() {
        const select = this.element.querySelector('select');
        if (select?.value && !this.frameTarget.children.length) {
            this.frameTarget.src = `${this.urlValue}?type=${select.value}`;
        }
    }

    changeType(event) {
        this.frameTarget.src = `${this.urlValue}?type=${event.target.value}`
    }
}
