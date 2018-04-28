<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/14 0014
 * Time: 13:39
 */
namespace app\admin\validate;
use think\Validate;


class AddSeller extends Validate{

    protected $rule = [
        's_name'      => 'require|unique:seller',
        's_phone'     => 'require',
    ];

    protected $message = [
        's_name.require'      => '请输入姓名',
        's_name.unique'       => '该姓名已存在',
        's_phone.require'     => '请输入手机号码',
    ];

}