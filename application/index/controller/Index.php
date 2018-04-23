<?php
namespace app\index\controller;

class Index
{
    public function index()
    {
//        $data = 'hello';
        $cacheKey = md5('xfn_123');
        if(!$data = cache($cacheKey)){
            $data = 'hello';
            cache($cacheKey, $data, 10);
        }
        echo $data;
    }
}
