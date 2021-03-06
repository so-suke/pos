<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Document</title>
  @section('csses')
  <link rel="stylesheet" href="{{ asset('/css/app.css') }}">
  <link rel="stylesheet" href="{{ asset('/css/contents/myapp.css') }}">
  @show
</head>

<body>

	@php
	$slash_pos = strrpos(url()->current(), '/');
	@endphp
  <div id="app">
    <div class="nav-wrap">
      <ul class="nav">
        <li class="nav-item">
          <a class="nav-link {{ strpos(url()->current(), 'register', $slash_pos) !== false ? 'active':'' }}" href="{{ route('register') }}">ポスレジスタ</a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ strpos(url()->current(), 'sales_history_reference', $slash_pos) !== false ? 'active':'' }}" href="{{ route('sales_history_reference') }}">販売履歴参照</a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ strpos(url()->current(), 'daily_tz_sales', $slash_pos) !== false ? 'active':'' }}" href="{{ route('daily_tz_sales') }}">日別時間帯別販売情報</a>
        </li>
      </ul>
    </div>
    @yield('contents')
  </div>

  @section('scripts')
  <script src="https://cdn.jsdelivr.net/npm/vue@2.5.17/dist/vue.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.18.0/axios.min.js"></script>
  <script src="{{ asset('/js/lib/lodash.min.js') }}"></script>
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
  @show
</body>

</html>
