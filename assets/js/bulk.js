let $bulkActionsContainer = document.querySelector('[data-role=bulk-actions]');
let $selectAllCheckbox = document.querySelector('[data-role=bulk-all]')
let $checkboxes = document.querySelectorAll('[data-role=bulk-item]');
let $input = document.querySelector('[data-role=bulk-values]');
let $counter = document.querySelector('[data-role=bulk-counter]');

$bulkActionsContainer.classList.add('d-none');

$selectAllCheckbox.addEventListener('change', function(event) {
    event.preventDefault();

    let $this = this;

    for(let $checkbox of $checkboxes) {
        $checkbox.checked = $this.checked;
    }

    updateValues();
});

for(let $checkbox of $checkboxes) {
    $checkbox.addEventListener('change', function(event) {
        updateValues();
    });
}

function updateValues() {
    let values = [ ];

    for(let $checkbox of $checkboxes) {
        if($checkbox.checked) {
            values.push($checkbox.value);
        }
    }

    $input.value = values.join(',');

    $counter.innerHTML = values.length;

    if(values.length !== 0) {
        $bulkActionsContainer.classList.remove('d-none');
    } else {
        $bulkActionsContainer.classList.add('d-none');
    }
}