<div class="d-flex">
  @include('shared.kirikae')
</div>
<div class="d-flex py-2">
  <div class="d-flex mr-2">
    <button class="btn btn-sm btn-primary" @click="toAnalysisSales">販売分析</button>
    <button class="btn btn-sm btn-primary disabled">廃棄分析</button>
    <button class="btn btn-sm btn-primary" @click="toAnalysisCompositionRatio">構成比表示</button>
  </div>
  <div class="d-flex mr-2">
    <button class="btn btn-sm btn-primary" v-for="num in [4,3,2]" @click="prevWeek(num)">
      @{{ num }}週間前
    </button>
    <button class="btn btn-sm btn-primary" @click="prevWeek(1)">直近週</button>
  </div>
  <span>@{{ start_fmt_at_all }}-@{{ end_fmt_at_all }}</span>
</div>
<div class="smallCategoryMain-grid border-bottom">
  <span>名前</span>
  <span>販売状況</span>
  <div class="d-flex flex-column" v-for="date in dates">
    <span>@{{ date.num }}</span>
    <span>(@{{ date.day_of_week }})</span>
  </div>
</div>
<div class="smallCategoryMain-grid border-bottom" v-for="(saleses, name) in item_name_saleses">
  <div class="d-flex align-items-center">
    <span class="h2">@{{ name }}</span>
  </div>
  <div class="d-flex flex-column">
    <template v-if="analysis_mode === ANALYSIS_MODE.SALES">
      <span>納品数</span>
      <span>販売数</span>
      <span>販売金額</span>
    </template>
    <template v-else-if="analysis_mode === ANALYSIS_MODE.COMPOSITION_RATIO">
      <span>販売数</span>
      <span>販売金額</span>
      <span>販売構成比</span>
    </template>
  </div>
  <div class="d-flex flex-column" v-for="(sales) in saleses">
    <template v-if="analysis_mode === ANALYSIS_MODE.SALES">
      <span>未設定</span>
      <span>@{{ sales.num }}</span>
      <span>@{{ sales.money }}</span>
    </template>
    <template v-else-if="analysis_mode === ANALYSIS_MODE.COMPOSITION_RATIO">
      <span>@{{ sales.num }}</span>
      <span>@{{ sales.money }}</span>
      <span>@{{ sales.composition_ratio }}</span>
    </template>
  </div>
</div>
