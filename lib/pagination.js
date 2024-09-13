class Pagination {
    constructor(selector, pageSelector, pageNumberAttribute, pageQuery) {
        this.selector = selector;
        this.pageSelector = pageSelector;
        this.pageNumberAttribute = pageNumberAttribute;
        this.pageQuery = pageQuery;

        this.init = function () {
            let apathName = that.baseName(window.location.pathname);
            let params = {};
            let queryString = window.location.search;
            let urlParams = new URLSearchParams(queryString);
            let entries = urlParams.entries();
            for (const entry of entries) {
                params[entry[0]] = entry[1];
            }
            let selector = this.selector;
            let pageSelector = this.pageSelector;
            let pageNumberAttribute = this.pageNumberAttribute;
            let pageQuery = this.pageQuery;
            if ($(selector).length) {
                $(selector).each(function (e2) {
                    let pagination = $(this);
                    if (pagination.find(pageSelector).length) {
                        pagination.find(pageSelector).each(function (e3) {
                            let pageSelector = $(this);
                            pageSelector.find('a').attr('href', that.generateUrl(apathName, params, pageQuery, pageSelector.attr(pageNumberAttribute)));
                        });
                    }
                });
            }
        };

        this.baseName = function (str) {
            let li = Math.max(str.lastIndexOf('/'), str.lastIndexOf('\\'));
            return new String(str).substring(li + 1);
        };
        this.generateUrl = function (apathName, params, pageKey, pageValue) {
            params[pageKey] = pageValue;
            let parameters = [];
            for (let i in params) {
                parameters.push(i + '=' + encodeURIComponent(params[i]));
            }
            return apathName + '?' + parameters.join('&');
        };
        let that = this;
    }
}