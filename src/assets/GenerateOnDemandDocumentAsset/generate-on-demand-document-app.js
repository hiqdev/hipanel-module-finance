const mountEl = document.querySelector("#generate-on-demand-document-app");

Vue.createApp({
  props: {
    locale:     { type: String, required: true },
    saveUrl:    { type: String, required: true },
    previewUrl: { type: String, required: true },
    prepareUrl: { type: String, required: true },
    chargeIds:  { type: String, required: true },
  },
  mounted() {
    // Clear prepare results when type or date changes
    document.querySelectorAll(
      '#generate-on-demand-document-form select, #generate-on-demand-document-form input[type="text"]'
    ).forEach((el) => {
      el.addEventListener("change", () => {
        this.groupsWithState = [];
        this.wasPrepared = false;
        this._highlightIneligible([]);
      });
    });
  },
  computed: {
    isPrepared() {
      return this.groupsWithState.length > 0;
    },
    parsedChargeIds() {
      try {
        return JSON.parse(this.chargeIds);
      } catch {
        return [];
      }
    },
  },
  data() {
    return {
      groupsWithState: [],
      wasPrepared: false,
      isLoading:   false,
      isSavingAll: false,
      allSaved:    false,
      type: null,
      date: null,
    };
  },
  methods: {
    _highlightIneligible(ineligibleIds) {
      const ineligibleSet = new Set(ineligibleIds);

      document.querySelectorAll("tr[data-charge-id]").forEach((row) => {
        const id = Number(row.dataset.chargeId);
        const isIneligible = ineligibleSet.has(id);
        row.querySelectorAll("td").forEach((td) => {
          td.style.backgroundColor = isIneligible ? "#ffaa44" : "";
        });
      });

      document.querySelectorAll(".box.box-widget").forEach((box) => {
        const rows = [...box.querySelectorAll("tr[data-charge-id]")];
        if (rows.length === 0) return;
        const allIneligible = rows.every((row) => ineligibleSet.has(Number(row.dataset.chargeId)));
        const header = box.querySelector(".box-header");
        if (header) {
          header.style.backgroundColor = allIneligible ? "#ffaa44" : "#d2e8f5";
        }
      });
    },

    prepareDocument() {
      this.type = document.querySelector('[name="PrepareOnDemandDocumentForm[type]"]')?.value || null;
      this.date = document.querySelector('[name="PrepareOnDemandDocumentForm[date]"]')?.value || null;

      if (!this.type) {
        hipanel.notify.warning("Please select a document type.");
        return;
      }
      if (this.parsedChargeIds.length === 0) {
        hipanel.notify.warning("No charges available.");
        return;
      }

      const postData = new URLSearchParams();
      this.parsedChargeIds.forEach((id) => postData.append("charge_ids[]", id));
      postData.append("type", this.type);
      if (this.date) {
        postData.append("date", this.date);
      }

      this.groupsWithState = [];
      this.wasPrepared = false;
      this._highlightIneligible([]);
      this.allSaved = false;
      this.makeRequest(this.prepareUrl, postData.toString(), (data) => {
        const entries = Object.entries(data);

        entries
          .filter(([, g]) => g._error)
          .forEach(([purseId, g]) => hipanel.notify.error(`Purse ${purseId}: ${g._error}`));

        this.groupsWithState = entries
          .filter(([, g]) => !g._error)
          .map(([key, group]) => ({
            groupKey:    key,
            purseId:     group.purse.id,
            location:    group.location || null,
            purse:       group.purse,
            charges:     group.charges,
            previewing:  false,
            documentUrl: null,
          }));

        const eligibleIds = new Set();
        entries.filter(([, g]) => !g._error).forEach(([, group]) => {
          (group.charges || []).forEach((c) => eligibleIds.add(Number(c.id)));
        });
        const ineligibleIds = this.parsedChargeIds.filter((id) => !eligibleIds.has(id));
        this._highlightIneligible(ineligibleIds);
        this.wasPrepared = true;
      });
    },

    previewGroup(group) {
      const postData = new URLSearchParams();
      postData.append("purse_id", group.purseId);
      postData.append("type", this.type);
      if (this.date) {
        postData.append("date", this.date);
      }
      if (group.location) {
        postData.append("location", group.location);
      }
      group.charges.forEach((c) => postData.append("charge_ids[]", c.id));

      group.previewing = true;
      this.makeRequest(this.previewUrl, postData.toString(), (response) => {
        group.previewing = false;
        if (response.preview_url) {
          window.open(response.preview_url, "_blank");
        }
      }, () => {
        group.previewing = false;
      });
    },

    async saveAll() {
      this.isSavingAll = true;
      for (const group of this.groupsWithState) {
        await this.saveGroup(group);
      }
      this.isSavingAll = false;
      this.allSaved = true;
    },

    saveGroup(group) {
      const postData = new URLSearchParams();
      postData.append("purse_id", group.purseId);
      postData.append("type", this.type);
      if (this.date) {
        postData.append("date", this.date);
      }
      if (group.location) {
        postData.append("location", group.location);
      }
      group.charges.forEach((c) => postData.append("charge_ids[]", c.id));

      return new Promise((resolve) => {
        this.makeRequest(this.saveUrl, postData.toString(), (response) => {
          if (response.link_to_document) {
            group.documentUrl = response.link_to_document;
          }
          resolve();
        }, () => {
          resolve();
        });
      });
    },

    stripTags(value) {
      return value ? String(value).replace(/<[^>]*>/g, "") : "";
    },

    formatSum(sum, currency) {
      return parseFloat(sum).toFixed(2) + " " + currency;
    },

    makeRequest(url, data, done, fail) {
      this.isLoading = true;
      $.ajax({
        url:      url,
        method:   "POST",
        timeout:  999999,
        dataType: "json",
        cache:    false,
        data:     data,
      })
        .done((response) => {
          this.isLoading = false;
          const errMsg = response?.errorMessage || response?._error;
          if (errMsg) {
            hipanel.notify.error(errMsg);
            if (typeof fail === "function") fail();
          } else {
            if (typeof done === "function") done(response);
          }
        })
        .fail((jqXHR, textStatus) => {
          this.isLoading = false;
          hipanel.notify.error(textStatus);
          if (typeof fail === "function") fail();
        });
    },
  },
}, { ...mountEl.dataset }).mount(mountEl);
