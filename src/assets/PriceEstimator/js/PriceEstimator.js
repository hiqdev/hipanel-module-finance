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
            this.getPriceCharges();
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
        displayEstimate(period, objects) {
            let estimatesPerRow = {};

            Object.keys(objects).forEach(object_id => {
                let objectActions = objects[object_id];

                Object.keys(objectActions).forEach(type => {
                    let row = this.matchPriceRow(object_id, type);

                    if (row) {
                        let charges = objectActions[type];
                        if (estimatesPerRow[row.data('id')] === undefined) {
                            estimatesPerRow[row.data('id')] = [];
                        }
                        estimatesPerRow[row.data('id')].push(...charges);
                    }
                });
            });

            this.estimatesPerPeriod[period] = estimatesPerRow;
        },
        drawEstimates() {
            let rows = this.getPriceRows();
            rows.find('.price-estimates').html('');

            Object.keys(this.estimatesPerPeriod).forEach(period => {
                let chargesPerRow = this.estimatesPerPeriod[period];

                Object.keys(chargesPerRow).forEach(rowId => {
                    let row = rows.filter('[data-id=' + rowId + ']'),
                        charges = chargesPerRow[rowId],
                        sum = 0.0;

                    charges.forEach(charge => {
                        sum += parseFloat(charge['price']);
                    })

                    let est = row.find('.price-estimates');
                    if (est.html().length === 0) {
                        est.append(`<strong title="${period}">${sum}</strong>`)
                    } else {
                        est.append(`, <i title="${period}">${sum}</i>`);
                    }
                });
            });
        },
        getPriceCharges() {
            let prices = this.getPrices();

            $.ajax({
                method: 'post',
                url: '/finance/plan/calculate-charges',
                data: {
                    prices,
                    actions: this.getActions(prices),
                },
                success: json => {
                    Object.keys(json).forEach(period => this.displayEstimate(period, json[period]))
                    this.drawEstimates();
                },
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
