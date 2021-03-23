<?php
namespace app\admin\controller;

//use think\captcha\Captcha;
use think\Request;
use think\captcha\facade\Captcha;
use think\facade\Db;
use think\facade\Session;

class Login
{

    public function index()
    {
        // 判断是否是post请求
        if (request()->isPost()) {
            $uname = input('post.uname');
            $pwd = input('post.pwd');
//            dd(request()->param());
            $res = Db::name('users')->field('uid,uname,pwd')->where('uname', $uname)->find();
//            dd($res);
            // 检查密码是否正确
            if ($res){
                if (password_verify($pwd, $res['pwd'])){
                    // 用户名与密码匹配成功
                    Session::set('userInfo',$res);
                    echo 'true';
                }else{
                    echo 'false';
                }
            }else {
                echo 'false';
            }
        } else {
            return view();
        }
    }

    // 自定义验证码
    public function verify()
    {
        return Captcha::create('verify');
    }

    // 验证码校验
    public function checkCaptcha()
    {
        // 使用全局函数captcha_check()来检测验证码是否正确,它的返回值是bool
//        $res = captcha_check(input('post.captcha'));
//        dd($res);
        if (captcha_check(input('post.captcha'))){
            return true;
        }
        return false;
    }

}
