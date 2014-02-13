$.Controller.extend('Paginator',
    /*
* @static
*/
    {
        defaults: {
            viewpath:'//components/user/paginator/views/'
        }
    },
    /*
* @prototype
*/
    {
        init: function(el, options){

//            $(el).append('<ul class="pagination"></ul>')

//            this.paginate_wrap = $(el).find('.paginate');
            this.paginate_wrap = $('<ul class="pagination"></ul>');

            this.opt = $.extend({
                items: 1, // Total number of items that will be used to calculate the pages.
                itemsOnPage: 1, // Number of items displayed on each page.
                pages: 0, // If specified, items and itemsOnPage will not be used to calculate the number of pages.
                displayedPages: 3, // How many page numbers should be visible while navigating. Minimum allowed: 3 (previous, current & next)
                edges: 1, // How many page numbers are visible at the beginning/ending of the pagination.
                currentPage: 1, // Which page will be selected immediately after init.
                hrefTextPrefix: '', // A string used to build the href attribute, added before the page number.
                hrefTextSuffix: '', //Another string used to build the href attribute, added after the page number.
                prevText: '&laquo',
                nextText: '&raquo',
                ellipseText: '&hellip;',
                cssStyle: 'light-theme',
                selectOnClick: true,//Set to false if you don't want to select the page immediately after click.
                onPageClick: function(pageNumber) {
                    // Callback triggered when a page is clicked
                    // Page number is given as an optional parameter
                },
                onInit: function() {
                // Callback triggered immediately after initialization
                }
            }, options || {});

            this.opt.pages = this.opt.pages ? this.opt.pages : Math.ceil(this.opt.items / this.opt.itemsOnPage) ? Math.ceil(this.opt.items / this.opt.itemsOnPage) : 1;
            this.opt.currentPage = this.opt.currentPage - 1;
            this.opt.halfDisplayed = this.opt.displayedPages / 2;

            if (this.opt.pages > 1)
                this._draw();

            this.opt.onInit();
        },

        _draw: function(){

            var o = this.opt,
            interval = this._getInterval(),
            i;

            this.paginate_wrap.empty();

            // Generate Prev link
            if (o.prevText) {
                this._appendItem(o.currentPage - 1, {
                    text: o.prevText,
                    classes: 'prev'
                });
            }
            // Generate start edges
            if (interval.start > 0 && o.edges > 0) {
                var end = Math.min(o.edges, interval.start);
                for (i = 0; i < end; i++) {
                    this._appendItem(i);
                }
                if (o.edges < interval.start && o.ellipseText) {
                    this.paginate_wrap.append('<li><span class="ellipse">' + o.ellipseText + '</span></li>');
                }
            }

            // Generate interval links
            for (i = interval.start; i < interval.end; i++) {
                this._appendItem(i);
            }

            // Generate end edges
            if (interval.end < o.pages && o.edges > 0) {
                if (o.pages - o.edges > interval.end && o.ellipseText) {
                    this.paginate_wrap.append('<li><span class="ellipse">' + o.ellipseText + '</span><li>');
                }
                var begin = Math.max(o.pages - o.edges, interval.end);
                for (i = begin; i < o.pages; i++) {
                    this._appendItem(i);
                }
            }

            // Generate Next link
            if (o.nextText) {
                this._appendItem(o.currentPage + 1, {
                    text: o.nextText,
                    classes: 'next'
                });
            }

            this.element.html(this.paginate_wrap);
        },

        _getInterval: function() {
            return {
                start: Math.ceil(this.opt.currentPage > this.opt.halfDisplayed ? Math.max(Math.min(this.opt.currentPage - this.opt.halfDisplayed, (this.opt.pages - this.opt.displayedPages)), 0) : 0),
                end: Math.ceil(this.opt.currentPage > this.opt.halfDisplayed ? Math.min(this.opt.currentPage + this.opt.halfDisplayed, this.opt.pages) : Math.min(this.opt.displayedPages, this.opt.pages))
            };
        },

        _appendItem: function(pageIndex, opts) {
            var options,
            $link,
            o = this.opt;

            pageIndex = pageIndex < 0 ? 0 : (pageIndex < o.pages ? pageIndex : o.pages - 1);

            options = $.extend({
                text: pageIndex + 1,
                classes: ''
            }, opts || {});

            if (pageIndex == o.currentPage || o.disabled) {
                $link = $('<li><span class="current">' + (options.text) + '</span></li>');
            } else {
                $link = $('<li><a href="' + o.hrefTextPrefix + (pageIndex + 1) + o.hrefTextSuffix + '" class="page-link">' + (options.text) + '</a></li>');
            }

            if (options.classes) {
                $link.addClass(options.classes);
            }

            if (this.opt.pages-1 == pageIndex)
                $link.addClass('last');

            this.paginate_wrap.append($link);

        },

        '.page-link click': function(el, ev){
            ev.preventDefault();
            this._selectPage($(el).attr('href')-1);

        },

        _selectPage: function(pageIndex) {
            this.opt.currentPage = pageIndex;

            if (this.opt.selectOnClick) {

//                var callBackString = this.opt.callBackString + pageIndex*this.opt.itemsOnPage +');';
//
//                eval(callBackString);

                this.opt.onPageClick(pageIndex*this.opt.itemsOnPage);
                this._draw();

            } else {

                this.opt.onPageClick(pageIndex*this.opt.itemsOnPage);

            }
        },

        destroy: function(){
            this.element.empty();
            this._super(); //Always call this!
        }
    });
