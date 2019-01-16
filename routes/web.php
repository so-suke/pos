<?php

Route::get('/', function () {
  return view('welcome');
});

//ポスレジ
Route::get('/register', function () {
  return view('register.index');
})->name('register');

//アクセスすると販売データが作成されてしまうためコメントアウトしております。
Route::get('/register/make_sales_details', 'ActionController@makeSalesDetails');

Route::get('/menu', function () {
  return view('contents.menu');
})->name('menu');

//販売履歴参照
Route::get('/register/sales_history_reference', 'ActionController@toSalesHistoryReference')->name('sales_history_reference');
//販売詳細(販売履歴参照から遷移してくる)
Route::post('/register/sales_detail', 'ActionController@toSalesDetail')->name('sales_detail');


Route::get('/daily_tz_sales', 'ManagementController@toLargeCategories')->name('daily_tz_sales');
Route::get('/small_categories/{lc_id}', 'ManagementController@toSmallCategories')->name('small_categories');
Route::get('/small_categories/small_category/{sc_id}', 'ManagementController@toSmallCategory')->name('small_category');

// Ajaxリクエスト系
//会計処理
Route::post('ajax_q/account', 'ActionController@ajaxAccount');
Route::post('small_categories/ajax_small_categories_action/init', 'ManagementController@getSmallCategoriesInitData');
Route::post('small_categories/ajax_small_category_action/init', 'ManagementController@get_small_category_init_data');
Route::post('small_categories/ajax_small_category_action/get_timezone_data', 'ManagementController@get_small_category_timezone_data');
Route::post('small_categories/ajax_small_category_action/timezone_prev_day', 'ManagementController@tz_prev_day');
Route::post('small_categories/ajax_small_category_action/timezone_next_day', 'ManagementController@tz_next_day');
Route::post('small_categories/ajax_small_category_action/prev_week/{week_before_num}', 'ManagementController@prevWeek');