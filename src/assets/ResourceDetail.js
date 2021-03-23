const ResourceDetail = {
  template: `
    <div class="row">
        <div class="col-md-3">
            <div v-if="types.length > 0" class="box box-widget">
                <div class="box-header">
                    <div class="box-title">Resource types</div>
                </div>
                <div class="box-body no-padding">
                    <ul class="nav nav-stacked">
                        <li v-for="type in types" :class="classType(type)">
                            <a v-on:click="selectType(type)" class="clickable">{{asTypeLabel(type)}}</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div v-if="type" class="col-md-4">
            <div class="box box-widget">
              <div class="box-header">
                <h3 class="box-title">{{showTotal}} {{showUnit}}</h3>
                <div class="box-tools pull-right">
                  <small>
                    <ol class="breadcrumb" style="padding: 5px 15px 0; background-color: transparent;">
                      <li v-if="type && year"><a v-on:click="selectType(type)" class="clickable">{{asTypeLabel(type)}}</a></li>
                      <li v-if="type && year === null" class="active">{{asTypeLabel(type)}}</li>
                      <li v-if="year && month === null" class="active">{{showYear}}</li>
                      <li v-if="year && month"><a v-on:click="selectYear(year)" class="clickable">{{showYear}}</a></li>
                      <li v-if="month" class="active">{{showMonth}}</li>
                    </ol>
                  </small>
                </div>
              </div>
              <div class="box-body no-padding">
                <table class="table table-striped">
                  <tbody>
                    <tr>
                      <th>Date</th>
                      <th class="text-right">Total</th>
                      <th>Unit</th>
                    </tr>
                    <tr v-if="resources.length > 0" v-for="resource in resources">
                      <td>
                        <a v-if="type && year === null" v-on:click="selectYear(resource.date)" class="clickable">{{moment(resource.date).format("YYYY")}}</a>
                        <a v-if="year && month === null" v-on:click="selectMonth(resource.date)" class="clickable">{{moment(resource.date).format("YYYY-MM")}}</a>
                        <span v-if="year && month">{{resource.date}}</span>
                      </td>
                      <td class="text-right">{{resource.qty || '--'}}</td>
                      <td>{{resource.unit}}</td>
                    </tr>
                    <tr>
                      <th colspan="1"></th>
                      <th class="text-right">{{showTotal}}</th>
                      <th>{{showUnit}}</th>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div v-if="isLoading" class="overlay"></div>
            </div>
        </div>
        <div v-if="type" class="col-md-5">
            <div class="box box-widget">
                <div class="box-body">
                  <div class="chart-responsive">
                    <canvas ref="chart"></canvas>
                  </div>
                </div>
              <div v-if="isLoading" class="overlay"></div>
            </div>
        </div>
    </div>
  `,
  data() {
    return {
      chart: null,
      initial: [],
      resources: [],
      cached: {},
      types: [],
      unit: null,
      type: null,
      year: null,
      month: null,
      isLoading: false,
      object_id: null,
    };
  },
  created: function () {
    const types = [];
    this.initial = window._init_resources;
    this.object_id = window._init_resources_id;
    _.forEach(this.initial.resources[this.object_id], (resources, type) => {
      types.push(type);
    });
    this.types = [...new Set(types)];
  },
  watch: {
    resources(newRes, oldRes) {
      const labels = [];
      const data = [];
      _.forEach(newRes, resource => {
        if (this.year === null && this.month === null) {
          labels.push(this.moment(resource.date).format('YYYY'));
        } else {
          labels.push(resource.date);
        }
        data.push(parseFloat(resource.qty.replace(/,/g, '')) || 0);
      });
      this.renderChart({
        labels: labels,
        datasets: [
          {
            label: `${this.asTypeLabel(this.type)} (${this.showUnit})`,
            data: data,
            backgroundColor: "#FFE0E6",
            borderColor: "#FE6384",
          },
        ],
      });
    },
  },
  computed: {
    requestData() {
      const params = {
        object_ids: this.object_id,
        type: "overuse," + this.type,
      };
      if (this.year === null) {
        params.groupby = "server_traf_year";
      }
      if (this.year && this.month === null) {
        params.groupby = "server_traf_month";
      }
      if (this.year && this.month) {
        params.groupby = "server_traf_day";
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
    years() {
      const years = [];
      _.forEach(_.filter(this.initial, resource => resource.type === this.type), resource => {
        years.push(resource.date);
      });

      return years;
    },
    showYear() {
      return this.moment(this.year).format("YYYY");
    },
    showMonth() {
      return this.moment(this.month).format("YYYY-MM");
    },
    total() {
      return this.cached[this.totalCacheKey];
    },
    showTotal() {
      if (this.total) {
        return this.total[this.type].qty;
      }

      return "";
    },
    showUnit() {
      if (this.total) {
        return this.total[this.type].unit;
      }

      return "";
    },
    resourceCacheKey() {
      return ["R", this.type, this.year, this.month].join("");
    },
    totalCacheKey() {
      return ["T", this.type, this.year, this.month].join("");
    },
  },
  methods: {
    renderChart(data) {
      const ctx = this.$refs.chart;
      if (this.chart) {
        this.chart.data = data;
        this.chart.update();
      } else {
        this.chart = new Chart(ctx, {
          type: "bar",
          data: data,
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
    },
    asTypeLabel(type) {
      return this.initial.resources[this.object_id][type][0].type_label;
    },
    moment(date) {
      return moment(date);
    },
    classType(type) {
      return {active: this.type === type};
    },
    classYear(year) {
      return {active: this.year === year};
    },
    selectType(type) {
      this.type = type;
      this.year = null;
      this.month = null;
      this.setCache(this.resourceCacheKey, _.sortBy(this.initial.resources[this.object_id][type], r => r.date));
      this.setCache(this.totalCacheKey, this.initial.totals);
      this.fetchResourceData();
    },
    selectYear(date) {
      this.year = date ? moment(date).format("YYYY-MM-DD") : null;
      this.month = null;
      this.fetchResourceData();
    },
    selectMonth(date) {
      this.month = moment(date).format("YYYY-MM-DD");
      this.fetchResourceData();
    },
    fetchResourceData: _.debounce(function () {
      const _this = this;
      const resources = this.getCache(this.resourceCacheKey);
      if (resources) {
        this.resources = resources;
        return;
      }
      $.ajax({
        url: "fetch-resources",
        method: "GET",
        data: this.requestData,
        dataType: "json",
        headers: {
          "X-Expected-Ajax-Response": "vue-details",
        },
        beforeSend: function () {
          _this.isLoading = true;
        },
        complete: function () {
          _this.isLoading = false;
        },
        success: function (data) {
          if (data.resources[_this.object_id]) {
            _this.resources = _.sortBy(data.resources[_this.object_id][_this.type], r => r.date);
            _this.setCache(_this.resourceCacheKey, _this.resources);
            _this.setCache(_this.totalCacheKey, data.totals);
          } else {
            _this.resources = [];
          }
        },
        error: function (error) {
          console.log("Error! Could not reach the API. ", error);
        },
      });
    }, 50),
    setCache(key, data) {
      if (key) {
        this.cached[key] = data || [];
      }
    },
    getCache(key) {
      if (this.cached[key]) {
        return this.cached[key];
      }

      return false;
    },
  },
};

const app = Vue.createApp(ResourceDetail).mount("#resource-detail");
