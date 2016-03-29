<?php
/**
 * tobiju
 *
 * @link      https://github.com/tobiju/bookdown-bootswatch-templates for the canonical source repository
 * @copyright Copyright (c) 2015-2016 Tobias Jüschke
 * @license   https://github.com/tobiju/bookdown-bootswatch-templates/blob/master/LICENSE.txt New BSD License
 */
?>
<script src="https://code.jquery.com/jquery-2.2.0.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"
        integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS"
        crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lunr.js/0.6.0/lunr.min.js"></script>
<script src="http://bartaz.github.io/sandbox.js/jquery.highlight.js"></script>
<script src="https://tobiju.github.io/share/prismjs/main.js"></script>
<script src="https://tobiju.github.io/share/prismjs/prism.js"></script>
<script type="text/javascript">

    function Search() {
        this.store = {};
        this.index = lunr(function () {
            this.ref('id');
            this.field('title', {boost: 10});
            this.field('content');
        });
        this.searchResults = $('.js-search-results').addClass('list-search-results');
    }

    Search.prototype = {
        constructor: Search,
        init: function () {
            this.createIndex();
            this.bindEvents();
        },
        createIndex: function () {
            var $this = this;

            $.getJSON("/index.json", function (data) {
                $.each(data, function (key, item) {
                    $this.index.add({
                        id: item.id,
                        title: item.title,
                        content: item.content
                    });

                    $this.store[item.id] = {
                        href: item.id,
                        title: item.title,
                        content: item.content
                    }
                });
            });
        },
        bindEvents: function () {
            var $this = this;

            $('html').on('click', {}, $this.close);
            $('.js-search-input, .js-search-results').on('click', {}, $this.click);
            $('.js-search-input').on('focus', {}, $this.focus)
                .on('keyup', {
                    index: $this.index,
                    store: $this.store,
                    searchResults: $this.searchResults,
                    cropText: $this.cropText
                }, $this.search)
                .on('keydown', {
                    'close': $this.close
                }, $this.navigation);
        },
        click: function (event) {
            event.stopPropagation();
        },
        focus: function (event) {
            $(this).data.searchInputWidth = $(this).css('width');
            $(this).animate({
                'width': 600
            }, 500);
        },
        close: function (event) {
            $('.js-search-results').hide();
            $('.js-search-input').animate({
                'width': $(this).data.searchInputWidth
            }, 500);
        },
        search: function (event) {
            if (event.keyCode == 13 || event.keyCode == 38 || event.keyCode == 40) {
                return;
            }
            var query = $(this).val(),
                results = event.data.index.search(query);

            if (!results.length) {
                event.data.searchResults.empty();
                return;
            }

            var resultsList = results.reduce(function (ul, result) {
                var item = event.data.store[result.ref];
                var title = $('<b>').text(item.title);

                var cropText = event.data.cropText(item.content, query);
                if (cropText.length === 0) {
                    cropText = $('<p>').html(item.content.substring(0, 120) + "...");
                }
                var content = content = $('<p>').html(cropText);

                var div = $('<div>')
                    .append(title)
                    .append(content);
                var a = $('<a>').attr('href', item.href)
                    .append(div);
                var li = $('<li>')
                    .append(a);
                ul.append(li);

                return ul;
            }, $('<ul>'));

            event.data.searchResults.html(resultsList);

            $('.js-search-results').show();
            $(".js-search-results li:first-child").addClass('selected');
        },
        cropText: function (content, query) {
            var cropedText = '';
            var re = new RegExp("\\s?(.{0,30})?" + query + ".*?\\b(.{0,30}.)?\\s?", "gi");

            $.each(content.match(re), function (key, value) {
                cropedText += '...' + value + '...';
            });

            return cropedText;
        },
        navigation: function (event) {
            var selected = null;
            var listSelector = ".js-search-results ul";
            var listItemSelector = listSelector + " li";
            var selectedListItemSelector = listItemSelector + ".selected";
            var selectedListItemSelectorAnchor = listItemSelector + ".selected a";

            // enter
            if (event.keyCode == 13) {
                event.preventDefault();
                event.data.close();
                window.location.replace($(selectedListItemSelectorAnchor).attr('href'));
            }

            // up
            if (event.keyCode == 38) {
                selected = $(selectedListItemSelector);
                $(listItemSelector).removeClass("selected");

                if (selected.prev().length == 0) {
                    selected.siblings().last().addClass("selected").focus();
                } else {
                    selected.prev().addClass("selected").focus();
                }
                $(listSelector).scrollTop($(selectedListItemSelector).position().top);
            }

            // down
            if (event.keyCode == 40) {
                selected = $(selectedListItemSelector);
                $(listItemSelector).removeClass("selected");

                if (selected.next().length == 0) {
                    selected.siblings().first().addClass("selected").focus();
                } else {
                    selected.next().addClass("selected").focus();
                }
                $(listSelector).scrollTop($(selectedListItemSelector).position().top);
            }
        }
    };

    $(function () {
        var search = new Search();
        search.init();
    });
</script>
