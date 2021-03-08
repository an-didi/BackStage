<?php

namespace app\admins\controller;

    use think\Controller;
    use Util\SysDb;

    class Account extends Controller
    {
        private $db;
        public function login()
        {
            // 创建一个自定义的数据库访问类的方法
//            $this->db = new SysDb();
//
//            // 链式调用
//            $this->db->table('admins')->where(array('id'=>1))->lists();
//            echo md5('admin123456');

            return $this->fetch();
        }

        public function dologin()
        {
            $username = trim(input('post.username'));
            $password = input('post.password');
            $verifycode = input('post.verifycode');

            if ($username == ''){
                exit(json_encode(array('code'=>1,'msg'=>'用户名不能为空')));
            }

            if ($password == ''){
                exit(json_encode(array('code'=>1, 'msg'=>'密码不能为空')));
            }

            // 验证码
            if ($verifycode == ''){
                exit(json_encode(array('code'=>1, 'msg'=>'验证码不能为空')));
            }

            if (captcha_check($verifycode)){
                exit(json_encode(array('code'=>1, 'msg'=>'验证码不正确')));
            }

            // 验证用户
            $this->db = new SysDb();
            $admin = $this->db->table('admins')->where(array('username'=>$username))->item();
            if (!$admin){
                exit(json_encode(array('code'=>1, 'msg' => '用户不存在')));
            }
            // 判断用户密码,在这里给密码加盐
            if (md5($admin['username'].$password) != $admin['password']){
                exit(json_encode(array('code'=>1, 'msg'=>'用户密码错误')));
            }
            // 判断用户是否被禁用
            if ($admin['status'] == 1){
                exit(json_encode(array('code'=>1, 'msg'=>'用户已被禁用')));
            }

            // 如果用户可用，设置用户session
            session('admin', $admin);
            exit(json_encode(array('code'=>0, 'msg'=>'登录成功')));
        }

        // 退出登录
        public function logout(){
            session('admin',null);
            exit(json_encode(array('code'=>0,'msg'=>'退出成功')));
        }
    }

