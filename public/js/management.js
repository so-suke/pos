const DISPLAY_MODE = {
  DAY: 'day',
  TIMEZONE: 'timezone',
}
const ANALYSIS_MODE = {
  SALES: 'sales',
  COMPOSITION_RATIO: 'composition_ratio',
}

var app = new Vue({
  el: '#app',
  data: {
    DISPLAY_MODE,
		ANALYSIS_MODE,
    display_mode: DISPLAY_MODE.DAY,
		analysis_mode: ANALYSIS_MODE.SALES,
    small_category: '',
    start_fmt_at_all: '',
    end_fmt_at_all: '',
    now_fmt_in_timezone: '',
    dates: [],
    item_name_saleses: [],
    item_name_sales_nums: [],
    item_name_sum_sales_nums: [],
  },
  created() {
    this._init_small_category()
  },
  methods: {
    _init_small_category: function() {
      axios.post('/pos/public/small_categories/ajax_small_category_action/init')
        .then(response => {
          this.small_category = response.data.small_category;
          this.start_fmt_at_all = response.data.start_fmt_at_all
          this.end_fmt_at_all = response.data.end_fmt_at_all
          this.dates = response.data.dates
          this.item_name_saleses = response.data.item_name_saleses
        })
        .catch(e => {
          console.log(e)
        })
    },
		toAnalysisSales: function() {
			this.analysis_mode = ANALYSIS_MODE.SALES;
		},
		toAnalysisCompositionRatio: function() {
			this.analysis_mode = ANALYSIS_MODE.COMPOSITION_RATIO;
		},
    prevWeek: function(num) {
      axios.post('/pos/public/small_categories/ajax_small_category_action/prev_week/' + num)
        .then(response => {
          this.dates = response.data.dates
          this.item_name_saleses = response.data.item_name_saleses
        })
        .catch(e => {
          this.errors.push(e)
        })
    },
    toTimezoneDivide: function() {
      axios.post('/pos/public/small_categories/ajax_small_category_action/get_timezone_data')
        .then(response => {
          this.item_name_sales_nums = response.data.item_name_sales_nums
          this.item_name_sum_sales_nums = response.data.item_name_sum_sales_nums
          this.now_fmt_in_timezone = response.data.now_fmt
          this.display_mode = DISPLAY_MODE.TIMEZONE
        })
        .catch(e => {
          console.log(e)
        })
    },
    toDayDivide: function() {
      if(this.display_mode === DISPLAY_MODE.DAY) return;
      this._init_small_category()
      this.display_mode = DISPLAY_MODE.DAY
    },
    prev_day: function() {
      const params = new URLSearchParams();
      params.append('now_fmt_in_tz', this.now_fmt_in_timezone);
      axios.post('/pos/public/small_categories/ajax_small_category_action/timezone_prev_day', params)
        .then(response => {
          this.item_name_sales_nums = response.data.item_name_sales_nums
          this.item_name_sum_sales_nums = response.data.item_name_sum_sales_nums
          this.now_fmt_in_timezone = response.data.now_fmt
        })
        .catch(e => {
          console.log(e)
        })
    },
    next_day: function() {
      const params = new URLSearchParams();
      params.append('now_fmt_in_tz', this.now_fmt_in_timezone);
      axios.post('/pos/public/small_categories/ajax_small_category_action/timezone_next_day', params)
        .then(response => {
          this.item_name_sales_nums = response.data.item_name_sales_nums
          this.item_name_sum_sales_nums = response.data.item_name_sum_sales_nums
          this.now_fmt_in_timezone = response.data.now_fmt
        })
        .catch(e => {
          console.log(e)
        })
    },
  }
})
