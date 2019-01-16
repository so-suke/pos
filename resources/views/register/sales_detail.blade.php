@extends('layouts.app')

@section('csses')
@parent
<link type="text/css" rel="stylesheet" href="//unpkg.com/bootstrap-vue@latest/dist/bootstrap-vue.css" />
<link rel="stylesheet" href="{{ asset('/css/contents/sales_detail.css') }}">
@endsection

@section('contents')
<div class="container-fluid pt-2 d-flex flex-column align-items-center">
  <div class="header text-center">
    <span class="h2">販売詳細</span>
  </div>

  <div class="d-flex h4 mt-3">
    <div class="mr-3">
      <span class="h5">販売日時:</span>
      <span>{{ $saled_at_fmt }}</span>
      {{-- <span>2018年01月22日 13:05</span> --}}
    </div>
    <div class="">
      <span class="h5">スタッフ番号:</span>
      <span>{{ $sales_detail->staff_number }}</span>
    </div>
  </div>

  <div class="d-flex flex-column">
    <ul class="border-bottom border-primary pb-3">
      @foreach ($sales as $sale)
      <li class="d-flex flex-column">
        @if ($sale->num === 1)
        <div class="d-flex justify-content-between">
          <span class="salesDetailMidasi">{{ $sale->item_name }}</span>
          <span>￥{{ $sale->item_price }}</span>
        </div>
        @else
        <div class="d-flex flex-column">
          <span>{{ $sale->item_name }}</span>
          <div class="d-flex justify-content-between">
            <span class="salesDetailMidasi text-center">@ {{ $sale->item_price }}×{{ $sale->num }}</span>
            <span>￥{{ $sale->sub_total_price }}</span>
          </div>
        </div>
        @endif
        @if ($sale->discount_amt > 0)
        @if ($sale->num === 1)
        <div class="d-flex justify-content-between">
          <span class="salesDetailMidasi">値引額</span>
          <span class="">-{{ $sale->sub_total_dis_price }}</span>
        </div>
        @else
        <div class="d-flex flex-column">
          <span>値引額</span>
          <div class="d-flex justify-content-between">
            <span class="salesDetailMidasi text-center">@ {{ $sale->discount_amt }}×{{ $sale->num }}</span>
            <span class="text-right">-{{ $sale->sub_total_dis_price }}</span>
          </div>
        </div>
        @endif
        @endif
      </li>
      @endforeach
    </ul>

    <ul class="mt-3">
      <li class="d-flex justify-content-between">
        <span class="salesDetailMidasi d-block">商品代金:</span>
        <span>￥{{ $sales_detail->before_dis_total_price }}</span>
      </li>
      <li class="d-flex justify-content-between">
        <span class="salesDetailMidasi d-block">値引合計:</span>
        <span>-{{ $sales_detail->total_dis_price }}</span>
      </li>
      <li class="d-flex justify-content-between">
        <span class="salesDetailMidasi d-block">合計:</span>
        <span>￥{{ $sales_detail->total_price }}</span>
      </li>
      <li class="d-flex justify-content-between">
        <span class="salesDetailMidasi d-block">お預かり:</span>
        <span>￥{{ $sales_detail->deposit_price }}</span>
      </li>
      <li class="d-flex justify-content-between">
        <span class="salesDetailMidasi d-block">お釣り:</span>
        <span>￥{{ $sales_detail->change_price }}</span>
      </li>
    </ul>
  </div>

</div>
@endsection

@section('scripts')
@parent
<script src="{{ asset('/js/contents/sales_detail.js') }}"></script>
@endsection
