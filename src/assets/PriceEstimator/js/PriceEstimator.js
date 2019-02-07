;(function ($, window, document, undefined) {
    var pluginName = "priceEstimator",
        defaults = {
            totalCellSelector: '#totalCell',
            estimatePlan: false,
        };

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

        this.url = this.settings.url;
        this.form = form;
        this.rowSelector = this.settings.rowSelector;
        this.estimatesPerPeriod = {};
        this.totalSum = {};
        this.saleObjects = {};
        this.estimatePlan = this.settings.estimatePlan;
        this.totalPerObjectSelector = this.settings.totalPerObjectSelector;

        this.init();
    }

    Plugin.prototype = {
        init() {
        },
        update() {
            if (this.estimatePlan) {
                this.updatePlanPrices();
            } else {
                this.updatePriceCharges();
            }
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

            if (!objects) {
                return;
            }
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
                        this.addToTotalSum(period, estimate);
                    }
                });
            });
            this.estimatesPerPeriod[period] = estimatesPerRow;
        },
        addToTotalSum(period, estimate) {
            let currency = estimate.currency;
            let totalPerCurrencySum = this.totalSum[currency] || new Map([]);
            let totalPerPeriod = totalPerCurrencySum.get(period) || 0;

            totalPerPeriod += estimate.sum;
            totalPerCurrencySum.set(period, totalPerPeriod);
            this.totalSum[currency] = totalPerCurrencySum;
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
                        sumFormatted = '&mdash;',
                        estimate = estimatesPerRow[row.dataset.id];
                    if (estimate) {
                        sumFormatted = estimate.sumFormatted;
                    }
                    this.drawEstimatedValue(estimateBox, period, sumFormatted);
                    this.attachPopover(
                        estimateBox.find(`[title="${period}"]`),
                        estimate || {}
                    );
                });
            });
        },
        drawPlanTotal() {
            let totalCell = $(this.settings.totalCellSelector);

            this.formatTotalSum();
            totalCell.html('');
            Object.keys(this.totalSum).forEach(currency => {
                let totalSpan = $(document.createElement('span'));
                let objectCurrency = this.totalSum[currency];

                totalSpan.addClass('total-per-currency');
                for (let values of objectCurrency) {
                    let period = values[0],
                        sumFormatted = values[1].sumFormatted;

                    this.drawEstimatedValue(totalSpan, period, sumFormatted || '&mdash;');
                }
                totalCell.append(totalSpan);
            });
        },
        formatTotalSum() {
            Object.keys(this.totalSum).forEach(currency => {
                let formatter = new Intl.NumberFormat(hipanel.locale.get(), {
                    style: 'currency',
                    currency: currency,
                    currencyDisplay: 'symbol',
                    minimumFractionDigits: 2,
                });
                for (let values of this.totalSum[currency]) {
                    let period = values[0],
                        sum = values[1];
                    this.totalSum[currency].set(period, {
                        sum: sum,
                        sumFormatted: formatter.format(sum),
                    });
                }
            });

        },
        drawEstimatedValue(element, period, value) {
            if (element.html().length === 0) {
                element.append($('<strong>').attr({title: period}).html(value));
            } else {
                element.append('&nbsp; ');
                element.append($('<i>').attr({title: period}).html(value));
            }
        },
        computeTotalPerSaleObject() {
            let rows = this.getPriceRows();

            rows.each((k, row) => {
                let saleObjectId = this.getRelatedSaleObjectId(row);
                Object.keys(this.estimatesPerPeriod).forEach(period => {
                    let estimatesPerRow = this.estimatesPerPeriod[period],
                        estimate = estimatesPerRow[row.dataset.id];
                    if (estimate) {
                        this.updateSaleObjectSum(saleObjectId, period, estimate.currency, estimate.sum);
                    }
                });
            });
        },
        getRelatedSaleObjectId(row) {
            return $(row).closest('table').closest('tr').prev().attr('data-key');
        },
        updateSaleObjectSum(saleObjectId, period, currency, sum) {
            let saleObject = this.saleObjects[saleObjectId] || {};
            saleObject[currency] = saleObject[currency] || {};
            saleObject[currency][period] = saleObject[currency][period] || 0;
            saleObject[currency][period] += sum;

            this.saleObjects[saleObjectId] = saleObject;
        },
        drawTotalPerSaleObject() {
            this.computeTotalPerSaleObject();
            this.formatSaleObjectsTotal();
            Object.keys(this.saleObjects).forEach(saleObjectId => {
                let saleObject = this.saleObjects[saleObjectId];
                let saleObjectTotalCell = $(`tr[data-key=${saleObjectId}] ${this.totalPerObjectSelector}`);

                saleObjectTotalCell.html('');
                Object.keys(saleObject).forEach(currency => {
                    let saleObjectTotalSpan = $(document.createElement('span'));

                    saleObjectTotalSpan.addClass('total-per-currency');
                    let objectCurrency = saleObject[currency];

                    Object.keys(objectCurrency).forEach(period => {
                        this.drawEstimatedValue(
                            saleObjectTotalSpan,
                            period,
                            objectCurrency[period].sumFormatted || '&mdash;'
                        );
                    });
                    saleObjectTotalCell.append(saleObjectTotalSpan);
                    saleObjectTotalCell.addClass('estimated');
                });
            })
            this.clearUnestimatedCells();
        },
        formatSaleObjectsTotal() {
            Object.keys(this.saleObjects).forEach(saleObjectId => {
                let object = this.saleObjects[saleObjectId];
                Object.keys(object).forEach(currency => {
                    let objectCurrency = object[currency];
                    let formatter = new Intl.NumberFormat(hipanel.locale.get(), {
                        style: 'currency',
                        currency: currency,
                        currencyDisplay: 'symbol',
                        minimumFractionDigits: 2,
                    });
                    Object.keys(objectCurrency).forEach(period => {
                        this.saleObjects[saleObjectId][currency][period] = {
                            'sum': objectCurrency[period],
                            'sumFormatted': formatter.format(objectCurrency[period]),
                        };
                    });
                });
            });
        },
        clearUnestimatedCells() {
            let cells = $('.total-per-object:not(.estimated)');
            cells.html('&mdash;');
        },
        updatePriceCharges() {
            this.totalSum = {};
            let prices = this.getPrices();
            let actions = this.getActions(prices);

            this.getPriceRows().find('.price-estimates').html(hipanel.spinner.small);
            $(this.settings.totalCellSelector).html(hipanel.spinner.small);

            $.ajax({
                method: 'post',
                url: this.url,
                data: {prices, actions},
                success: json => {
                    Object.keys(json).forEach(period => {
                        this.rememberEstimates(period, json[period].targets);
                    });
                    this.drawEstimates();
                    this.drawPlanTotal();
                },
                error: xhr => {
                    this.showError(xhr.statusText);
                    this.getPriceRows().find('.price-estimates').html('');
                }
            })
        },
        updatePlanPrices() {
            $(this.settings.totalCellSelector).html(hipanel.spinner.small);
            $(this.settings.totalPerObjectSelector).html(hipanel.spinner.small);

            $.ajax({
                method: 'post',
                url: this.url,
                success: json => {
                    Object.keys(json).forEach(period => {
                        this.drawDynamicQuantity(json);
                        this.rememberEstimates(period, json[period].targets);
                    });
                    this.drawEstimates();
                    this.drawTotalPerSaleObject();
                    this.drawPlanTotal()
                },
                error: xhr => {
                    hipanel.notify.error(xhr.statusText);
                    $('.price-estimates').text('--');
                }
            });
        },
        drawDynamicQuantity(rows) {
            let firstPeriod = Object.keys(rows)[0];
            let period = rows[firstPeriod];

            if (period.targets) {
                Object.keys(period.targets).forEach(object_id => {
                    let objectActions = period.targets[object_id];

                    Object.keys(objectActions).forEach(type => {
                        let row = this.matchPriceRow(object_id, type);
                        if (row) {
                            let dynamicQuantity = row.parents('tr[data-key]').find('[data-dynamic-quantity]');
                            if (dynamicQuantity.length) {
                                dynamicQuantity.text(objectActions[type].quantity);
                            }
                        }
                    });
                });
            }
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
