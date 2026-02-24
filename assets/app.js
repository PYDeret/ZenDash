import './stimulus_bootstrap.js';
import M from 'materialize-css';
import './styles/app.css';

function initMaterialize() {
    M.Sidenav.init(document.querySelectorAll('.sidenav'));
    M.Dropdown.init(document.querySelectorAll('.dropdown-trigger'), { constrainWidth: false, coverTrigger: false });
    M.Modal.init(document.querySelectorAll('.modal'));
    M.FormSelect.init(document.querySelectorAll('select'));
    M.Tabs.init(document.querySelectorAll('.tabs'));
    M.updateTextFields();
}

document.addEventListener('turbo:load', initMaterialize);
