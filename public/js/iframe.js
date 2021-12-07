// setup an "add a tag" link
const $addTagLink = $('<a href="#" class="btn btn-primary btn-green add_tag_link">Ajouter un produit</a>');
const $newLinkLi = $('<div></div>').append($addTagLink);

let $index = 0;

let height;

const sendPostMessage = () => {
    // if (height !== document.getElementById('container').offsetHeight) {
        height = document.getElementById('container').offsetHeight;
        window.parent.postMessage({
            frameHeight: height + 200
        }, '*');
    // }
}

window.onload = () => sendPostMessage();
window.onresize = () => sendPostMessage();
$(document).on('click touchend', '.add_tag_link',function (e) {
    sendPostMessage();
});
$(document).on('click touchend', '.remove-tag',function (e) {
    sendPostMessage();
});

$(document).ready(function () {
    const $collectionHolder = $('div.savArticle');

    if ($collectionHolder.length > 0) {
        $collectionHolder.append($newLinkLi);

        $collectionHolder.data('index', $collectionHolder.find(':input').length);

        addArticleSavForm($collectionHolder, $newLinkLi);
    }

    let $select2 = $('.select2entity');
    if ($select2.length > 0) {
        $select2.each(function (e) {
            $(this).select2entity({
                //Your parameters
                minimumInputLength: 2,
                language: {
                    inputTooShort: function () {
                        return 'Saisissez au moins 2 caractères';
                    }
                },
                ajax: {
                    processResults: displayRequiredElementsNotFound
                }
            });
        });
    }

    $(document).on('click', '.add_tag_link',function (e) {
        e.preventDefault();
        addArticleSavForm($collectionHolder, $newLinkLi);
        $('.select2entity').each(function(e) {
            $(this).select2entity({
                //Your parameters
                minimumInputLength: 2,
                ajax: {
                    processResults: displayRequiredElementsNotFound
                },
                language: {
                    inputTooShort: function () {
                        return 'Saisissez au moins 2 caractères';
                    }
                },
            });
        });
    });

    $('input[type="file"]').change(function (event) {
        var fileName = event.target.files[0].name;
        if (event.target.nextElementSibling!=null){
            if (event.target.files.length > 1) {
                event.target.nextElementSibling.innerText = event.target.files.length + ' fichiers'
            } else {
                event.target.nextElementSibling.innerText = fileName;
            }
        }
    });

    $(document).on('submit', 'form', function () {
        $('#sav_submit').prop('disabled', true);
        $('#loader').removeClass('d-none');
    });
});

function addArticleSavForm($collectionHolder, $newLinkLi) {
    const prototype = $collectionHolder.data('prototype');

    const index = $collectionHolder.data('index');

    const newForm = prototype.replace(/__name__/g, index);

    $collectionHolder.data('index', index + 1);
    $index = index;

    const $newFormLi = $('<div></div>').append(newForm);

    //$newFormLi.append(' <a href="#" class="float-right btn-danger delete-icon remove-tag"><i class="far fa-trash-alt"></i></a>');

    $newLinkLi.before($newFormLi);

    $('.remove-tag').click(function (e) {
        e.preventDefault();

        $(this).parent().remove();

        return false;
    });

    $('.nature-settings').each(function () {
        $(this).select2();
    });
    $('input[type="file"]').change(function (event) {
        var fileName = event.target.files[0].name;
        if (event.target.nextElementSibling!=null){
            if (event.target.files.length > 1) {
                event.target.nextElementSibling.innerText = event.target.files.length + ' fichiers'
            } else {
                event.target.nextElementSibling.innerText = fileName;
            }
        }
    });
}

function displayRequiredElementsNotFound(data, params) {
    var results, more = false, response = {};
    params.page = params.page || 1;

    if ($.isArray(data)) {
        results = data;
    } else if (typeof data == 'object') {
        // assume remote result was proper object
        results = data.results;
        more = data.more;
    } else {
        // failsafe
        results = [];
    }

    if (scroll) {
        response.pagination = {more: more};
    }
    response.results = results;
    let $element = $('#' + $(this)[0].$element.attr('id'));
    let $divToDisplay = $element.closest('div.row').parent('div').find('div.unknown');
    let $index = $(this)[0].$element.attr('id').split('_')[$(this)[0].$element.attr('id').split('_').length - 2];
    if (response.results.length <= 0) {
        $divToDisplay.removeClass('d-none');
        let $article = $('#sav_savArticles_' + $index + '_article');
        let $unknownArticle = $('#sav_savArticles_' + $index + '_unknownArticle');
        $unknownArticle.prop('required', 'required');
        $unknownArticle.val(params.term);
        $('#sav_savArticles_' + $index + '_fileUnknown').prop('required', 'required');
        $article.prop('required', false);
        $article.addClass('d-none');
        $article.select2('close');
        $article.parent('div.unknown-to-hide').addClass('d-none');
        $unknownArticle.focus();
        $(document).on('keyup', '#sav_savArticles_' + $index + '_unknownArticle', function () {
            if ($(this).val() === '') {
                $article.prop('required', true);
                $article.removeClass('d-none');
                $article.select2entity({
                    //Your parameters
                    minimumInputLength: 2,
                    width: '100%',
                    ajax: {
                        processResults: displayRequiredElementsNotFound
                    },
                    language: {
                        inputTooShort: function () {
                            return 'Saisissez au moins 2 caractères';
                        }
                    },
                });
                $article.parent('div.unknown-to-hide').removeClass('d-none');
                $unknownArticle.prop('required', false);
                $unknownArticle.parent('div.form-group').parent('div.unknown').addClass('d-none');
                $('#sav_savArticles_' + $index + '_fileUnknown').prop('required', false);
                $('#sav_savArticles_' + $index + '_fileUnknown').parent('div.custom-file').parent('div.form-group').parent('div.col-12').parent('div.unknown').addClass('d-none');
            }
        });
    } else {
        $divToDisplay.addClass('d-none');
        $('#sav_savArticles_' + $index + '_unknownArticle').prop('required', false);
        $('#sav_savArticles_' + $index + '_fileUnknown').prop('required', false);
        $('#sav_savArticles_' + $index + '_article').prop('required', true);
    }

    return response;
}
