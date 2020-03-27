require('../css/app.scss');

let bsn = require('bootstrap.native');
require('../../vendor/schoolit/common-bundle/Resources/assets/js/polyfill');
require('../../vendor/schoolit/common-bundle/Resources/assets/js/menu');

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[title]').forEach(function(el) {
        new bsn.Tooltip(el, {
            placement: 'bottom'
        });
    });
});