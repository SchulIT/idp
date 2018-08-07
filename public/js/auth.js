'use strict';

var u2fauth = u2fauth || {};

u2fauth.formId = 'u2fform';
u2fauth.authCodeId = '_auth_code';
u2fauth.keynameId = 'u2fkeyname';
u2fauth.pressButtonId = 'u2fbutton';
u2fauth.errorId = 'u2ferror';
u2fauth.errorContainerId = 'u2ferrorContainer';
u2fauth.retryButtonId = 'u2fretry';
u2fauth.successId = 'u2fsuccess';
u2fauth.errorTranslation = {
    1: 'An unknown error occurred,',
    2: 'The request was bad.',
    3: 'The client configuration is not supported.',
    4: 'The device is already registered or ineligible.',
    5: 'Timeout while waiting for the device.'
};

u2fauth.ready = function(fn) {
    if ('loading' !== document.readyState){
        fn();
    } else if (document.addEventListener) {
        document.addEventListener('DOMContentLoaded', fn);
    } else {
        document.attachEvent('onreadystatechange', function() {
            if ('loading' !== document.readyState)
                fn();
        });
    }
};

u2fauth.authenticate = function() {
    u2fauth.clearError();
    u2fauth.showPressButton();

    var form = document.getElementById(u2fauth.formId);
    var request = JSON.parse(form.dataset.request);

    console.log(form);

    u2f.sign(request[0].appId, request[0].challenge, request, function(data){
        u2fauth.hidePressButton();
        if(!data.errorCode) {
            u2fauth.showSuccess();
            u2fauth.submit(form, data);
        } else {
            u2fauth.showError(data.errorCode, u2fauth.authenticate);
        }
    });
};

u2fauth.register = function() {
    u2fauth.clearError();
    u2fauth.hideKeyname();
    u2fauth.showPressButton();

    var form = document.getElementById(u2fauth.formId);
    var request = JSON.parse(form.dataset.request);

    u2f.register(request[0].appId, [request[0]], request[1], function(data){
        u2fauth.hidePressButton();
        if(!data.errorCode) {
            u2fauth.submit(form, data);
        } else {
            u2fauth.showError(data.errorCode, u2fauth.register);
        }
    });
};

u2fauth.submit = function(form, data) {
    var codeField = document.getElementById(u2fauth.authCodeId);
    codeField.value = JSON.stringify(data);
    form.submit();
};

u2fauth.hideKeyname = function() {
    var keyname = document.getElementById(u2fauth.keynameId);
    keyname.style.display = 'none';
};

u2fauth.hidePressButton = function() {
    var pressButton = document.getElementById(u2fauth.pressButtonId);
    pressButton.style.display = 'none';
};

u2fauth.showPressButton = function() {
    var pressButton = document.getElementById(u2fauth.pressButtonId);
    pressButton.style.display = 'block';
};

u2fauth.clearError = function() {
    var errorContainer = document.getElementById(u2fauth.errorContainerId);
    errorContainer.style.display = 'none';

    var errorDisplay = document.getElementById(u2fauth.errorId);
    errorDisplay.innerText = '';
};

u2fauth.showError = function(error, callback) {
    var errorContainer = document.getElementById(u2fauth.errorContainerId);
    errorContainer.style.display = 'block';

    var errorDisplay = document.getElementById(u2fauth.errorId);
    errorDisplay.innerText = u2fauth.errorTranslation[error];

    var retryButton = document.getElementById(u2fauth.retryButtonId);
    retryButton.onclick = callback;
};

u2fauth.showSuccess = function() {
    var successDiv = document.getElementById(u2fauth.successId);
    successDiv.style.display = 'block';
};

u2fauth.ready(function(){
    var form = document.getElementById(u2fauth.formId);
    var type = form.dataset.action;

    if('auth' === type) {
        u2fauth.authenticate();
    }
});
