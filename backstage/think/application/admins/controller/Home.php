<?php
 // 主页面控制器

namespace app\admins\controller;

use think\Controller;
use Util\SysDb;

class Home extends Base
{

    public function index()
    {
        // 动态菜单加载
        // 获取当前角色
        $role = $this->db->table('admins_group')->where(array('gid'=>$this->admin['gid']))->item();
        // 如果当前角色存在
        if ($role){
            // 获取到当前角色拥有的权限
            $role['rights'] = $role['rights']?json_decode($role['rights'],true) : [];
        }

        if ($role['rights']){
            // 如果当前权限存在，将当前角色的所拥有的可访问菜单获取
            // 创建查询条件
            $where = 'mid in(' . implode(',', $role['rights']) . ') and ishidden=0 and status=0';
            // 获取当前可访问菜单
            $menus = $this->db->table('admins_menus')->where($where)->cates('mid');
            $menus && $menus = $this->gettreeitems($menus);

//            dump($menus);
//            dump($role);
            $data['menus'] = $menus;
            $data['role'] = $role;
            return $this->fetch('', $data);
        }
    }

    public function welcome()
    {
        return $this->fetch();
    }

    private function gettreeitems(array $items): array
    {
        // 将一维数组变为我们想要的多维数组
        $tree = array();
        foreach ($items as $item) {
            // 判断items的上级菜单在item中是否存在
            if (isset($items[$item['pid']])) {
                // 将当前菜单放到上级菜单的“children”字段中去,引用传递
                $items[$item['pid']]['children'][] = &$items[$item['mid']];
            } else {
                // 没有上级菜单，直接放到tree中去
                $tree[] = &$items[$item['mid']];
            }
        }
        return $tree;
    }
}


