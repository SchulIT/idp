document.addEventListener('DOMContentLoaded', function() {
    let characters = 'ABCDEFGHKLMNPQRSTUVWXYZ123456789';
    let blocks = 3;
    let characterPerBlock = 4;
    let crypto = window.crypto || window.msCrypto;

    function getRandomNumber() {
        let array = new Uint8Array(1);
        crypto.getRandomValues(array);

        return array[0];
    }

    document.querySelectorAll('[data-trigger=generate-code]').forEach(function(el) {
        el.addEventListener('click', function(event) {
            event.preventDefault();

            let targetSelector = el.getAttribute('data-target');
            if(targetSelector === null) {
                console.info('You must specify data-target.');
                return;
            }

            let target = document.querySelector(targetSelector);
            if(target === null) {
                console.info('Target element "' + targetSelector + '" was not found.');
                return;
            }

            let code = '';
            for(let blockIdx = 0; blockIdx < blocks; blockIdx++) {
                for(let charIdx = 0; charIdx < characterPerBlock; charIdx++) {
                    let randomIndex = getRandomNumber() % characters.length;
                    code += characters.charAt(randomIndex);
                }

                if(blockIdx + 1 < blocks) {
                    code += '-';
                }
            }

            target.value = code;
        });
    });
});