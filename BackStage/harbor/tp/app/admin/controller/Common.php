<?php


namespace app\admin\controller;

use app\BaseController;
use think\facade\Session;
use lib\Auth;//权限认证类
class Common extends BaseController
{
    public function initialize()
    {
        // 拿到登录信息
        $session = session();
//        $session = $session['userInfo'];
        // 如果没有登录成功或者没有登陆的情况下，不允许访问其他页面
        if (!$session){
            // 自定义跳转公共函数
            jumpTo('/login/index');
            exit;
        }else{
            $auth = new Auth;
            if (!$auth->check(request()->controller() . '/' . request()->action(), $session['userInfo']['uid'])){
                historyTo('抱歉~你没有操作该栏目的权限，请联系管理员！');
                exit;
            }
        }
    }
}