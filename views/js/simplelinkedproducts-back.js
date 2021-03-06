/**
*  @author    Pierre Viéville <contact@pierrevieville.fr>
*  @copyright 2020 - Pierre Viéville
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  https://www.pierrevieville.fr
*/
var waitForJQuery = setInterval(function () {
    if (typeof $ != 'undefined') {

        $(document).ready(function () {
            $('.simple-linked-product-autocomplete').each(function () {
                loadAutocomplete($(this), false);
            });

            $('.simple-linked-product-autocomplete').on('buildTypeahead', function () {
                loadAutocomplete($(this), true);
            });
        });

        function loadAutocomplete(object, reset) {
            let autocompleteObject = $(object);
            let autocompleteFormId = autocompleteObject.attr('data-formid');
            let formId = '#' + autocompleteFormId + '-data .delete';
            let autocompleteSource = autocompleteFormId + '_source';

            if (true === reset) {
                $('#' + autocompleteFormId).typeahead('destroy');
            }

            $(document).on('click', formId, (e) => {
                e.preventDefault();

                window.modalConfirmation.create(window.translate_javascripts['Are you sure to delete this?'], null, {
                    onContinue: () => {
                        $(e.target).parents('.media').remove();
                        // Save current product after its related product has been removed
                        $('#submit').click();
                    }
                }).show();
            });

            document[autocompleteSource] = new Bloodhound({
                datumTokenizer: Bloodhound.tokenizers.whitespace,
                queryTokenizer: Bloodhound.tokenizers.whitespace,
                identify: function (obj) {
                    return obj[autocompleteObject.attr('data-mappingvalue')];
                },
                remote: {
                    url: autocompleteObject.attr('data-remoteurl'),
                    cache: false,
                    wildcard: '%QUERY',
                    transform: function (response) {
                        if (!response) {
                            return [];
                        }
                        return response;
                    }
                }
            });

            //define typeahead
            $('#' + autocompleteFormId).typeahead({
                limit: 20,
                minLength: 2,
                highlight: true,
                cache: false,
                hint: false,
            }, {
                display: autocompleteObject.attr('data-mappingname'),
                source: document[autocompleteFormId + '_source'],
                limit: 30,
                templates: {
                    suggestion: function (item) {
                        return '<div><img src="' + item.image + '" style="width:50px" /> ' + item.name + '</div>';
                    }
                }
            }).bind('typeahead:select', function (e, suggestion) {
                //if collection length is up to limit, return

                let formIdItem = $('#' + autocompleteFormId + '-data li');
                let autocompleteFormLimit = parseInt(autocompleteObject.attr('data-limit'));

                if (autocompleteFormLimit !== 0 && formIdItem.length >= autocompleteFormLimit) {
                    return false;
                }

                var value = suggestion[autocompleteObject.attr('data-mappingvalue')];
                if (suggestion.id_product_attribute) {
                    value = value + ',' + suggestion.id_product_attribute;
                }

                let tplcollection = $('#tplcollection-' + autocompleteFormId);
                let tplcollectionHtml = tplcollection.html().replace('%s', suggestion[autocompleteObject.attr('data-mappingname')]);

                var html = '<li class="media"><div class="media-left"><img class="media-object image" src="' + suggestion.image + '" /></div>';
                html += '<div class="media-body media-middle">' + tplcollectionHtml + '</div>';
                html += '<input type="hidden" name="' + autocompleteObject.attr('data-fullname') + '" value="' + parseInt(value) + '" /></li>';

                $('#' + autocompleteFormId + '-data').append(html);

            }).bind('typeahead:close', function (e) {
                $(e.target).typeahead('val', '');
            });
        }

        clearInterval(waitForJQuery);
    }
}, 10);



