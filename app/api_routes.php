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
Route::get('adnews_v1/:id','api/ad.v1.News/read');     //查询
Route::post('adnews_v1','api/ad.v1.News/add');          //新增
Route::put('adnews_v1/:id','api/ad.v1.News/update');    //修改
Route::delete('adnews_v1/:id','api/ad.v1.News/delete'); //删除
