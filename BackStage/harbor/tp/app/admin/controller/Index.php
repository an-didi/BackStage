<?php
namespace app\admin\controller;

use think\Request;
//use think\facade\Request;
use think\facade\Db;
use think\facade\Session;
use lib\Rule;
class Index extends Common
{
//    protected $request;

     // 构造方法的依赖注入
//    public function __construct(Request $request)
//    {
////        $this->request = $request;
//        $this->request = $request['name'];
//    }


    // 操作方法的依赖注入
//    public function index()
//    {
//        // 构造方法依赖注入
////        return $this->request;
////        dump($this->request);
////        dump($request->param());
//        // 操作方法依赖注入
////        return $request->param();
////        return '您好！这是一个[admin]示例应用';
//        // 门面技术，静态调用
////        return Request::param('name');
//
//        // 使用助手函数request()
////        return request()->param('name');
//
//        // 使用助手函数input(),通过get请求的就用get，post请求就用post
////        return input('get.name');
//
//
//
//        // 连接数据库
////        $data['uname'] = '金融大大亨';
////        $data['pwd'] = password_hash('123456', PASSWORD_BCRYPT);
////        $data['create_time'] = time();
////        $data['login_ip'] = request()->ip();
//        // 连接数据库,更新数据库的第一条内容, 更新之后，将下边的更新操作注释
////        Db::name('users')->insert($data);
//
//        // 输出视图
////        return view();
//        // 不输入参数，默认访问的是当前控制器对应的视图的index，传入参数就会访问对应的视图目录下的其他页面
//        // 如果需要给前端页面传入参数，就必须要指定页面，然后将要传入的参数放到一个数组中去，例如：给index页面传入参数
//        $name = $this->request;
//        $data['name'] = $name;
//        return view('index',$data);
////        return view('welcome');
//    }

    public function index()
    {
//        $data['uname'] = '纵横演艺圈';
//        $data['pwd'] = password_hash('123456', PASSWORD_BCRYPT);
//        $data['create_time'] = time();
//        $data['login_ip'] = request()->ip();
//        // 连接数据库,更新数据库的第一条内容, 更新之后，将下边的更新操作注释
//        Db::name('users')->insert($data);
//        dd($data);
        // session返回的是一个二维数组，userInfo字段是我们要拿到的用户信息
        $session = session()['userInfo'];
//        dd($session);
        // 通过检测当前登陆者的uid，获取到该用户所拥有的权限，连表查询
        $res = Db::name('users')->alias('u')->where(['u.uid'=>$session['uid']])
        ->leftJoin('users_role ur', 'ur.uid = u.uid')
        ->leftJoin('auth_role ar', 'ar.id = ur.role_id')
        ->field('u.uid,u.uname,ar.rules')
        ->select()->toArray();
        // res是一个二维数组，获取到这个二维数组的rules字段
        $rules = implode(',',array_column($res, 'rules'));
//        dd($rules);
        // in查询，根据获取到的rules的id来选择权限列表
        $res = Db::name('auth_rule')->field('id,name,title,pid')->where('is_menu', 1)
        ->where('id', 'in', $rules)->select()->order('id', 'asc');
//        dd($res);
        // 得到的res是一个二维的数组，但是不是想要的，我们想要的是拥有pid的数组在它的对应的上级菜单id下，自定义一个函数，用来处理无限级菜单
        $rlist = Rule::ruleLayer($res);
//        dd($rlist);
        $data = [
          'session'=>$session,
          'rlist'=>$rlist,
        ];
        return view('index',$data);

    }

    // 退出登录
    public function logOut()
    {
        // 清除session缓存
        Session::clear();
        // 将页面重定向到登录页面
        return redirect('/login/index');
    }

    // 清除缓存
    public function clearcache()
    {
        // 清除缓存的目的就是想删除，runtime目录下的admin目录下的日志记录 "E:\Visual Studio Code\harbor\tp\runtime\admin\"

        // 1. 获取到admin应用的运行时目录
        $runtime_path = app()->getRuntimePath();
//        dd($runtime_path);
        // 删除一个目录之前，需要将目录下的所有文件都删除干净
        // 调用一个自定义函数， delete_dir_file
        if(delete_dir_file($runtime_path)){
            return json(['status'=>1, 'msg'=>'清除成功']);
        }else{
            return json(['status'=>0, 'msg'=>'清除失败']);
        }

    }

    // 欢迎页面
    public function welcome()
    {
        return view('welcome');
    }
}