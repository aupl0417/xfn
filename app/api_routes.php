<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Route;

Route::get('/',function(){
    return 'Hello,world!';
});

/*
 * 接口路由文件，接口专用
 * */

//V1版本接口路由
//屏幕活动
Route::get('activity_v1/index','api/v1.Screen.Activity/read');

//汽车活动及客户意向
Route::get('car_v1/index','api/v1.Screen.Car/read');
Route::get('car_v1/info','api/v1.Screen.Car/info');
Route::get('car_v1/phone','api/v1.Screen.Car/phone');
Route::get('menu_v1/brand','api/v1.Menu/brand');
Route::get('menu_v1/series','api/v1.Menu/series');
Route::get('menu_v1/search','api/v1.Menu/search');
Route::post('user_v1/create','api/v1.Seller.User/create');
Route::post('carinfo_v1/collection','api/v1.Seller.Car/collection');
Route::post('carinfo_v1/order','api/v1.Seller.Car/order');
Route::post('carinfo_v1/info','api/v1.Seller.Car/carInfo');
Route::get('seller_v1/info','api/v1.Seller.Index/info');
Route::get('seller_v1/lists','api/v1.Seller.Index/lists');
Route::get('seller_v1/business','api/v1.Seller.Index/business');
Route::get('seller_v1/message','api/v1.Seller.Index/message');
