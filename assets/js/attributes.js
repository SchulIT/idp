+(function($) {
    'use strict';

    function deleteOption() {
        var $this = $(this);
        $this.closest('.form-inline').remove();
    }

    function addOption($collectionHolder) {
        // Get the data-prototype explained earlier
        var prototype = $collectionHolder.data('prototype');

        // get the new index
        var index = $collectionHolder.data('index');

        // Replace '__name__' in the prototype's HTML to
        // instead be a number based on how many items we have
        var newForm = prototype.replace(/__name__/g, index);

        // increase the index with one for the next item
        $collectionHolder.data('index', index + 1);

        // Display the form in the page in an li
        var $newFormLi = $('<li></li>').append(newForm);
        $collectionHolder.append($newFormLi);

        $newFormLi.find('.btn-delete').click(deleteOption);
    }

    $(document).ready(function() {
        // Get the ul that holds the collection of tags
        var $collectionHolder = $('ul.options');

        $collectionHolder.find('.btn-delete').click(deleteOption);

        // count the current form inputs we have (e.g. 2), use that as the new
        // index when inserting a new item (e.g. 2)
        $collectionHolder.data('index', $collectionHolder.find(':input').length);

        $('a.btn-add-option').on('click', function(e) {
            // prevent the link from creating a "#" on the URL
            e.preventDefault();

            // add a new tag form (see next code block)
            addOption($collectionHolder);
        });
    });
})(jQuery);