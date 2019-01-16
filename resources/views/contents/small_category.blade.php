@extends('layouts.app')

@section('csses')
@parent
<link rel="stylesheet" href="{{ asset('/css/management.css') }}">
@endsection

@section('contents')
<div class="container-fluid pt-2">
  <div class="d-flex">
    <span class="border title">分類</span>
    <span>@{{ small_category === '' ? '' : small_category.name }}</span>
  </div>

  <template v-if="display_mode === DISPLAY_MODE.DAY">
    @include('shared.small_c_date')
  </template>
	
  <template v-else-if="display_mode === DISPLAY_MODE.TIMEZONE">
    @include('shared.small_c_tz')
  </template>

</div>

@endsection

@section('scripts')
@parent
<script src="{{ asset('/js/management.js') }}"></script>
@endsection