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

//Route::get('/',function(){
//    return 'Hello,world!';
//});
Route::domain('work.' . config('url_domain_root'), 'admin');
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
Route::get('car_v1/saoma','api/v1.Screen.Car/saoma');
Route::get('car_v1/callback','api/v1.Screen.Car/callback');

//销售员/分销员/店长
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
Route::get('seller_v1/orderlist','api/v1.Seller.Index/orderList');
Route::get('seller_v1/detail','api/v1.Seller.Index/detail');

//后台接口

//登录
Route::post('work_v1/login','api/v1.Login.Index/index');

//销售员管理
Route::get('work_v1/seller','api/v1.SellerManage.Index/index');
Route::post('work_v1/seller/add','api/v1.SellerManage.Index/add');
Route::get('work_v1/seller/remove','api/v1.SellerManage.Index/remove');

//活动管理
Route::get('work_v1/activity','api/v1.ActivityManage.Index/index');
Route::post('work_v1/activity/add','api/v1.ActivityManage.Index/add');
Route::post('work_v1/activity/edit','api/v1.ActivityManage.Index/edit');
Route::get('work_v1/activity/remove','api/v1.ActivityManage.Index/remove');

//订单管理
Route::get('work_v1/order','api/v1.Order.Index/index');
Route::get('work_v1/order/detail','api/v1.Order.Index/detail');
Route::post('work_v1/order/addscheme','api/v1.Order.Index/addScheme');
Route::post('work_v1/order/edit','api/v1.Order.Index/edit');

//车辆管理
Route::get('work_v1/car','api/v1.CarManage.Index/index');
Route::post('work_v1/car/add','api/v1.CarManage.Index/add');

//门店管理
Route::get('work_v1/shop','api/v1.ShopManage.Index/index');
Route::post('work_v1/shop/add','api/v1.ShopManage.Index/add');

//公共接口
Route::post('common_v1/area','api/v1.Common/area');
Route::post('public_v1/upload','api/v1.Publics.Upload/upload');
Route::get('public_v1/token','api/v1.Publics.Upload/getToken');
Route::get('common_v1/qcode','api/v1.Common/qcode');

//微信扫码
Route::post('car_v1/scan','api/v1.Screen.Scan/callback');
Route::get('car_v1/scan/state','api/v1.Screen.Scan/state');
