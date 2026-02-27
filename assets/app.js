import './stimulus_bootstrap.js';
import M from 'materialize-css';
import './styles/app.css';

window.M = M;

function initMaterialize(rootElement = document) {
    document.querySelectorAll('.lean-overlay, .modal-overlay').forEach(el => el.remove());
    M.AutoInit(rootElement);

    const dropdowns = rootElement.querySelectorAll('.dropdown-trigger');
    if (dropdowns.length > 0) {
        M.Dropdown.init(dropdowns, {
            constrainWidth: false,
            coverTrigger: false
        });
    }
}

document.addEventListener('turbo:load', () => {
    initMaterialize();
});

document.addEventListener('turbo:submit-end', (event) => {
    const response = event.detail.fetchResponse.response;
    if (response.status === 422) {
        window.hasFormError = true;
    }

    if (response.ok && response.status === 200) {
        const modalElem = document.querySelector('#create-widget-modal');
        const instance = M.Modal.getInstance(modalElem);
        if (instance) {
            instance.close();
        }

        const form = document.querySelector('#create-widget-form-element');
        if (form) {
            form.reset();
            M.updateTextFields();
        }

        window.hasFormError = false;
    }
});

document.addEventListener('turbo:render', () => {
    if (window.hasFormError) {
        const modalElem = document.querySelector('#create-widget-modal');
        if (modalElem) {
            const instance = M.Modal.init(modalElem);
            instance.open();
            M.updateTextFields();
        }

        window.hasFormError = false;
    }
});

document.addEventListener('turbo:before-stream-render', (event) => {
    const fallback = event.target;

    setTimeout(() => {
        initMaterialize(document.getElementById('widgets-container'));
    }, 0);
});
