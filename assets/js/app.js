require('../css/app.scss');

let bsn = require('bootstrap.native');
require('../../vendor/schoolit/common-bundle/Resources/assets/js/polyfill');
require('../../vendor/schoolit/common-bundle/Resources/assets/js/menu');

import Choices from "choices.js";

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[title]').forEach(function(el) {
        new bsn.Tooltip(el, {
            placement: 'bottom'
        });
    });

    document.querySelectorAll('select[data-choice=true]').forEach(function(el) {
        new Choices(el, {
            itemSelectText: ''
        });
    });
});