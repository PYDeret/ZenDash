import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['frame']
    static values = { url: String }

    changeType(event) {
        this.frameTarget.src = `${this.urlValue}?type=${event.target.value}`
    }
}
