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

    document.querySelectorAll('[data-trigger="submit"]').forEach(function (el) {
        el.addEventListener('change', function (event) {
            let confirmModalSelector = el.getAttribute('data-confirm');
            let form = this.closest('form');

            if(confirmModalSelector === null || confirmModalSelector === '') {
                form.submit();
                return;
            }

            let modalEl = document.querySelector(confirmModalSelector);
            let modal = new bsn.Modal(modalEl);
            modal.show();

            let confirmBtn = modalEl.querySelector('.confirm');
            confirmBtn.addEventListener('click', function(event) {
                event.preventDefault();
                event.stopImmediatePropagation();

                console.log(form);

                form.submit();
            });
        });
    });

    document.querySelectorAll('select[data-choice=true]').forEach(function(el) {
        new Choices(el, {
            itemSelectText: ''
        });
    });
});