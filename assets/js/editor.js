import EasyMDE from 'easymde';

document.addEventListener('DOMContentLoaded', function(event) {
    document.querySelectorAll('[data-editor=markdown]').forEach(function(el) {
        if(el.classList.contains('md-textarea-hidden')) {
            /*
             * Somehow, when adding the markdown editor to an textarea,
             * this event is triggered again (with the hidden textarea)...
             */
            return;
        }

        if(el.getAttribute('data-preview') === null) {
            console.error('You must provide an URL which returns the markdown preview');
            return;
        }

        let previewUrl = el.getAttribute('data-preview');

        let options = {
            autoDownloadFontAwesome: false,
            autofocus: false,
            autosave: {
                enabled: false
            },
            element: el,
            placeholder: '',
            toolbar: [
                'bold', 'italic', 'heading', '|', 'unordered-list', 'ordered-list', '|', 'link',
                'image', '|', 'preview', 'side-by-side', 'fullscreen', '|', 'guide'
            ],
            previewRender: function(text, preview) {
                let request = new XMLHttpRequest();
                request.open('POST', previewUrl, true);
                request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');

                request.onload = function() {
                    if(request.status >= 200 && request.status < 400) {
                        preview.innerHTML = request.responseText;
                    }
                };

                request.send(text);

                return 'Laden...';
            },
            spellChecker: false,
            status: false
        };

        new EasyMDE(options);
    });
});
