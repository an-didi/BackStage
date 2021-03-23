<?php


namespace app\admin\controller;

use app\admin\model\Cat;
use think\facade\Db;
class Cate extends Common
{
    // 分类的主页面
    public function index(Cat $Cat)
    {
        if (request()->isPost()){
            $data = request()->param();
            $res = $Cat->Cate($data);

//            dd($res);
            echo json_encode($res);

        }
        if (request()->isGet()){
            return view();
        }
    }

    // 新增分类
    public function add()
    {
        if (request()->isPost()){
            $data = request()->param();
            // 检测分类名称是否存在，如果已经存在，重新定位到分类名称栏
            $catname = Db::name('cate')->where('catname', $data['catname'])->find();
            if ($catname){
                exit(json_encode(array('code'=>-1, 'msg'=>'输入的分类名称当前已存在，请重新输入!')));
            }else{
                $data['create_time'] = time();
                // 新增数据
                $res = Db::name('cate')->insert($data);
                if ($res){
                    exit(json_encode(array('code'=>1, 'msg'=>'新增成功！')));
                }else{
                    exit(json_encode(array('code'=>0, 'msg'=>'新增失败！')));
                }
            }
        }

        if (request()->isGet()){
            return view('add');
        }
    }

    // 编辑分类
    public function update()
    {
        if (request()->isPost()){
            $data = request()->param();
            // 更新数据表
            $res = Db::name('cate')->where('catid', $data['catid'])->update($data);
            if ($res){
                exit(json_encode(array('code'=>1, 'msg'=>'更新成功！')));
            }else{
                exit(json_encode(array('code'=>0, 'msg'=>'更新失败！')));
            }
        }

        if (request()->isGet()){
            $catid = input('get.catid');
            // 通过id查找数据返回
            $data['info'] = Db::name('cate')->field('catid,catname,status')->where('catid',$catid)->find();
//            dd($data);
            return view('update', $data);
        }
    }

    // 删除分类
    public function del()
    {
        if (request()->isPost()){
            $catid = input('post.catid');
            // 删除数据
            $res = Db::name('cate')->delete($catid);
            if ($res){
                exit(json_encode(array('code'=>1, 'msg'=>'删除成功！')));
            }else{
                exit(json_encode(array('code'=>0, 'msg'=>'删除成功！')));
            }
        }
    }
}