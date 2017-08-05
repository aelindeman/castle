(function () {
    'use strict';

    function CastleJS() {
        return this;
    }

    var Config = {},
        Debug = false,
        Lang = {},
        Route = '';

    /**
     * Utility functions
     */
    var Utils = {

        /**
         * Caps the rate on calls to `callback` so that it will be fired only
         * once per `period` ms. `immediate` causes the function to be called
         * at the beginning of the period rather than the end.
         */
        debounce: function (callback, period, immediate) {
            var timeout;
            return function() {
                var context = this, args = arguments;
                var later = function() {
                    timeout = null;
                    if (!immediate) callback.apply(context, args);
                };
                var callNow = immediate && !timeout;
                clearTimeout(timeout);
                timeout = setTimeout(later, period);
                if (callNow) callback.apply(context, args);
            };
        },

        /**
         * Generates a safe 'short slug' based on the capital letters of the
         * input string.
         */
        shortSlugify: function (input) {
            return input.replace(/[^A-Z0-9]/g, '').toLowerCase();
        },

        /**
         * Generates a slug based on the input string.
         */
        slugify: function (input, separator) {
            separator = separator || '-';
            return input.toString()
                .toLowerCase()
                .replace(/\s+/g, separator)
                .replace(/[^\w\-]+/g, '')
                .replace(/\-\-+/g, separator)
                .replace(/^-+/, '')
                .replace(/-+$/, '');
        },

        /**
         * Picks the best-constrasting dark or light color using the YIQ method.
         */
        yiqContrast: function (color, dark, light) {
            color = color.replace(/[^0-9A-Fa-f]/, '');
            dark = dark === undefined ? '#2C3E50' : dark;
            light = light === undefined ? '#FFF' : light;

            var r = parseInt(color.substr(0, 2), 16),
                g = parseInt(color.substr(2, 2), 16),
                b = parseInt(color.substr(4, 2), 16);

            var yiq = ((r * 299) + (g * 587) + (b * 114)) / 1000;
            return (yiq >= 128) ? dark : light;
        },

    };

    CastleJS.prototype = {
        constructor: CastleJS,
        utils: Utils,

        /**
         * Initializes everything.
         */
        init: function (data) {
            if (data !== undefined) {
                Config = data.config || {};
                Debug = data.debug || false;
                Lang = data.lang || {};
                Route = data.route || 'unknown';
            }

            $('[data-toggle="tooltip"], .btn.btn-tooltip').tooltip({
                container: 'body'
            });

            // initialize more things for editor pages
            if (Route.endsWith('.create') || Route.endsWith('.edit')) {
                this.initAttachmentEditor();
                // this.initColorPicker();
                this.initEditorHotkeys();
                this.initResourceKeyValuePairFields();
                this.initShortSlugFields();
                this.initSlugFields();
                this.initSelectizeFields();
            }

            if (Route == 'docs.show') {
                this.initCodeHighlighting();
                this.initTableOfContents();
            }

            if (Route == 'clients.show') {
                this.initTypeFilter();
            }

            if (Route.endsWith('.revisions')) {
                this.initDiffViewer();
            }

            this.initCopyButtons();
            this.initDeletionConfirmation();
            this.initFooterPadding();
            this.initLabelColors();
            this.initSearchSuggestions();
            this.initVotingAjax();
        },

        /**
         * Initializes attachment editor lists.
         */
        initAttachmentEditor: function (selector) {
            selector = selector || 'select[data-editor="attachments"]';

            $(selector).each(function () {
                var select = $(this),
                    form = $('[data-editor-target="#' + select.attr('id') + '"]');

                form.find('[data-attachment]').each(function () {
                    var attachment = $(this),
                        rel = attachment.data('attachment');

                    attachment.find('button[data-role="remove"]').on('click', function () {
                        select.children('option[value="' + rel + '"]').prop('selected', false);
                        attachment.remove();
                    });
                });
            });
        },

        /**
         * Initializes highlight.js, if available.
         */
        initCodeHighlighting: function () {
            if (typeof hljs == 'object') {
                hljs.initHighlightingOnLoad();
            }
        },

        /**
         * Adds a shortcut button to copy the contents of text fields to the
         * user's clipboard.
         */
        initCopyButtons: function (selector) {
            selector = selector || '.copy-me';

            $(selector).click(function (event) {
                var input = $(this).closest('.input-group')
                    .children('input, textarea')
                    .focus();

                input.get(0).setSelectionRange(0, input.val().length);

                document.execCommand('copy');
            }).attr('title', 'Copy to clipboard');
        },

        /**
         * Initializes a preset palette of colors below color inputs.
         */
        initColorPicker: function (selector, palette) {
            selector = selector || 'input[type="color"]';

            palette = palette || [
                'fce94f', 'edd400', 'c4a000',
                'fcaf3e', 'f57900', 'ce5c00',
                'e9b96e', 'c17d11', '8f5902',
                '8ae234', '73d216', '4e9a06',
                '729fcf', '3465a4', '204a87',
                'ad7fa8', '75507b', '5c3566',
                'ef2929', 'cc0000', 'a40000',
                'd3d7cf', '888a85', '2e3436',
            ];

            var s = '';
            while (palette.length > 0) {
                var row = palette.splice(0, 6),
                    r = row.length;

                s += '<div class="color-selector">';
                while (r--) {
                    var color = row[r];
                    s += '<label class="color-selector-color"' +
                        'style="background-color: #' + color + ';" value="#' + color +
                        '"><input type="radio" name="color" value="#' + color +
                        '">&#x00a0;<span class="sr-only">Set color to #' + color + '</input></label>';
                }
                s += '</div>';
            }

            $(selector).after(s);
        },

        /**
         * Initializes click-wait-click-to-delete confirmation buttons.
         */
        initDeletionConfirmation: function (selector) {
            selector = selector || '[data-confirm="delete"]';

            $(selector).siblings('button[type="submit"]').hide();

            $(selector).on('click', function (e) {
                var orig = $(this),
                    real = orig.siblings('button[type="submit"]');

                orig.hide().blur();
                real.show().hover(function () {
                    $(this).prop('disabled', true)
                        .delay(625)
                        .queue(function (next) {
                            $(this).prop('disabled', false);
                            next();
                        })
                        .focus();
                }, function () {
                    $(this).stop().hide(0).blur();
                    orig.show(0);
                });
            });
        },

        /**
         * Initializes jsdiff if available.
         */
        initDiffViewer: function (selector) {
            selector = selector || '.revision-values';

            if (JsDiff === undefined) {
                console.warn('JsDiff not loaded');
                return false;
            }

            $(selector).each(function () {
                var fromContainer = fromContainer || $(this).find('.revision-old-value pre').first(),
                    toContainer = toContainer || $(this).find('.revision-new-value pre').first(),

                    diff = JsDiff.diffWords(fromContainer.text(), toContainer.text()),
                    viewer = $('<pre>'),

                    make = function(part, added, removed, neither) {
                        return part.added ? added :
                            part.removed ? removed : neither;
                    };

                diff.forEach(function (part) {
                    var element = make(part, '<ins>', '<del>', '<span>');
                    $(element).text(part.value).appendTo(viewer);
                });

                $(this).empty().append(viewer);
            });
        },

        /**
         * Override some of the system's hotkeys for editor forms.
         */
        initEditorHotkeys: function (selector) {
            selector = selector || '.item-editor .form-control';

            $(selector).on('keydown', function (e) {
                if ((e.ctrlKey || e.metaKey) && e.which === 83) {
                    e.preventDefault();
                    $(selector).closest('form').get(0).submit();
                    return false;
                }
            });
        },

        /**
         * Recalculates the bottom padding when resizing to accomodate the
         * footer.
         */
        initFooterPadding: function (selector, layout) {
            selector = selector || '.castle-footer';
            layout = layout || '.castle-layout';

            $('html, body').css({
                'height': '100%'
            });

            $(layout).css({
                'min-height': '100%',
                'position': 'relative'
            });

            $(selector).css({
                'position': 'absolute',
                'bottom': '0',
                'left': '0',
                'right': '0',
                'width': '100%'
            });

            $(window).on('resize', Utils.debounce(function() {
                $(layout).css({
                    'padding-bottom': $(selector).outerHeight() + 'px'
                });
            }, 200)).trigger('resize');
        },

        /**
         * Colorizes labels with a color attribute.
         */
        initLabelColors: function (selector) {
            selector = selector || '[data-color]';

            $(selector).each(function () {
                var el = $(this),
                    bg = el.data('color'),
                    fg = Utils.yiqContrast(el.data('color'));

                if (el.data('colorProperties') !== undefined) {
                    var props = el.data('colorProperties').split(','),
                        rules = {};
                    $.each(props, function (i) {
                        rules[props[i]] = bg;
                    });
                    el.css(rules);
                } else {
                    el.css({
                        backgroundColor: bg,
                        color: fg
                    });
                }
            });
        },

        /**
         * Adds dynamic addable and removeable key-value fields for resource
         * editor views.
         */
        initResourceKeyValuePairFields: function (selector) {
            selector = selector || '[data-resource-editor]';

            $(selector).each(function () {
                // add row button
                $(this).siblings('.add-row').on('click', function () {
                    var row = $(selector).children().last().clone(true);
                    row.find('.help-block').remove();
                    row.find('input[type="text"]').val('');
                    row.appendTo(selector);
                });

                // remove row button
                $(this).find('.remove-row').on('click', function () {
                    var row = $(this).closest('.row');
                    if (row.siblings().length > 0) {
                        row.remove();
                    } else {
                        // don't remove the last row
                        row.find('input[type="text"]').val('');
                    }
                });
            });
        },

        /**
         * Recommends items before a full search.
         */
        initSearchSuggestions: function (selector, into) {
            selector = selector || '#castle-search';
            into = into || '#castle-search-suggestions';

            var context = this,
                timeout,
                dropdown,
                dropdownItems,
                keyNavSelection,
                fadeTime = 250,

                // create or show the suggestions dropdown
                showDropdown = function (contents) {
                    dropdown = $(into).length ?
                        $(into) :
                        $('<div>').attr('id', into.replace('#', '')).insertAfter(selector);

                    dropdown.html(contents).show();

                    keyNavSelection = 0;

                    dropdownItems = dropdown.find('a');
                    dropdownItems.each(function(index) {
                        $(this).data('index', index);
                    }).first().addClass('selected');

                    context.initLabelColors();

                    dropdown.on('mouseenter', 'a', function() {
                        keyNavSelection = $(this).data('index');
                        dropdownItems.removeClass('selected')
                            .eq(keyNavSelection)
                            .addClass('selected');
                    });

                    $(selector).on('blur', function() {
                        dropdown.fadeOut(fadeTime);
                        }).on('focus', function() {
                        if (dropdown.length) {
                            dropdown.show();
                        }
                    });
                },

                // hide dropdown and clear selection
                hideDropdown = function() {
                    if (dropdown) {
                        dropdown.fadeOut(fadeTime).remove();
                        keyNavSelection = undefined;
                    }
                },

                // perform search
                search = Utils.debounce(function() {
                    $.ajax({
                        method: 'GET',
                        url: '/search',
                        data: {
                            term: $(selector).val()
                        },
                        dataType: 'html',
                        success: showDropdown
                    });
                }, 200);

            // handle keyboard navigation
            $(selector).on('keydown', function(e) {
                var k = e.keyCode;

                if (dropdown && (k == 13 || k == 27 || k == 38 || k == 40)) {
                    e.preventDefault();

                    switch (k) {
                        case 27: // escape - clear input
                            $(selector).val('');
                            hideDropdown();
                            break;

                        case 13: // enter - go to selected item
                            var url = dropdownItems.eq(keyNavSelection).attr('href');
                            window.location.href = url;
                            break;

                        case 40: // down arrow - select item
                            if (keyNavSelection < dropdownItems.length - 1) {
                                keyNavSelection ++;
                                dropdownItems.removeClass('selected')
                                    .eq(keyNavSelection)
                                    .addClass('selected');
                            }
                            break;

                        case 38: // up arrow - select item
                            if (keyNavSelection > 0) {
                                keyNavSelection --;
                                dropdownItems.removeClass('selected')
                                    .eq(keyNavSelection)
                                    .addClass('selected');
                            }
                            break;
                    }

                    return false;
                }
            })

            // handle input suggestions
            .on('input', search);
        },

        /**
         * Initializes Selectize.js on 'taggable' fields.
         */
        initSelectizeFields: function (selector) {
            selector = selector || '.taggable';

            var colors = {}, // keep track of colors

                // generates markup for input tokens
                getItemColor = function (field, key) {
                    return !colors[field][key] ?
                        '' :
                        ' style="' +
                        'background-color: ' + colors[field][key] + '; ' +
                        'color: ' + Utils.yiqContrast(colors[field][key]) + ';' +
                        '"';
                },

                // generates markup for input dropdown
                getOptionColor = function (field, key) {
                    return !colors[field][key] ?
                        '' :
                        '<span style="color: ' + colors[field][key] + ';' +
                        '">&#x25cf;&#x00a0;</span>';
                },

                // returns the appropriate 'create' function for Selectize.js,
                // depending on the form element
                getCreateFunction = function (element) {
                    switch (element.attr('id')) {

                        // handled by controller after submission
                        case 'resourceType':
                            return true;

                        // handled by AJAX before submission
                        case 'tags':
                            return createTag;

                    }

                    return false;
                },

                // performs an AJAX request to create new tags dynamically
                createTag = function (input, callback) {
                    $.ajax({
                        type: 'POST',
                        url: '/tags',
                        dataType: 'json',
                        data: {
                            'name': input,
                            'slug': Utils.slugify(input),
                            '_token': $('input[name="_token"]').val()
                        },
                        success: function (result) {
                            callback({
                                text: result.name,
                                value: result.id,
                                color: result.color
                            });
                        },
                        error: function (result) {
                            // todo: display error message to user
                            callback();
                        }
                    });
                };

            $(selector).each(function () {
                var el = $(this),
                    field = el.attr('id'),
                    allowCreate = el.data('create');

                // remember element colors in an array
                colors[field] = {};
                el.children('option').each(function () {
                    colors[field][$(this).val()] = $(this).data('color');
                });

                el.selectize({
                    create: getCreateFunction(el),
                    render: {
                        item: function (data, escape) {
                            return '<div class="item"' +
                                getItemColor(field, escape(data.value)) + '>' +
                                escape(data.text) + '</div>';
                        },
                        option: function (data, escape) {
                            return '<div class="option">' +
                                getOptionColor(field, escape(data.value)) +
                                escape(data.text) + '</div>';
                        }
                    },
                    onOptionAdd: function (value, data){
                        colors[field][value] = data.color;
                    }
                });
            });
        },

        /**
         * Initializes 'short slug' input filters.
         */
        initShortSlugFields: function (selector) {
            selector = selector || 'input[data-short-slug]';

            $(selector).each(function () {
                var sourceField = $('#' + $(this).data('shortSlug')),
                    slugField = $(this);

                if (sourceField.length > 0) {
                    sourceField.on('keyup', function (e) {
                        slugField.val(Utils.shortSlugify(sourceField.val()));
                    });
                }
            });
        },

        /**
         * Initializes 'slug' input filters.
         */
        initSlugFields: function (selector) {
            selector = selector || 'input[data-slug]';

            $(selector).each(function () {
                var sourceField = $('#' + $(this).data('slug')),
                    slugField = $(this);

                if (sourceField.length > 0) {
                    sourceField.on('keyup', function (e) {
                        slugField.val(Utils.slugify(sourceField.val()));
                    });
                }

                slugField.on('blur', function (e) {
                    slugField.val(Utils.slugify(slugField.val()));
                });
            });
        },

        /**
         * Creates a table of contents of `selector`.
         * (derived from http://stackoverflow.com/a/187946/262640)
         */
        initTableOfContents: function(selector) {
            selector = selector || '[data-toc]';

            var content = $(selector).data('toc'),
                ids = {},

                // unique-ify IDs for TOC
                getUniqueId = function(name) {
                    name = Utils.slugify(name);

                    for (var anchor in ids) {
                        if (anchor == name) {
                            return name + '-' + (ids[anchor] ++);
                        }
                    }

                    ids[name] = 1;
                    return name;
                };

            // get the IDs already on the page
            $('[id]').each(function() {
                ids[this.id] = 1;
            });

            var toc = '',
                level = 0;

            // collect and id-ify headers
            $(content).html(
                $(content).html().replace(
                    /<h([1-3])>(.*?)<\/h([1-3])>/gi,
                    function (str, openLevel, titleText, closeLevel) {
                        if (openLevel != closeLevel) {
                            return str;
                        }

                        if (openLevel > level) {
                            toc += (new Array(openLevel - level + 1)).join('<ul class="nav">');
                        } else if (openLevel < level) {
                            toc += (new Array(level - openLevel + 1)).join('</ul>');
                        }

                        level = parseInt(openLevel);

                        titleText = titleText.replace(/(<([^>]+)>)/ig, ''); // strip tags
                        var anchor = getUniqueId(titleText);

                        toc += '<li><a href="#' + anchor + '">' + titleText +
                            '</a></li>';

                        return '<h' + openLevel + ' id="' + anchor + '">' +
                            titleText + '</h' + closeLevel + '>';
                    }
                )
            );

            if (level) {
                toc += (new Array(level + 1)).join('</ul>');
            }

            $(selector).html(toc);

            // don't apply affix and scrollspy on small screens
            $(window).on('resize', function() {
                if ($(window).width() >= 992) {
                    $(selector).parent().stick_in_parent({
                        offset_top: 18
                    });

                    $('body').scrollspy({
                        offset: 18,
                        target: selector
                    });
                } else {
                    $(selector).parent().trigger('sticky_kit:detach');
                }
            }).trigger('resize');
        },

        /**
         * Filters resource types on client views.
         */
        initTypeFilter: function (selector) {
            selector = selector || '[data-type-filter]';

            $(selector).each(function () {
                var el = $(this),
                    target = $('#' + el.data('typeFilter'));

                target.on('change', function () {
                    el.find('[data-type]').each(function () {

                        if (
                            target.val() === '' ||
                            $(this).data('type') == target.val()
                        ) {
                            $(this).show();
                        } else {
                            $(this).hide();
                        }

                    });
                }).trigger('change');
            });
        },

        /**
         * Set up voting buttons to work via AJAX rather than regular forms,
         * which cause a redirect.
         */
        initVotingAjax: function (selectors) {
            selectors = $.extend({}, selectors, {
                container: '.item-vote-buttons',
                score: '[data-vote-score]',
                voteButton: '[data-vote-button]',
                voteUp: '[data-vote-button="up"]',
                voteDown: '[data-vote-button="down"]',
            });

            $(selectors.container).each(function () {
                var voteItem = $(this),
                    voteItemForm = voteItem.closest('form').first(),
                    voteItemCsrfToken = voteItemForm.children('input[name="_token"]')
                        .first()
                        .val(),
                    voteBeforeSendCallback = function (xhr) {
                        // todo: maybe change css to { cursor: 'wait' }
                        // while waiting for the request to complete?
                    },
                    voteSuccessCallback = function (data, status) {
                        var value = String(data.vote.value),
                            button = {
                                '1': voteItem.find(selectors.voteUp),
                                '0': false,
                                '-1': voteItem.find(selectors.voteDown),
                            }[value];

                        voteItem.find(selectors.voteButton)
                            .removeClass('active');

                        voteItem.find(selectors.score)
                            .text(data.score);

                        if (button) {
                            button.addClass('active');
                        }
                    },
                    voteFailureCallback = function (data, status) {
                        // todo: better vote error handling (display a message)
                        console.log([status, data]);
                    },
                    ajaxBaseSettings = {
                        url: voteItemForm.attr('action'),
                        method: 'POST',
                        cache: false,
                        dataType: 'json',
                        data: {
                            '_token': voteItemCsrfToken
                        },
                        beforeSend: voteBeforeSendCallback,
                        success: voteSuccessCallback,
                        error: voteFailureCallback,
                    };

                $(this).children(selectors.voteDown).on('click', function(event) {
                    event.preventDefault();

                    var voteDownSettings = $.extend(true, {}, ajaxBaseSettings);
                    voteDownSettings.data.vote = $(this).hasClass('active') ?
                        'none' :
                        'down';

                    $.ajax(voteDownSettings);
                });

                $(this).children(selectors.voteUp).on('click', function(event) {
                    event.preventDefault();

                    var voteUpSettings = $.extend(true, {}, ajaxBaseSettings);
                    voteUpSettings.data.vote = $(this).hasClass('active') ?
                        'none' :
                        'up';

                    $.ajax(voteUpSettings);
                });
            });
        },

    };

    if (typeof window == 'object') {
        window.CastleJS = new CastleJS();
    }

})();
