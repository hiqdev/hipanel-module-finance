const mountEl = document.querySelector("#generate-invoice-app");

Vue.createApp({
  props: {
    locale: {
      type: String,
      required: true,
    },
  },
  mounted() {
    $("#generate-invoice-form").on("beforeSubmit", this.prepareInvoice);
    $("#generate-invoice-form :input").on("change", () => {
      this.invoice = null;
    });
    $("#prepareinvoiceform-month").datetimepicker({
      maxDate: moment(),
      locale: this.locale,
      viewMode: "months",
      format: "YYYY-MM-01",
    });
  },
  computed: {
    isInvoicePrepared() {
      return this.invoice !== null;
    },
  },
  data() {
    return {
      invoice: null,
      isLoading: false,
      action: null,
    };
  },
  methods: {
    prepareInvoice(event) {
      event.preventDefault();
      if ($(event.target).find(".has-error").length === 0) {
        this.action = $(event.target).yiiActiveForm("data").options.action;
        this.makeRequest($(event.target).yiiActiveForm("data").options.action, $(event.target).serialize(), (data) => {
          this.invoice = data;
        });
      }

      return false;
    },
    generateInvoice(event) {
      event.preventDefault();
      if ($(event.target).find(".has-error").length === 0) {
        const form = document.createElement("form");
        const csrfParam = $("meta[name=\"csrf-param\"]").attr("content");
        const csrfToken = $("meta[name=\"csrf-token\"]").attr("content");
        const csrf = document.createElement("input");
        csrf.name = csrfParam;
        csrf.value = csrfToken;
        form.method = "POST";
        form.action = this.action;
        form.target = "_blank";
        for (const [name, value] of Object.entries(this.invoice)) {
          let elem = document.createElement("input");
          elem.name = `GenerateInvoiceForm[${name}]`;
          elem.value = name === "data" ? JSON.stringify(value) : value;
          form.appendChild(elem);
        }
        form.appendChild(csrf);
        document.body.appendChild(form);
        form.submit();
      }

      return false;
    },
    generateInvoiceAndRouteToDocument(event) {
      event.preventDefault();
      if ($(event.target).find(".has-error").length === 0) {
        this.invoice.save = true;
        this.makeRequest(this.action, {"GenerateInvoiceForm": this.invoice}, data => {
          window.open(data.link_to_document, "_blank");
        });
      }

      return false;
    },
    makeRequest(url, data, done, always) {
      $.ajax({
          url: url,
          method: "POST",
          timeout: 999999,
          dataType: "json",
          cache: false,
          data: data,
          beforeSend: () => {
            this.isLoading = true;
          },
          complete: () => {
            this.isLoading = false;
          },
        })
        .always(function () {
          if (typeof always === "function") {
            always();
          }
        })
        .done(function (response) {
          if (response.errorMessage) {
            hipanel.notify.error(response.errorMessage);
          } else {
            if (typeof done === "function") {
              done(response);
            }
          }
        })
        .fail(function (jqXHR, textStatus) {
          hipanel.notify.error(textStatus);
        });
    },
  },
}, {...mountEl.dataset}).mount(mountEl);
