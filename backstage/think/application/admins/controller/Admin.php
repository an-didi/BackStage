<?php


namespace app\admins\controller;


use think\Controller;
use Util\SysDb;
class Admin extends Base
{
    public function index()
    {
        // 加载管理员列表
        $data['lists'] = $this->db->table('admins')->order('id desc')->lists();
        return $this->fetch('',$data);
    }

    // 添加管理员
    public function add()
    {
        //为了安全起见，将id进行强制转换成int，因为页面传过来的信息有可能是int，也有可能是undefined
        $id = (int)input('get.id');
        // 将查询的数据放到data中
        $data['item'] = $this->db->table('admins')->where(array('id'=>$id))->item();
        return $this->fetch('',$data);
    }

    public function save()
    {
        // 接收传回的数据
        $data['username'] = trim(input('post.username'));
        $data['gid'] = (int)input('post.gid');
        $data['truename'] = trim(input('post.truename'));
        $data['status'] = (int)input('post.status');
        $password = input('post.password');
        $id = (int)input('post.id');

        // 对接收的数据进行检查
        if (!$data['username']){
            exit(json_encode(array('code'=>1, 'msg'=>'用户名不能为空')));
        }
        if (!$data['gid']){
            exit(json_encode(array('code'=>1,'msg'=>'角色不能为空')));
        }
        if (!$data['truename']){
            exit(json_encode(array('code'=>1,'msg'=>'真实姓名不能为空')));
        }

        // 对id进行判断，来决定页面是编辑管理员还是添加管理员
        if ($id == 0 && !$password){
            exit(json_encode(array('code'=>1,'msg'=>'密码不能为空')));
        }

        // 对密码进行加盐处理
        if ($password){
            $data['password'] = md5($data['username'].$password);
        }


        // 声明一个变量res来保存数据更新成功的状态
        $res = true;
        // 如果$id === 0 的话，那么就是添加管理员
        if ($id == 0){
            // 添加管理员
            // 判断添加的用户在不在数据表中
            $item = $this->db->table('admins')->where(array('username'=>$data['username']))->item();
            if ($item){
                exit(json_encode(array('code'=>1, 'msg'=>'用户已存在')));
            }

            // 将当前时间给add_time
            $data['add_time'] = time();
            // 添加信息
            $res = $this->db->table('admins')->insert($data);
        }else{
            // id != 0, 编辑管理员
            $res = $this->db->table('admins')->where(array('id'=>$id))->update($data);
        }

        if ($res){
            exit(json_encode(array('code'=>0, 'msg'=>'保存成功')));
        }else{
            exit(json_encode(array('code'=>1, 'msg'=>'保存失败')));
        }
    }

    // 删除管理员
    public function delete()
    {
        // 获取id
        $id = (int)input('post.id');

        // 从数据库中删除id = id的信息
        $res = $this->db->table('admins')->where(array('id'=>$id))->delete();

        if ($res){
            exit(json_encode(array('code'=>0,'msg'=>'删除成功')));
        }
        exit(json_encode(array('code'=>1, 'msg'=>'删除失败')));

    }



}