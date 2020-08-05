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
        this.popoverElements = [];
        this.rows = {};

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
                    let row = this.matchPriceRow(object_id, type);
                    let id = row.dataset.id;
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
            element.dataset.popoverGroup = 'price-estimate';
            element.dataset.content = estimate.detailsTable || '<p style="text-align: center">&mdash;</p>';
            element.dataset.placement = 'bottom';
            this.popoverElements.push(element);
        },
        drawEstimates() {
            let rows = this.getPriceRows();
            document.querySelectorAll('.price-estimates').forEach(elem => {
                elem.innerHTML = '';
            });

            rows.forEach(row => { // For each price row
                Object.keys(this.estimatesPerPeriod).forEach(period => { // Get all estimation periods
                    let estimatesPerRow = this.estimatesPerPeriod[period],
                        estimateBox = row.querySelector('.price-estimates'),
                        sumFormatted = '&mdash;',
                        estimate = estimatesPerRow[row.dataset.id];
                    if (estimate) {
                        sumFormatted = estimate.sumFormatted;
                    }
                    this.drawEstimatedValue(estimateBox, period, sumFormatted);
                    this.attachPopover(
                        estimateBox.querySelector(`[title="${period}"]`),
                        estimate || {}
                    );
                });
            });
        },
        drawPlanTotal() {
            let totalCell = document.querySelector(this.settings.totalCellSelector);
            totalCell.innerHTML = '';

            this.formatTotalSum();
            Object.keys(this.totalSum).forEach(currency => {
                let totalSpan = document.createElement('span');
                let objectCurrency = this.totalSum[currency];

                totalSpan.classList.add('total-per-currency');
                for (let values of objectCurrency) {
                    let period = values[0],
                        sumFormatted = values[1].sumFormatted;

                    this.drawEstimatedValue(totalSpan, period, sumFormatted || '&mdash;');
                }
                totalCell.appendChild(totalSpan);
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
            if (element.textContent.trim() === '') {
                const strong = document.createElement('strong');
                strong.title = period;
                strong.innerHTML = value;
                element.appendChild(strong);
            } else {
                const i = document.createElement('i');
                i.title = period;
                i.innerHTML = '&nbsp; ' + value;
                element.appendChild(i);
            }
        },
        computeTotalPerSaleObject() {
            let rows = this.getPriceRows();

            rows.forEach(row => {
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
            return row.closest('table').closest('tr').previousElementSibling.dataset.key;
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
                let saleObjectTotalCell = document.querySelector(`tr[data-key="${saleObjectId}"] ${this.totalPerObjectSelector}`);

                saleObjectTotalCell.innerHTML = '';
                Object.keys(saleObject).forEach(currency => {
                    let saleObjectTotalSpan = document.createElement('span');

                    saleObjectTotalSpan.classList.add('total-per-currency');
                    let objectCurrency = saleObject[currency];

                    Object.keys(objectCurrency).forEach(period => {
                        this.drawEstimatedValue(
                            saleObjectTotalSpan,
                            period,
                            objectCurrency[period].sumFormatted || '&mdash;'
                        );
                    });
                    saleObjectTotalCell.appendChild(saleObjectTotalSpan);
                    saleObjectTotalCell.classList.add('estimated');
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
            }).then(() => {
                this.activatePopovers();
            });
        },
        updatePlanPrices() {
            const spinner = this.getSpinner();
            document.querySelector(this.settings.totalCellSelector).appendChild(spinner.cloneNode(true));
            document.querySelectorAll(this.settings.totalPerObjectSelector).forEach(elem => {
                elem.appendChild(spinner.cloneNode(true));
            });

            $.ajax({
                method: 'post',
                url: this.url,
                success: json => {
                    const t0 = performance.now();

                    this.drawDynamicQuantity(json);
                    Object.keys(json).forEach(period => {
                        this.rememberEstimates(period, json[period].targets);
                    });
                    this.drawEstimates();
                    this.drawTotalPerSaleObject();
                    this.drawPlanTotal()

                    const t1 = performance.now();
                    // console.log("estimate took " + (t1 - t0) + " milliseconds.")

                },
                error: xhr => {
                    hipanel.notify.error(xhr.statusText);
                    $('.price-estimates').text('--');
                }
            }).then(() => {
                this.activatePopovers();
            });
        },
        activatePopovers() {
            if (this.popoverElements.length) {
                this.popoverElements.forEach(elem => {
                    elem.onclick = () => {
                        $(elem).popover({
                            html: true
                        }).on('show.bs.popover', e => {
                            $('.price-estimates *').not(e.target).popover('hide');
                        });
                        $(elem).popover('show');
                    };
                });
            }
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
                            let dynamicQuantity = row.closest('tr[data-key]').querySelector('[data-dynamic-quantity]');
                            if (dynamicQuantity && dynamicQuantity.textContent.trim() === '') {
                                dynamicQuantity.innerHTML = objectActions[type].quantity;
                            }
                        }
                    });
                });
            }
        },
        getPriceRows() {
            if (!this.rows.length) {
                this.rows = document.getElementById(this.form.attr('id')).querySelectorAll(`:scope ${this.rowSelector}`);
            }

            return this.rows;
        },
        getPrices() {
            const rows = this.getPriceRows();
            let result = {};
            rows.forEach(elem => {
                let row = $(elem),
                    id = row.data('id');

                result[id] = reformat($('<form>').add(row.find(':input').filter('[name]')).serializeArray());
            });

            return result;
        },
        matchPriceRow(object_id, type) {
            let rows = this.getPriceRows();

            let result;
            rows.forEach(elem => {
                const row_object_id = elem.querySelector("[name*=object_id]").value;
                const row_type = elem.querySelector("[name*=\"type\"]").value;

                if (object_id === row_object_id && type === row_type) {
                    result = elem;
                    return false;
                }
            });

            return result;
        },
        showError(message) {
            hipanel.notify.error(message);
        },
        getSpinner() {
            const spinner = document.createElement('span');
            spinner.classList.add('fa', 'fa-refresh', 'fa-spin', 'fa-fw');

            return spinner;
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
