@extends('layouts.app')

@section('csses')
@parent
<link type="text/css" rel="stylesheet" href="//unpkg.com/bootstrap-vue@latest/dist/bootstrap-vue.css" />
<link rel="stylesheet" href="{{ asset('/css/contents/sales_history_reference.css') }}">
@endsection

@section('contents')
<div class="container-fluid pt-2">
  <div class="header text-center">
    <span class="h3">販売履歴参照</span>
  </div>

  <form class="d-none" action="{{ route('sales_detail') }}" method="POST" ref="toSalesDetailForm">
    @csrf
    <input type="hidden" name="sales_detail_id" value="" ref="salesDetailID">
  </form>

  <table class="table table-hover">
    <thead>
      <tr>
        <th scope="col">#</th>
        <th scope="col">販売日時</th>
        <th scope="col">スタッフ番号</th>
        <th scope="col">合計金額</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($sales_details as $key => $sales_detail)
      <tr class="clickable" @click="toSalesDetail" data-sales_detail_id="{{ $sales_detail->id }}">
        <th scope="row">{{ $key }}</th>
        <td>{{ $sales_detail->saled_at_fmt }}</td>
        <td>{{ $sales_detail->staff_number }}</td>
        <td>￥{{ $sales_detail->total_price }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection

@section('scripts')
@parent
<script src="{{ asset('/js/contents/sales_history_reference.js') }}"></script>
@endsection
