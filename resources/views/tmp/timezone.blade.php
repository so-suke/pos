@extends('layouts.app')

@section('contents')
<div class="container-fluid pt-2">
  <div class="d-flex">
    <span class="border title">分類</span>
    <span>onigiri</span>
  </div>
  <div class="d-flex">
    <span class="border title">切替</span>
    <div>
      <button class="btn btn-sm btn-primary">日別</button>
      <button class="btn btn-sm btn-primary">便別</button>
      <button class="btn btn-sm btn-primary">時間帯別</button>
    </div>
    <span>対象期間</span>
    <span>2018/11/11(月)-2018/11/11(月)</span>
  </div>
  <div class="d-flex py-2">
    <button class="btn btn-sm btn-primary">日付変更</button>
    <span>2018/11/11(月)</span>
    <div>
      <button class="btn btn-sm btn-primary">前の日</button>
      <button class="btn btn-sm btn-primary">次の日</button>
    </div>
  </div>

  <div class="grid-1 border-bottom text-center">
    <span class="text-left">時間</span>
    <span>-1</span>
    <span>-2</span>
    <span>-3</span>
    <span>-4</span>
    <span>-5</span>
    <span>-6</span>
    <span>-7</span>
    <span>-8</span>
    <span>-9</span>
    <span>-10</span>
    <span>-11</span>
    <span>-12</span>
    <span>-13</span>
    <span>-14</span>
    <span>-15</span>
    <span>-16</span>
    <span>-17</span>
    <span>-18</span>
    <span>-19</span>
    <span>-20</span>
    <span>-21</span>
    <span>-22</span>
    <span>-23</span>
    <span>-24</span>
    <span>合計</span>
  </div>

  <div>
    <span>item_a</span>
    <div class="grid-1 border-bottom text-center">
      <span class="text-left">販売数</span>
      <span>10</span>
      <span>10</span>
      <span>10</span>
      <span>10</span>
      <span>10</span>
      <span>10</span>
      <span>10</span>
      <span>10</span>
      <span>10</span>
      <span>10</span>
      <span>10</span>
      <span>10</span>
      <span>10</span>
      <span>10</span>
      <span>10</span>
      <span>10</span>
      <span>10</span>
      <span>10</span>
      <span>10</span>
      <span>10</span>
      <span>10</span>
      <span>10</span>
      <span>10</span>
      <span>10</span>
      <span>10</span>
    </div>
  </div>
</div>

@endsection
