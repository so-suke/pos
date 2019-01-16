@extends('layouts.app')

@section('csses')
@parent
@endsection

@section('contents')
<div class="container-fluid pt-2">
  <div class="text-center">
		<span class="h5 d-block">日別_時間帯別_販売情報</span>
    <span class="h2">大カテゴリ選択画面</span>
  </div>

  <div class="d-flex justify-content-center mt-3">
    @foreach ($large_categories as $large_category)
    <a href="{{ route('small_categories', ['lc_id' => $large_category->id]) }}" class="btn btn-primary mr-3">{{ $large_category->name }}</a>
    @endforeach
  </div>
</div>

@endsection

@section('scripts')
@parent
@endsection
