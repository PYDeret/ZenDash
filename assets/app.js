import './stimulus_bootstrap.js';
import M from 'materialize-css';
import './styles/app.css';

window.M = M;

function initMaterialize() {
    M.AutoInit();
    M.Dropdown.init(document.querySelectorAll('.dropdown-trigger'), {
        constrainWidth: false,
        coverTrigger: false
    });
}

document.addEventListener('turbo:load', initMaterialize);

document.addEventListener('turbo:submit-end', (event) => {
    if (event.detail.fetchResponse.response.status === 422) {
        console.log('adazffaf');
        window.hasFormError = true;
    }
});

document.addEventListener('turbo:render', () => {
    if (window.hasFormError) {
        console.log('adazd');
        const modalElem = document.querySelector('#create-widget-modal');
        if (modalElem) {
            const instance = M.Modal.init(modalElem);
            instance.open();
            M.updateTextFields();
            M.FormSelect.init(modalElem.querySelectorAll('select'));
        }

        window.hasFormError = false;
    }
});
