(function ($) {

    $.entwine('ss', function ($) {
        $('[name=FocusKeyword],[name=SEOTitle],[name=MetaDescription]').entwine({
            onmatch: function () {
                SEOCheck();
            },
            onkeyup: function () {
                SEOCheck();
            }
        });
    });

    $.entwine('ss', function ($) {
        $('.toastSEOMetaText').entwine({
            onkeyup: function () {
                MetaLength();
            },
            onmatch: function () {
                MetaLength();
            }
        });
    });

    $.entwine('ss', function ($) {
        $('.focusWords').entwine({
            onmatch: function () {
                $('.focusWords').autocomplete({
                    source   : function (request, response) {
                        $.ajax({
                            url     : "//suggestqueries.google.com/complete/search",
                            dataType: "jsonp",
                            data    : {
                                client: 'firefox',
                                q     : request.term
                            },
                            success : function (data) {
                                response(data[1]);
                            }
                        });
                    },
                    minLength: 3
                });
            }
        });
    });

    /**
     * Test the length of the meta description
     * and warn if going over the recommended limit
     */
    function MetaLength() {
        var $count        = $('.toastSeoChars');
        var length        = 156;
        var currentLength = $('[name=MetaDescription]').val().length;
        if (currentLength <= length) {
            $count.text(length - currentLength).css({ fontWeight: 'bold', color: 'green' });
        } else {
            $count.text(length - currentLength).css({ fontWeight: 'bold', color: 'red' });
        }

    }

    /**
     * Test our fields to see if we
     * have good SEO practices in place
     */
    function SEOCheck() {

        function aContainsB(a, b, log) {
            var words = a.split(' ');
            for (var x = 0; x < words.length; x++) {
                if (words[x].length > 0) {
                    if (words[x].toLowerCase() == b.toLowerCase().trim()) {
                        return true
                    }
                }
            }
        }

        var keywords = $('[name=FocusKeyword]').val();

        /**
         * Test to see if any of our keywords
         * appear in the page URL
         */
        var pageURL  = $('.preview').text().replace(window.location.origin, '');
        var cleanURL = pageURL.substr(pageURL.lastIndexOf('/') + 1);
        var URLArray = cleanURL.toLowerCase().split('-');
        var URLCount = 0;

        for (var a = 0; a < URLArray.length; a++) {
            if (aContainsB(keywords, URLArray[a])) {
                URLCount++;
            }
        }

        if (URLCount > 0) {
            $('.toastURLMatch').text(' Yes (' + URLCount + ')').css({ color: 'green' });
        } else {
            $('.toastURLMatch').text(' No (' + URLCount + ')').css({ color: 'red' });
        }

        /**
         * Test to see if any of our keywords
         * appear in the pages' first paragraph
         */
        var summary      = $('.toastSEOSummaryText').text().replace(/[^a-zA-Z ]/g, "");
        var summaryArray = summary.toLowerCase().split(' ');
        var summaryCount = 0;

        for (var b = 0; b < summaryArray.length; b++) {
            if (aContainsB(keywords, summaryArray[b].trim(), true)) {
                summaryCount++;
            }
        }

        if (summaryCount > 0) {
            $('.toastSEOSummary').text(' Yes (' + summaryCount + ')').css({ color: 'green' });
        } else {
            $('.toastSEOSummary').text(' No (' + summaryCount + ')').css({ color: 'red' });
        }

        /**
         * Test to see if any of our keywords
         * appear in the pages' meta description
         */
        var meta      = $('[name=MetaDescription]').val().replace(/[^a-zA-Z ]/g, "");
        var metaArray = meta.toLowerCase().split(' ');
        var metaCount = 0;

        for (var c = 0; c < metaArray.length; c++) {
            if (aContainsB(keywords, metaArray[c].trim())) {
                metaCount++;
            }
        }

        if (metaCount > 0) {
            $('.toastSEOMeta').text(' Yes (' + metaCount + ')').css({ color: 'green' });
        } else {
            $('.toastSEOMeta').text(' No (' + metaCount + ')').css({ color: 'red' });
        }

        /**
         * Test to see if any of our keywords
         * appear in the pages' title
         */
        var title      = $('[name=Title]').val().replace(/[^a-zA-Z ]/g, "");
        var titleArray = title.toLowerCase().split(' ');
        var titleCount = 0;

        for (var c = 0; c < titleArray.length; c++) {
            if (aContainsB(keywords, titleArray[c].trim())) {
                titleCount++;
            }
        }

        if (titleCount > 0) {
            $('.toastSEOTitle').text('Yes (' + titleCount + ')').css({ color: 'green' });
        } else {
            $('.toastSEOTitle').text('No (' + titleCount + ')').css({ color: 'red' });
        }

        var tmpl = '';

        tmpl += '<h2 style="color:#1a0dab;font-family: arial,sans-serif;font-weight:normal;font-size:18px;margin-bottom:0;">' + $('[name=SEOTitle]').val() + '</h2>';
        tmpl += '<p style="color:#006621;font-family: arial,sans-serif;font-weight:normal;font-size:14px;    margin-bottom: 1.8px;">' + $('.preview').text().trim().replace('http://', '') + '/<span class="mn-dwn-arw" style="    border-style: solid;border-width: 5px 4px 0 4px !important;border-color: #006621 transparent;margin-top: -4px;margin-left: 3px;left: 0;    position: relative;top: 13px;left: 2px;"></span></p>';
        tmpl += '<p style="line-height: 18px;color:#545454;font-family: arial,sans-serif;font-weight:normal;font-size:13px;">' + $('[name=MetaDescription]').val() + '</p>';

        $('.toastSEOSnippet').html(tmpl);

    }

})(jQuery);






