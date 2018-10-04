;(function ($, window, document, undefined) {
    var pluginName = "priceEstimator",
        defaults = {};

    function name2attribute(name) {
        let regex = /(\[([\w_]+)\])$/u;
        let matches = name.match(regex);

        if (matches === null) {
            return name;
        }

        return matches[2];
    }

    function reformat(array) {
        let result = {};
        for (let i in array) {
            let a = array[i];
            result[name2attribute(a.name)] = a.value;
        }
        return result;
    }

    function Plugin(form, options) {
        this.settings = $.extend({}, defaults, options);

        this.form = form;
        this.rowSelector = this.settings.rowSelector;
        this.estimatesPerPeriod = {};

        this.init();
    }

    Plugin.prototype = {
        init() {
        },
        update() {
            this.updatePriceCharges();
        },
        getActions(prices) {
            let result = {};
            for (let i in prices) {
                let price = prices[i],
                    key = price.type + '_' + price.object_id;

                result[key] = {
                    type: price.type,
                    object_id: price.object_id,
                    unit: price.unit,
                    amount: 1,
                };
            }

            return result;
        },
        rememberEstimates(period, objects) {
            let estimatesPerRow = {};

            Object.keys(objects).forEach(object_id => {
                let objectActions = objects[object_id];

                Object.keys(objectActions).forEach(type => {
                    let row = this.matchPriceRow(object_id, type),
                        id = row.data('id');

                    if (row) {
                        let estimate = objectActions[type];
                        if (estimatesPerRow[id] === undefined) {
                            estimatesPerRow[id] = [];
                        }
                        estimatesPerRow[id] = estimate;
                    }
                });
            });

            this.estimatesPerPeriod[period] = estimatesPerRow;
        },
        attachPopover: function (element, estimate) {
            element.data({
                'popover-group': 'price-estimate',
                'content': estimate.detailsTable || '<p style="text-align: center">&mdash;</p>',
                'placement': 'bottom'
            });
            element.popover({html: true}).on('show.bs.popover', e => {
                $('.price-estimates *').not(e.target).popover('hide');
            });
        },
        drawEstimates() {
            let rows = this.getPriceRows();
            rows.find('.price-estimates').html('');

            rows.each((k, row) => { // For each price row
                Object.keys(this.estimatesPerPeriod).forEach(period => { // Get all estimation periods
                    let estimatesPerRow = this.estimatesPerPeriod[period],
                        estimateBox = $(row).find('.price-estimates'),
                        sum = '&mdash;',
                        estimate = estimatesPerRow[row.dataset.id];

                    if (estimate) {
                        sum = estimate['sumFormatted'];
                    }

                    if (estimateBox.html().length === 0) {
                        estimateBox.append($('<strong>').attr({title: period}).html(sum));
                    } else {
                        estimateBox.append('; ');
                        estimateBox.append($('<i>').attr({title: period}).html(sum));
                    }

                    this.attachPopover(
                        estimateBox.find(`[title="${period}"]`),
                        estimate || {}
                    );
                });
            });
        },
        updatePriceCharges() {
            let prices = this.getPrices();
            let actions = this.getActions(prices);

            this.getPriceRows().find('.price-estimates').html('<i class="fa fa-spinner fa-spin fa-lg"></i>');

            $.ajax({
                method: 'post',
                url: '/finance/plan/calculate-charges',
                data: { prices, actions },
                success: json => {
                    Object.keys(json).forEach(period => this.rememberEstimates(period, json[period].targets));
                    this.drawEstimates();
                },
                error: xhr => {
                    this.showError(xhr.statusText);
                    this.getPriceRows().find('.price-estimates').html('');
                }
            })
        },
        getPriceRows() {
            return this.form.find(this.rowSelector);
        },
        getPrices() {
            let rows = this.getPriceRows();
            let result = {};
            rows.each(function () {
                let row = $(this),
                    id = row.data('id');

                result[id] = reformat($('<form>').add(row.find(':input').filter('[name]')).serializeArray());
            });

            return result;
        },
        matchPriceRow(object_id, type) {
            let rows = this.getPriceRows();

            let result;
            rows.each(function () {
                let row = $(this),
                    row_object_id = row.find(':input').filter('[name*=object_id]').val(),
                    row_type = row.find(':input').filter('[name*="type"]').val();

                if (object_id === row_object_id && type === row_type) {
                    result = row;
                    return false;
                }
            });

            return result;
        },
        showError(message) {
            hipanel.notify.error(message);
        },
    };

    $.fn[pluginName] = function (options) {
        let elem = $(this);
        if (elem.prop('tagName').toLowerCase() !== 'form') {
            elem = elem.closest('form');
        }

        if (!elem.data("plugin_" + pluginName)) {
            elem.data("plugin_" + pluginName, new Plugin(elem, options));
        }

        return elem.data("plugin_" + pluginName);
    };
})(jQuery, window, document);
