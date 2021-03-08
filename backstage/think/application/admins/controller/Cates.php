<?php


namespace app\admins\controller;

use think\Controller;
use Util\SysDb;
class Cates extends Base
{
    // 商品分类
    public function index(){
        $data['cates'] = $this->db->table('product_cates')->order('ord asc')->lists();
        return $this->fetch('',$data);
    }

    public function save(){
        $ords = input('post.ords/a');
        $titles = input('post.titles/a');
        $status = input('post.status/a');
        foreach ($ords as $key => $value) {
            $data['ord'] = $value;
            $data['title'] = $titles[$key];
            $data['status'] = isset($status[$key])?1:0;

            if($key == 0 && $data['title']){
                $this->db->table('product_cates')->insert($data);
            }
            if($key>0){
                $this->db->table('product_cates')->where(array('id'=>$key))->update($data);
            }
        }
        exit(json_encode(array('code'=>0,'msg'=>'保存成功')));
    }
}