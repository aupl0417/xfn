<?php
namespace app\api\controller\ad\v1;

use think\Controller;
use think\Db;
class Index extends Controller\Rest
{
    private $methodType = array('get', 'post', 'put', 'patch', 'delete');

    public function __call($name, $arguments)
    {
        echo '调用不存在的方法名是：' . $name . '<br>参数是：';
        print_r($arguments);
        echo '<br>';
    }

    public function index()
    {
        echo request()->controller() . PHP_EOL;
        echo request()->action();
    }

    public function test(){
        echo request()->action();
    }
}
