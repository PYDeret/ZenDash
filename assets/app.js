import './stimulus_bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

import M from 'materialize-css';

document.addEventListener('turbo:load', function() {
    const tabs = document.querySelectorAll('.tabs');
    M.Tabs.init(tabs);
    M.updateTextFields();
});
