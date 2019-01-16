@extends('layouts.app')

@section('csses')
@parent
<link type="text/css" rel="stylesheet" href="//unpkg.com/bootstrap-vue@latest/dist/bootstrap-vue.css" />
<link rel="stylesheet" href="{{ asset('/css/register.css') }}">
@endsection

@section('contents')
<div class="container-fluid pt-2" v-show="display_mode === DISPLAY_MODE.NORMAL">
  <div class="mb-3 head-options">
    <div class="input-group">
      <input type="number" class="form-control form-control-sm" placeholder="barcode" ref="barcode">
      <div class="input-group-append">
        <button class="btn btn-sm btn-outline-primary" @click="scan" ref="scan">スキャン</button>
      </div>
    </div>
    <div class="input-group">
      <input type="number" class="form-control form-control-sm" ref="multi_input">
      <div class="input-group-append">
        <button class="btn btn-sm btn-outline-primary" @click="multiplication">乗算</button>
      </div>
    </div>
    <button class="btn btn-sm btn-outline-danger" @click="to_cancel_display">商品取消</button>
    <div class="input-group">
      <input type="number" class="form-control form-control-sm" placeholder="預かり金額" ref="deposit_num">
      <button class="btn btn-sm btn-outline-success" @click="account">会計</button>
    </div>
  </div>


  <div class="text-center">
    <div class="grid-0 border-bottom">
      <span>行</span>
      <span>名前</span>
      <span>単価</span>
      <span>数量</span>
      <span>小計</span>
    </div>
    <div class="scrollable border-bottom">
      <template v-for="(item, idx) in normal.sales_items">
        <div class="grid-0 border-bottom" v-bind:class="{ 'text-danger': item.sales_num < 0 }">
          <span>@{{ idx + 1 }}</span>
          <span>@{{ item.name }}</span>
          <span>@{{ item.price }}</span>
          <span>@{{ item.sales_num }}</span>
          <span>@{{ item.sub_total }}</span>
        </div>
      </template>
    </div>
  </div>

  <div class="d-flex mt-2">
    <div class="w-50 border d-flex justify-content-center align-items-center">Advertisement Area</div>
    <div class="w-50 border p-2">
      <div class="input-group input-group-sm">
        <div class="input-group-prepend">
          <span class="input-group-text myinput-group-text">合計</span>
        </div>
        <input type="text" class="form-control" :value="get_total">
      </div>
      <div class="input-group input-group-sm">
        <div class="input-group-prepend">
          <span class="input-group-text myinput-group-text">お預かり</span>
        </div>
        <input type="text" class="form-control" ref="deposit_price">
      </div>
      <div class="input-group input-group-sm">
        <div class="input-group-prepend">
          <span class="input-group-text myinput-group-text">お釣り</span>
        </div>
        <input type="text" class="form-control" ref="change_price">
      </div>
    </div>
  </div>

  <div class="grid-1 text-center">
    <button class="btn btn-sm btn-primary" data-small_category_name="hotsnack" @click="to_simple">フランク</button>
    <button class="btn btn-sm btn-primary" data-small_category_name="chukaman" @click="to_simple">中華まん</button>
  </div>
</div>


<div class="container-fluid pt-2" v-show="display_mode === DISPLAY_MODE.SIMPLE">
  <div class="d-flex justify-content-end mb-3">
    <button class="btn btn-sm btn-primary" @click="simple_confirm">確定</button>
    <button class="btn btn-sm btn-primary ml-2" @click="simple_cancel">キャンセル</button>
  </div>
  <div class="grid-2 mb-3">
    <template v-if="simple.is_sales_updating !== true">
      <button class="btn btn-sm btn-primary d-flex flex-column" v-for="item in simple.sales_items" @click="simple_sales(item)">
        <span>名前: @{{ item.name }}</span>
        <span>単価: @{{ item.price }}</span>
        <span>数量: @{{ item.sales_num.tmp }}</span>
        <span>小計: @{{ item.getSubTotal() }}</span>
      </button>
    </template>

    <template v-else>
      <template v-for="item in simple.sales_items">
        <div v-if="item.hasSalesNumTMP() !== true" class="simpleDisplay-blankItem"></div>
        <button v-else class="btn btn-sm btn-primary d-flex flex-column" @click="simple_sales(item)">
          <span>名前: @{{ item.name }}</span>
          <span>単価: @{{ item.price }}</span>
          <span>数量: @{{ item.sales_num.tmp }}</span>
          <span>小計: @{{ item.getSubTotal() }}</span>
        </button>
      </template>
    </template>
  </div>
  <div class="d-flex">
    <button class="btn btn-sm btn-primary mr-2 disabled" @click="prepare_simple_multiplication" ref="simple_multiplication_btn">乗算</button>
    <button class="btn btn-sm btn-primary mr-2 disabled" @click="prepare_simple_update" ref="simple_update_btn">変更</button>
    <div class="input-group">
      <div class="input-group-prepend">
        <span class="input-group-text" id="total">合計</span>
      </div>
      <input type="text" class="form-control no-touch" aria-describedby="total" :value=get_total_simple>
    </div>
  </div>

  <b-modal ref="numInputModal" hide-footer title="数値入力" @hide="num_input_modal_hide">
    <div class="d-block mb-2">
      <input type="number" name="" id="" class="form-control" ref="simple_action_num_input">
    </div>
    <b-btn class="mr-2" variant="primary" @click="num_input_modal_ok">OK</b-btn>
    <b-btn class="" variant="primary" @click="num_input_modal_cancel">Cancel</b-btn>
  </b-modal>

</div>



{{-- 商品取消画面 --}}
<div class="container-fluid pt-2" v-show="display_mode === DISPLAY_MODE.CANCEL">
  <div class="text-center">
    <span class="h2">商品取消画面</span>
  </div>
  <div class="d-flex justify-content-center mb-3">
    <button class="btn btn-primary mydisabled" @click="sales_cancel_ok" ref="sales_cancel_ok">確定</button>
    <button class="btn btn-primary ml-2" @click="sales_cancel_cancel">キャンセル</button>
  </div>
  <div class="text-center">
    <div class="grid-cancel-header border-bottom">
      <span></span>
      <span>行</span>
      <span>名前</span>
      <span>単価</span>
      <span>数量</span>
      <span>小計</span>
    </div>
    <div class="scrollable border-bottom cancelDisplay-itemsWrap">
      <div class="grid-cancel-0 border-bottom" v-bind:class="{ 'text-danger': item.sales_num < 0 }" v-for="(item, idx) in normal.sales_items" ref="cancel_rows">
        {{-- 商品の販売数が0より上、商品が「値下げ」でない、取消されていない --}}
        <template v-if="item.sales_num > 0 && !_isDiscount({item}) && item.is_canceled === false">
          <div class="form-check">
            <input class="form-check-input position-static" type="radio" name="cancel_radio" v-bind:data-idx_in_sales_items="idx" v-bind:id="idx" @click="choose_cancel_item">
          </div>
          <label class="grid-cancel-1 mb-0" v-bind:for="idx">
            <span>@{{ idx + 1 }}</span>
            <span>@{{ item.name }}</span>
            <span>@{{ item.price }}</span>
            <span>@{{ item.sales_num }}</span>
            <span>@{{ item.sub_total }}</span>
          </label>
        </template>

        <template v-else>
          <div></div>
          <div class="grid-cancel-1 isCanceled" v-bind:for="idx">
            <span>@{{ idx + 1 }}</span>
            <span>@{{ item.name }}</span>
            <span>@{{ item.price }}</span>
            <span>@{{ item.sales_num }}</span>
            <span>@{{ item.sub_total }}</span>
          </div>
        </template>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
@parent
<script src="//unpkg.com/babel-polyfill@latest/dist/polyfill.min.js"></script>
<script src="//unpkg.com/bootstrap-vue@latest/dist/bootstrap-vue.js"></script>
<script src="{{ asset('/js/masters/all_items.js') }}"></script>
<script src="{{ asset('/js/masters/simple_items.js') }}"></script>
<script src="{{ asset('/js/define_class.js') }}"></script>
<script src="{{ asset('/js/simple_sales.js') }}"></script>
<script src="{{ asset('/js/register.js') }}"></script>
@endsection
