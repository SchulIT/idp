document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[data-trigger="metadata-xml"]').forEach(function(button) {
        button.addEventListener('click', function(event) {
            event.preventDefault();

            button.disabled = true;

            let endpoint = button.getAttribute('data-endpoint');
            let inputEl = document.getElementById(button.getAttribute('data-input-id'));
            let urlEl = document.getElementById(button.getAttribute('data-url-id'));
            let entityIdEl = document.getElementById(button.getAttribute('data-entityid-id'));
            let acsEl = document.getElementById(button.getAttribute('data-acs-id'));
            let certificateEl = document.getElementById(button.getAttribute('data-cert-id'));

            let xhr = new XMLHttpRequest();
            xhr.responseType = 'json';
            xhr.onload = function () {
                let response = xhr.response;
                if (xhr.status >= 200 && xhr.status < 300) {
                    urlEl.value = response.entity_id;
                    entityIdEl.value = response.entity_id;
                    certificateEl.value = response.certificate;

                    for(let acs of response.acsUrls) {
                        let $button = document.querySelector('button[data-collection=acs]');

                        if($button === null) {
                            continue;
                        }

                        $button.click();

                        let $div = document.querySelector('div[data-collection=acs]');
                        let $input = $div.lastChild.querySelector('input');
                        $input.value = acs;
                    }
                } else {
                    console.error(response);
                }

                button.disabled = false;
            };
            let url = inputEl.value.trim();

            if(url === '') {
                return;
            }

            xhr.open('GET', endpoint + '?url=' + url);
            xhr.setRequestHeader('Accept', 'application/json');
            xhr.send();
        });
    });
});