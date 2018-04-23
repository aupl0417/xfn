<?php
/**
 * Created by PhpStorm.
 * User: liaozijie
 * Date: 2018-04-19
 * Time: 15:40
 */

$data = array(
    'id' => 1,
    'name' => 'aupl',
    'sex'  => 1,
    'addr' => '白云区白云山'
);
echo json_encode($data);

$arr = array(
    array('id' => 1, 'name' => 'aupl1'),
    array('id' => 2, 'name' => 'aupl2'),
    array('id' => 3, 'name' => 'aupl3'),
    array('id' => 4, 'name' => 'aupl4'),
);

foreach($arr as $key => &$val){
    $arr2[] = $val;
}

var_dump('aa');
var_dump($val);
unset($val);
@var_dump($val);


//echo phpinfo();