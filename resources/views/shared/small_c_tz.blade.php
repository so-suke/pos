<div class="d-flex">
  @include('shared.kirikae')
  <span>対象期間</span>
  <span>@{{ start_fmt_at_all }}-@{{ end_fmt_at_all }}</span>
</div>

<div class="d-flex py-2">
  <button class="btn btn-sm btn-primary mydisabled">日付変更</button>
  <span>@{{ now_fmt_in_timezone }}</span>
  <div>
    <button class="btn btn-sm btn-primary" @click="prev_day">前の日</button>
    <button class="btn btn-sm btn-primary" @click="next_day">次の日</button>
  </div>
</div>

<div class="timezoneTable-grid border-bottom text-center">
  <span class="text-left">時間</span>
  <span v-for="n in 24">-@{{ n }}</span>
  <span>合計</span>
</div>

<div v-for="(sales_nums, name) in item_name_sales_nums">
  <span>@{{ name }}</span>
  <div class="timezoneTable-grid border-bottom text-center">
    <span class="text-left">販売数</span>
    <span v-for="sales_num in sales_nums">@{{ sales_num }}</span>
    <span>@{{ item_name_sum_sales_nums[name] }}</span>
  </div>
</div>
