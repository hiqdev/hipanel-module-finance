const app = new Vue({
  el: "#consumption-view",
  template: `<div class="row">
    <div class="col-md-12">
        <div class="box box-widget">
            <div class="box-header with-border">
                <h3 class="box-title pull-right">{{boxTitle}}</h3>
                <div class="box-tools" style="right: inherit">
                    <small>
                        <ol class="breadcrumb" style="padding: 5px 15px 0; background-color: transparent;">
                            <li v-if="year" class="active"><a v-on:click="selectYear(null)" class="clickable"><i
                                    class="fa fa-home fa-fw"/></a></li>
                            <li v-if="year && month === null" class="active">{{showYear}}</li>
                            <li v-if="year && month">
                                <a v-on:click="selectYear(year)" class="clickable">{{showYear}}</a>
                            </li>
                            <li v-if="month" class="active">{{showMonth}}</li>
                        </ol>
                    </small>
                </div>
            </div>
            <div class="box-body no-padding">
                <table class="table table-striped table-condensed table-bordered">
                    <thead>
                    <tr>
                        <th></th>
                        <th v-for="(label, type) in columns" class="text-right">{{label}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(rows, date) in tableData">
                        <th scope="row" style="width: 10%;" class="text-center">
                            <a v-if="year === null" v-on:click="selectYear(date)" class="clickable">{{relativeData(date)}}</a>
                            <a v-if="year && month === null" v-on:click="selectMonth(date)" class="clickable">{{relativeData(date)}}</a>
                            <span v-if="year && month">{{relativeData(date)}}</span>
                        </th>
                        <td v-for="(label, type) in columns"
                            :class="{'text-right': true, 'bg-success': isMax(date, type), 'bg-danger': isMin(date, type)}"
                        >
                            {{findAmount(type, rows)}}
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <th v-for="(label, type) in columns" class="text-right">{{findTotal(type)}}</th>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div v-if="isLoading" class="overlay"></div>
        </div>
    </div>
    <div v-if="showCharts" v-for="(header, group) in charts" class="col-md-6" :style="{display: isChartVisible(group)}">
        <div class="box box-widget">
            <div class="box-header with-border">
                <h3 class="box-title">{{header}}</h3>
            </div>
            <div class="box-body">
                <div class="chart-responsive">
                    <canvas :id="group" :ref="group"></canvas>
                </div>
            </div>
            <div v-if="isLoading" class="overlay"></div>
        </div>
    </div>
</div>`,
  data: {
    boxTitle: "Resource consumption",
    cache: {},
    resources: [],
    totals: {},
    columns: {},
    groups: {},
    object_id: null,
    class: null,
    year: null,
    month: null,
    years: [],
    months: [],
    isLoading: false,
    existingCharts: {},
    getConsumptionUrl: null,
    showCharts: true,
  },
  created: function () {
    const data = window._INITIAL_DATA;
    if (data) {
      this.resources = data.resources;
      this.totals = data.totals;
      this.object_id = data.object_id;
      this.class = data.class;
      this.columns = data.columns;
      this.groups = data.groups;
      this.boxTitle = data.boxTitle;
      this.getConsumptionUrl = data.getConsumptionUrl;
      this.showCharts = data.showCharts;
      this.extractYears(this.resources);
      this.setCache({ resources: data.resources, totals: data.totals });
    }
  },
  mounted: function () {
    if (this.showCharts) {
      this.renderCharts();
    }
  },
  methods: {
    isChartVisible(group) {
      let show = false;
      _.forEach(group.split("."), type => {
        if (show) {
          return;
        }
        const exists = _.filter(this.resources, (resource) => resource.type === type);
        if (exists.length > 0) {
          show = true;
        }
      });

      return show ? "block" : "none";
    },
    showGroupHeader(group) {
      return Object.values(group).join(", ");
    },
    isMax(date, type) {
      const resources = _.filter(this.resources, (resource) => resource.type === type);
      if (resources.length === 0 || resources.length === 1) {
        return false;
      }
      const max = _.maxBy(resources, function (row) {
        return row.amount;
      });

      return date === max.date;
    },
    isMin(date, type) {
      const resources = _.filter(this.resources, (resource) => resource.type === type);
      if (resources.length === 0 || resources.length === 1) {
        return false;
      }
      const min = _.minBy(resources, function (row) {
        return row.amount;
      });

      return date === min.date;
    },
    relativeData(date) {
      if (this.isLoading) {
        return "";
      }
      if (this.year === null) {
        return this.moment(date).format("YYYY");
      }
      if (this.month === null) {
        return this.moment(date).format("YYYY-MM");
      }

      return this.moment(date).format("YYYY-MM-DD");
    },
    findAmount(type, rows) {
      const row = _.find(rows, (row) => row.type === type);

      return row ? `${row.amount} ${row.unit}` : "";
    },
    findTotal(type) {
      if (!this.totals) {
        return "";
      }
      const total = this.totals[type];

      return total ? `${total.amount} ${total.unit}` : "";
    },
    asYear(date) {
      return this.moment(date).format("YYYY");
    },
    asMonth(date) {
      return this.moment(date).format("MMM");
    },
    selectYear(date) {
      this.year = date ? moment(date).format("YYYY-MM-DD") : null;
      this.month = null;
    },
    selectMonth(date) {
      this.month = moment(date).format("YYYY-MM-DD");
    },
    extractYears(resources) {
      const years = [];
      _.forEach(resources, resource => {
        const year = this.moment(resource.date).format("YYYY");
        if (!years.includes(year)) {
          years.push(year);
        }
      });

      this.years = years;
    },
    extractMonths(resources) {
      const months = [];
      _.forEach(resources, resource => {
        const month = this.moment(resource.date).format("YYYY-MM");
        if (!months.includes(month)) {
          months.push(month);
        }
      });

      this.months = months;
    },
    moment(date) {
      return moment(date);
    },
    fetchResourceData: _.debounce(function () {
      const _this = this;
      const data = this.getCache();
      if (data) {
        this.resources = data.resources;
        this.totals = data.totals;

        return;
      }
      $.ajax({
        url: this.getConsumptionUrl,
        method: "GET",
        data: this.requestData,
        dataType: "json",
        beforeSend: function () {
          _this.isLoading = true;
        },
        complete: function () {
          _this.isLoading = false;
        },
        success: function (data) {
          _this.resources = data.resources;
          _this.totals = data.totals;
          _this.setCache(data);
        },
        error: function (error) {
          hipanel.notify.error(error.responseText);
        },
      });
    }, 50),
    setCache(data) {
      this.cache[this.cacheKey] = data;
    },
    getCache() {
      if (this.cache[this.cacheKey]) {
        return this.cache[this.cacheKey];
      }

      return false;
    },
    renderCharts() {
      const chartColors = {
        1: "rgb(240, 173, 78)",
        2: "rgb(40, 96, 144)",
      };
      _.forEach(this.groups, (group) => {
        const chartId = Object.keys(group).join(".");
        const labels = [];
        _.forEach(this.tableData, (rows, date) => {
          if (this.year === null) {
            labels.push(this.moment(date).format("YYYY"));
          }
          if (this.year && this.month === null) {
            labels.push(this.moment(date).format("YYYY-MM"));
          }
          if (this.year && this.month) {
            labels.push(this.moment(date).format("YYYY-MM-DD"));
          }
        });
        const chartData = {
          labels: labels,
          datasets: [],
        };
        let color = 1;
        let unit = "";
        _.forEach(group, (label, type) => {
          const resourceData = [];
          _.forEach(this.tableData, (rows, date) => {
            const row = _.find(rows, (row) => row.type === type);
            const amount = row ? row.amount : 0;
            unit = row && !unit ? row.unit : unit;
            resourceData.push(amount);
          });
          chartData.datasets.push({
            label: [label, unit].join(", "),
            data: resourceData,
            backgroundColor: Chart.helpers.color(chartColors[color]).alpha(0.5).rgbString(),
            borderColor: Chart.helpers.color(chartColors[color]).alpha(0.7).rgbString(),
          });
          color++;
        });
        if (this.existingCharts[chartId]) {
          this.existingCharts[chartId].data = chartData;
          this.existingCharts[chartId].update();
        } else if (this.showCharts) {
          const ctx = this.$refs[chartId];
          this.existingCharts[chartId] = new Chart(ctx, {
            type: "bar",
            data: chartData,
            options: {
              scales: {
                yAxes: [
                  {
                    ticks: {
                      beginAtZero: true,
                    },
                  },
                ],
              },
            },
          });
        }
      });
    },
  },
  watch: {
    resources(next, prev) {
      if (this.year && !this.month) {
        this.extractMonths(next);
      }
      if (prev !== {}) {
        this.renderCharts();
      }
    },
    year() {
      this.fetchResourceData();
    },
    month() {
      this.fetchResourceData();
    },
  },
  computed: {
    showYear() {
      return this.moment(this.year).format("YYYY");
    },
    showMonth() {
      return this.moment(this.month).format("YYYY-MM");
    },
    charts() {
      const charts = {};
      _.forEach(this.groups, (group) => {
        charts[Object.keys(group).join(".")] = Object.values(group).join(", ");
      });

      return charts;
    },
    tableData() {
      return _.groupBy(this.resources, (entry) => entry.date);
    },
    requestData() {
      const params = {
        object_id: this.object_id,
        class: this.class,
      };
      if (this.year === null) {
        params.groupby = "year";
      }
      if (this.year && this.month === null) {
        params.groupby = "month";
      }
      if (this.year && this.month) {
        params.groupby = "day";
      }
      if (this.year && this.month === null) {
        params.time_from = moment(this.year).startOf("year").format("YYYY-MM-DD");
        params.time_till = moment(this.year).endOf("year").format("YYYY-MM-DD");
      }
      if (this.year && this.month) {
        params.time_from = moment(this.month).startOf("month").format("YYYY-MM-DD");
        params.time_till = moment(this.month).endOf("month").format("YYYY-MM-DD");
      }

      return params;
    },
    cacheKey() {
      return Object.values(this.requestData).join(".");
    },
  },
});
