import './stimulus_bootstrap.js';
import M from 'materialize-css';
import './styles/app.css';

window.M = M;

function initMaterialize(rootElement = document) {
    document.querySelectorAll('.lean-overlay, .modal-overlay').forEach(el => el.remove());
    M.AutoInit(rootElement);

    const dropdowns = rootElement.querySelectorAll('.dropdown-trigger');
    if (dropdowns.length > 0) {
        M.Dropdown.init(dropdowns, { constrainWidth: false, coverTrigger: false });
    }
}

document.addEventListener('turbo:load', () => {
    initMaterialize();
});

document.addEventListener('turbo:before-stream-render', (event) => {
    setTimeout(() => {
        const root = event.target.parentElement;
        if (root) {
            initMaterialize(root);
        }
    }, 0);
});
