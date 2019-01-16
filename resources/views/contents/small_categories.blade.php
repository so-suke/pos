@extends('layouts.app')

@section('csses')
@parent
<link rel="stylesheet" href="{{ asset('/css/management.css') }}">
@endsection

@section('contents')
<div class="container-fluid pt-2">
  <div class="smallCategoryMain-grid border-bottom">
    <div class="d-flex justify-content-center align-items-center">
      <span class="smallCategoriesBunrui">分類</span>
    </div>
    <span></span>
    <div class="d-flex flex-column" v-for="date in dates">
      <span>@{{ date.num }}</span>
      <span>(@{{ date.day_of_week }})</span>
    </div>
  </div>
	{{-- 小カテゴリ名をキーとした販売情報配列を繰り返し表示 --}}
  <div class="smallCategoryMain-grid border-bottom" v-for="(saleses, id) in sc_id_saleses">
    <div class="d-flex justify-content-center align-items-center">
			<a :href="'/pos/public/small_categories/small_category/' + id" class="smallCategoriesMidasi btn btn-primary">@{{ saleses.sc_name }}</a>
    </div>
    <div class="d-flex flex-column">
      <template>
        <span>販売数</span>
        <span>販売金額</span>
      </template>
    </div>
    <div class="d-flex flex-column" v-for="(sales) in saleses.list">
      <template>
        <span>@{{ sales.num }}</span>
        <span>@{{ sales.money }}</span>
      </template>
    </div>
  </div>

</div>

@endsection

@section('scripts')
@parent
<script src="{{ asset('/js/contents/small_categories.js') }}"></script>
@endsection
