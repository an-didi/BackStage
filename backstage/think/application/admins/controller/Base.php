<?php


namespace app\admins\controller;

use think\Controller;
use Util\SysDb;


class Base extends Controller
{
    protected $admin;
    protected $db;

    public function __construct()
    {
        parent::__construct();

        // 判断用户是否登录
        $this->admin = session('admin');
        if (!$this->admin){
            // 如果session中没有用户信息，则强制跳转
            header('Location:/index.php/admins/account/login');
            exit;
        }

        // 将admin渲染到登陆成功的主页面
        $this->assign('admin',$this->admin);

        // 新建一个数据库对象在子类中使用
        $this->db = new SysDb;

        // 判断用户是否有权限
        $group = $this->db->table('admins_group')->where(array('gid'=>$this->admin['gid']))->item();
        if (!$group){
            $this->request_error("对不起，您没有权限访问");
        }
        $rights = json_decode($group['rights']);
        // 当前访问的菜单
        $controller = request()->controller();
        $method = request()->action();

        $res = $this->db->table('admins_menus')->where(array('controller'=>$controller, 'method'=>$method))->item();

        if (!$res){
            $this->request_error("对不起，您访问的功能不存在");
        }
        if ($res['status'] === 1){
            $this->request_error("对不起。该功能已经禁止使用");
        }
        if (!in_array($res['mid'],$rights)){
            $this->request_error('对不起，您没有权限');
        }


    }

    private function request_error($msg)
    {
        if (request()->isAjax()){
            exit(json_encode(array('code'=>1, 'msg'=>$msg)));
        }
        exit($msg);
    }
}


