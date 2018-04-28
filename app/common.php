<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

/*
* 	返回数据到客户端
*	@param $code type : int		状态码
*   @param $info type : string  状态信息
*	@param $data type : mixed	要返回的数据
*	return json
*/
function apiReturn($code, $data = null, $msg = ''){
    header('Content-Type:application/json; charset=utf-8');//返回JSON数据格式到客户端 包含状态信息

    $jsonData = array(
        'code' => $code,
        'msg'  => $msg ?: ($code == 200 ? '操作成功' : '操作失败'),
        'data' => $data ? $data : null
    );

    exit(json_encode($jsonData));
}


//得到高强度不可逆的加密字串
function getSuperMD5($str) {
    return MD5(SHA1($str) . '@$^^&!##$$%%$%$$^&&asdtans2g234234HJU');
}

function checkPhone($phone){
    if(!preg_match_all("/^1([358][0-9]|4[579]|66|7[0135678]|9[89])[0-9]{8}$/", $phone, $array)){
        return false;
    }
    return true;
}

function checkEmail($email){
    return preg_match("/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i", $email);
}

/*
 * 生成单号
 * */
function makeOrder(){
    $order = date('YmdHis');
    $array = explode('.', microtime(true));
    return $order . end($array);
}
