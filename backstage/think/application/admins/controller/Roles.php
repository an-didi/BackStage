<?php
// 角色列表的控制器

namespace app\admins\controller;

use think\Controller;
use Util\SysDb;
class Roles extends Base
{
    public function index(){
        // 访问数据库的角色表
        $data['lists'] = $this->db->table('admins_group')->lists();
        // 渲染到页面
        return $this->fetch('', $data);
    }

    // 添加角色信息
    public function add()
    {
        // 读取gid，判断是编辑还是添加
        $gid = (int)input('get.gid');

        // 拿到当前角色的详细信息
        $group = $this->db->table('admins_group')->where(array('gid'=>$gid))->item();
//        dump($group);
        // admins_group表中rights字段中存储的是json数据,所以需要将json格式数据转为php数据
        if ($group){
            $group['rights'] = json_decode($group['rights']);
        }

        // 读取菜单列表
        $menu_list = $this->db->table('admins_menus')->where(array('status'=>0))->cates('mid');

        // 定义一个结果数组menu,让它得到第一步按照菜单等级划分的数组
        $menu = $this->gettreeitems($menu_list);

        // 对menu进行foreach处理，将其变成一个二维数组，此时的menu是一个多维数组
        $result = array();
        foreach ($menu as $value){
            // 判断value中是否有children字段，有的话就对这个value进行处理
            $value['children'] = isset($value['children']) ? $this->formatMenus($value['children']) : false;
            $result[] = $value;
        }
//        dump($result);
        $data['menus'] = $result;
        $data['group'] = $group;
        return $this->fetch('', $data);
    }

    // 定义一个gettreeitems方法，处理读取到的菜单列表，得到第一步处理的结果
    private function gettreeitems(array $items)
    {
        // 将一维数组变为我们想要的多维数组
        $tree = array();
        foreach ($items as $item){
            // 判断items的上级菜单在item中是否存在
            if (isset($items[$item['pid']])){
                // 将当前菜单放到上级菜单的“children”字段中去,引用传递
                $items[$item['pid']]['children'][] = &$items[$item['mid']];
            }else{
                // 没有上级菜单，直接放到tree中去
                $tree[] = &$items[$item['mid']];
            }
        }

        // 处理完成，返回tree
        return $tree;
    }

    // 定义一个formatMenus方法，处理得到的多维数组，让它返回一个二维数组
    private function formatMenus(array $items, &$res = array())
    {
        foreach ($items as $item){
            // 判断是否有children存在，递归条件出口
            if (!isset($item['children'])){
                $res[] = $item;
            }else{
                // 存在子菜单
                $tmp = $item['children'];
                // 将$item['children']unset掉
                unset($item['children']);
                // 将剩余的没有children字段的数组放到res中去
                $res[] = $item;
                // 使用递归处理
                $this->formatMenus($tmp, $res);
            }
        }
        // 返回res
        return $res;
    }

    // 角色保存
    public function save()
    {
        // 接收角色名称
        $data['title'] = trim(input('post.title'));
        // 接收管理员权限菜单
        $menus = input('post.menu/a');

        // 接收gid，判断是添加还是编辑
        $gid = (int)input('post.gid');
        if (!$data['title']){
            exit(json_encode(array('code'=>1, 'msg'=>'角色名称不能为空')));
        }
        // 如果有menus，将menus中的key全部取出来,因为保存在数据库的权限字段是json数据,所以需要对其进行json_encode
        if ($menus == []){
            exit(json_encode(array('code'=>1,'msg'=>'没有信息')));
        }
        $menus && $data['rights'] = json_encode(array_keys($menus));

        // 如果有gid，就是编辑，此时save方法就是更新，如果gid不存在，此时save方法是新建
        if ($gid){
            // 更新数据表
            $res = $this->db->table('admins_group')->where(array('gid'=>$gid))->update($data);
            if ($res){
                exit(json_encode(array('code'=>0, 'msg'=>'更新成功')));
            }
            exit(json_encode(array('code'=>1, 'msg'=>'更新失败')));
        }else{
            // 将数据插入数据库
            $res = $this->db->table('admins_group')->insert($data);
            if ($res){
                exit(json_encode(array('code'=>0, 'msg'=>'保存成功')));
            }
            exit(json_encode(array('code'=>1, 'msg'=>'保存失败')));
        }

    }

    // 删除角色
    public function delete()
    {
        $gid = (int)input('post.gid');
        $res = $this->db->table('admins_group')->where(array('gid'=>$gid))->delete();
        if ($res){
            exit(json_encode(array('code'=>0, 'msg'=>'删除成功')));
        }
        exit(json_encode(array('code'=>1, 'msg'=>'删除失败')));
    }
}