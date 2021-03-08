<?php


namespace app\admins\controller;

use think\Controller;
use Util\SysDb;
class Menu extends Base
{
    // 加载菜单列表
    public function index()
    {
        // 获取pid，然后通过pid来对子菜单进行定位
        $pid = (int)input('get.pid');
        $where['pid'] = $pid;
        // 加载菜单列表
        $data['lists'] = $this->db->table('admins_menus')->where($where)->order('ord asc')->lists();
        $data['pid'] = $pid;

        // 处理子菜单
        if ($pid>0){
            // 加载上级菜单
            $parent = $this->db->table('admins_menus')->where(array('mid'=>$pid))->item();
            $data['backid'] = $parent['pid'];
        }
        return $this->fetch('', $data);
    }

    // 添加、编辑菜单
    public function add()
    {
        // 获取pid，通过pid查询数据库中mid等于pid的记录
        $pid = (int)input('get.pid');
        $mid = (int)input('get.mid');
        // 上级菜单信息
        $data['parent_menu'] = $this->db->table('admins_menus')->where(array('mid'=>$pid))->item();
        // 当前菜单的信息
        $data['menu'] = $this->db->table('admins_menus')->where(array('mid'=>$mid))->item();
        return $this->fetch('',$data);
    }

    // 保存菜单
    public function save()
    {
        $data['pid'] = (int)input('post.pid');
        $data['title'] = trim(input('post.title'));
        $data['controller'] = trim(input('post.controller'));
        $data['method'] = trim(input('post.method'));
        $data['ord'] = (int)input('post.ord');
        $data['ishidden'] = (int)input('post.ishidden');
        $data['status'] = (int)input('post.status');
        // 传入当前菜单的mid
        $mid = (int)input('post.mid');

        // 对传进来的数据进行判断
        if ($data['title'] == ''){
            exit(json_encode(array('code'=>1, 'msg'=>'菜单名不能为空')));
        }

        // 判断pid是否大于0来知道当前菜单是主菜单还是子菜单
        if ($data['pid'] > 0 && $data['controller'] == ''){
            exit(json_encode(array('code'=>1, 'msg'=>'控制器名称不能为空')));
        }

        if ($data['pid'] > 0 && $data['method'] == ''){
            exit(json_encode(array('code'=>1, 'msg'=>'方法名称不能为空')));
        }

        // 如果有mid就是更新菜单,如果没有就是插入菜单
        if ($mid){
            $res = $this->db->table('admins_menus')->where(array('mid'=>$mid))->update($data);
        }else{
            $res = $this->db->table('admins_menus')->insert($data);
        }


        if ($res){
            exit(json_encode(array('code'=>0, 'msg'=>'保存成功')));
        }
        exit(json_encode(array('code'=>1,'msg'=>'保存失败')));

    }


    // 删除菜单
    public function delete()
    {
        $mid = (int)input('post.mid');
        $res = $this->db->table('admins_menus')->where(array('mid'=>$mid))->delete();
        if ($res){
            exit(json_encode(array('code'=>0, 'msg'=>'删除成功')));
        }

        exit(json_encode(array('code'=>1, 'msg'=>'删除失败')));
    }
}