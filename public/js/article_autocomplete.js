$(document).ready(function () {
    $(document).on('click', '.add_tag_link', function () {
        $('.js-article-autocomplete').each(function() {
            const autocompleteUrl = $(this).data('autocomplete_article');
            $(this).autocomplete({hint: false}, [
                {
                    source: function(query, cb) {
                        $.ajax({
                            url: autocompleteUrl+'?q='+query
                        }).then(function(data) {
                            cb(data.article);
                        });
                    },
                    displayKey: 'designation',
                    debounce: 500 // only request every 1/2 second
                }
            ])
        });
    })
})