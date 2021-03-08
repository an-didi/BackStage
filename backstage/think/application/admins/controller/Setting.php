<?php


namespace app\admins\controller;

use think\Controller;
use Util\SysDb;
class Setting extends Base
{
    // 网站设置
    public function index()
    {
        $data['item'] = $this->db->table('setting')->where(array('names'=>'site_setting'))->item();
        if($data){
            $data['item']['values'] = json_decode($data['item']['values'],true);
        }
//        dump($data);
        return $this->fetch('', $data);
    }

    public function save()
    {
        $data['names'] = trim(input('post.names'));
        $data['values'] = json_encode(input('post.values'));

        // 到setting表中查询是否有数据
        $item = $this->db->table('setting')->where(array('names'=>$data['names']))->item();
        if ($item){
            // 如果记录已经存在，则进行修改，如果没有则添加
            $res = $this->db->table('setting')->where(array('names'=>$data['names']))->update($data);
            if ($res){
                exit(json_encode(array('code'=>0, 'msg'=> '修改成功')));
            }else{exit(json_encode(array('code'=>0, 'msg'=>'修改失败')));}

        }else{
            $res = $this->db->table('setting')->insert($data);
            if ($res){
                exit(json_encode(array('code'=>0, 'msg'=>'保存成功')));
            }else{exit(json_encode(array('code'=>0, 'msg'=>'保存失败')));}

        }

    }
}