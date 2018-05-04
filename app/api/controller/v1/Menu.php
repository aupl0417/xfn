<?php
/**
 * Created by PhpStorm.
 * User: liaozijie
 * Date: 2018-04-25
 * Time: 9:28
 */

namespace app\api\controller\v1;

use think\Controller;
use think\Db;
class Menu extends Home
{

    /*
     * 汽车品牌
     * */
    public function brand(){
        $model = model('Brand');
        $data  = $model->getBrandList();
        !$data && $this->apiReturn(201);
        $this->apiReturn(200, $data);
    }

    /*
     * 通过汽车品牌ID获取车系
     * */
    public function series(){
        (!isset($this->data['bid']) || empty($this->data['bid'])) && $this->apiReturn(201, '', '品牌ID非法');
        $brandId = $this->data['bid'] + 0;
        
        $model = model('Brand');
        $data  = $model->getCarFamilyByBrandId($brandId);
        !$data && $this->apiReturn(201);
        $this->apiReturn(200, $data);
    }

    /*
     * 通过车系查询
     * */
    public function search(){
        (!isset($this->data['fid']) || empty($this->data['fid'])) && $this->apiReturn(201, '', '系列ID非法');
        $page = isset($this->data['page']) ? $this->data['page'] + 0: 1;

        $familyId = $this->data['fid'] + 0;
        $field = 'ac_id as id,c_name as name,c_image as image,c_introPrice as price,c_output as output,c_styleName as styleName';
        $model = model('Car');
        $data  = $model->getCarByFamilyId($familyId, $field, $page);
        !$data && $this->apiReturn(201);
        $this->apiReturn(200, $data);
    }
}