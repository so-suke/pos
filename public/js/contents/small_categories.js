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
    small_categories: [],
    dates: [],
    sc_id_saleses: [],
  },
  created() {
    this._initSmallCategory()
  },
  methods: {
    _initSmallCategory: function() {
      axios.post('/pos/public/ajax_small_categories_action/init')
        .then(response => {
          this.small_categories = response.data.small_categories;
          this.dates = response.data.dates;
          this.sc_id_saleses = response.data.sc_id_saleses;
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
  }
});