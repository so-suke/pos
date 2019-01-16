@extends('layouts.app')

@section('csses')
@parent
@endsection

@section('contents')
<div class="container-fluid pt-2">
  <div class="text-center">
    <span class="h2">メニュー画面</span>
  </div>

	<div class="d-flex justify-content-center mt-3">
		<a href="{{ route('register') }}" class="btn btn-primary">ポスレジスター</a>
		<a href="{{ route('sales_history_reference') }}" class="btn btn-primary ml-3">販売履歴参照</a>
		<a href="{{ route('daily_tz_sales') }}" class="btn btn-primary ml-3">日別時間帯別販売情報</a>
	</div>
</div>

@endsection

@section('scripts')
@parent
@endsection
