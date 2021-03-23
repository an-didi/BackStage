<?php


namespace app\admin\controller;

use app\admin\model\AuthRule;
use think\facade\Db;
use lib\Rule;
use app\admin\model\AuthRole;
class Auth extends Common
{
    public function index()
    {
        // 使用的是dataTables框架来进行table的渲染，它传到后端的数据有很多，必须要返回的数据有
        // draw:这个是用来确保Ajax从服务器返回的是相对应的（Ajax是异步的，因此返回的顺序是不确定的）。 要求在服务器接收到此参数后再返回,强制转换成整型，防止xss攻击
        // recordsTotal:记录总数，没有过滤之前（数据库里表的总记录数）
        // recordsFiltered:过滤后的总记录数（如果有接收到前台的过滤条件，则返回的是过滤后的记录数，而不仅仅是当前页的记录数）
        // data:要在表中显示的数据。这是一个数据源对象数组，每一个项代表一行，Datatables将使用该数组。 请注意，可以使用 ajaxOption 选项的 ajax.dataSrcOption 属性更改此参数的名称

        if (request()->isPost()){
            $data = request()->param();
//            dump($data);
            $draw = intval($data['draw']);
            // 从那一列进行排序
            $order_column = $data['order'][0]['column'];
            // 降序还是升序
            $order_dir = $data['order'][0]['dir'];

            // 排序的sql语句,如果order_column存在，则说明用户选择了排序,对order sql语句进行拼接
            $orderSql = '';
            if (isset($order_column)){
                // 根据前端显示的用户表可以知道，column==0的时候是根据uid排序的，column==3的时候是根据创建时间排序的
                $column = intval($order_column);
                switch ($column){
                    case 0;$orderSql = ' order by u.uid ' . $order_dir;break;
                    case 3;$orderSql = ' order by u.create_time ' . $order_dir;break;
                    default;$orderSql = '';
                }
            }

            // 拼接分页的sql语句，接收分页参数，然后对limit sql语句进行拼接
            $start = $data['start'];
            $length = $data['length'];
            $limitSql = '';
            // 检查一下传进来的分页参数，如果符合要求，才会对分页sql语句进行拼接
            // 当length==-1时，表示应该返回所有的记录
//            $limitFlag = isset($start) && $length != -1;
            if (isset($start) && $length != -1){
              $limitSql = ' limit ' . intval($start) . "," . intval($length);
            }

            // 模糊查询的搜索条件拼接
            $search = $data['search']['value'];
            $searchSql = " where (u.uid LIKE '%" . $search . "%' or u.uname LIKE '%" . $search . "%')";

            // 获取数据的总行数
            $sumSql = "SELECT count(uid) as sum FROM users as u ";
            $recordsTotal = Db::query($sumSql)[0]['sum'];
//            dd($recordsTotal);

            // 过滤后的总条数。过滤是指搜索过之后的数据，需要先判断是否进行搜索过程
            $recordsFiltered = 0;
            // 如果按照过滤条件查询
            if (strlen($search)>0){
                // 进行sql语句查询,获取到搜索结果过滤后的总数据数量
                $recordsFiltered = Db::query($sumSql . $searchSql)[0]['sum'];
//                dd($recordsFiltered);
            }else{
                // 如果没有搜索条件
                $recordsFiltered = $recordsTotal;
            }

            // 由于显示在前端的并不是一张数据表中的内容，所以需要进行连表查询
            $totalSql = "select u.uid,u.uname,u.create_time,u.status,ar.title from users u left join users_role ur on ur.uid=u.uid left join auth_role ar on ar.id=ur.role_id";

            // 要在前端数据表中显示的数据
            $data = Db::query($totalSql . $searchSql . $orderSql . $limitSql);
//            dd($data);

            // 对data数据进行处理再传到前端显示
            foreach ($data as $k => $vl){
                $data[$k]['status'] = $vl['status'] == 1 ? '启用' : '禁用';
                $data[$k]['create_time'] = date('Y-m-d H:i:s', $vl['create_time']);
            }

//            dd($data);
            // 给前端返回数据,json_encode()的第二个参数传入常量，避免中文编码出问题
            echo json_encode(array(
                'draw'=>$draw,
                'recordsTotal'=>$recordsTotal,
                'recordsFiltered'=>$recordsFiltered,
                'tableData'=>$data
            ),320);
        }else{
            return view();
        }

    }

    public function add()
    {
        if (request()->isPost()){
            $unames = Db::name('users')->field('uname')->select()->toArray();
            $unames = array_column($unames, 'uname');
            // 判断输入的用户名是否重复
            $uname = input('post.uname');
            $role_id = intval(input('post.role_id'));
            if (in_array($uname, $unames)){
                exit(json_encode(array('code'=>-1, 'msg'=>'输入的用户名已存在，请重新输入')));
            }
            // 判断是否是对超级管理员进行角色赋予
            if ($role_id ===  1){
                exit(json_encode(array('code'=>-1, 'msg'=>'不可以新增一个超级管理员')));
            }
            $data = [
                'uname'=>$uname,
                'pwd'=>password_hash(input('post.pwd'), PASSWORD_BCRYPT),
                'create_time'=>time(),
                'login_ip'=>request()->ip(),
                'status'=>input('post.status'),
            ];
//            dd($data);
            // 因为需要更新完users表后，再更新users_role表,所以需要进行插入的uid的获取
            $uid = Db::name('users')->insertGetId($data);
            if ($uid){
                $datas = [
                    'uid'=>$uid,
                    'role_id'=>$role_id
                ];
                $res = Db::name('users_role')->insertGetId($datas);
                if ($res){
                    exit(json_encode(array('code'=>1, 'msg'=>'新增成功!')));
                }else{
                    exit(json_encode(array('code'=>0, 'msg'=>'新增失败!')));
                }
            }else {
                exit(json_encode(array('code' => 0, 'msg' => '新增失败!')));
            }
        }

        if (request()->isGet()){
            $roles = Db::name('auth_role')->field('id,title')->order('id', 'asc')->where('status',1)->select();
            $data['roles'] = $roles;
            return view('add',$data);
        }
    }

    public function update()
    {
        // 更新用户信息，需要更新用户表和用户角色关联表
        if (request()->isPost()){
            $data = request()->param();
//            dd($data);
            // 更新用户表
            $data['create_time'] = time();
            $data['login_ip'] = request()->ip();
            unset($data['role_id']);
            $res = Db::name('users')->where('uid',$data['uid'])->update($data);

            // 如果用户表更新成功，才会去更新用户角色关联表
            if ($res){
                $res = Db::name('users_role')->where('uid',$data['uid'])->update(['role_id'=>input('post.role_id')]);
                if ($res){
                    exit(json_encode(array('status'=>1, 'msg'=>'更新成功！')));
                }else{
                    exit(json_encode(array('status'=>0, 'msg'=>'更新失败！')));
                }
            }else{
                exit(json_encode(array('status'=>0, 'msg'=>'更新失败！')));
            }
        }
        if (request()->isGet()){
            $uid = input('get.uid');
//        dd($uid);
            // 根据uid获取用户的数据,需要获取到的数据有：1.所有的角色的title， 2.当前用户名，3.当前用户的状态
            $roles = Db::name('auth_role')->field('id,title')->order('id', 'asc')->where('status',1)->select();
            $data['roles'] = $roles;

            // 获取用户信息
            $info = Db::name('users')->alias('u')->field('u.uname,u.uid,u.status,ur.role_id')->join('users_role ur', 'u.uid=ur.uid')->join('auth_role ar', 'ar.id=ur.role_id')->where('u.uid',$uid)->find();
            $data['info'] = $info;
            return view('update',$data);
        }
    }

    // 修改密码
    public function resetPwd()
    {
        if (request()->isPost()){
            $uid = input('post.uid');
            $role_id = Db::name('users_role')->where('uid', $uid)->field('role_id')->find()['role_id'];
            if ($role_id == 1){
                exit(json_encode(array('code'=>-1, 'msg'=>'你没有权限修改超级管理员的密码！')));
            }else{
                $pwd = password_hash(input('post.pwd'), PASSWORD_BCRYPT);
                // 对其他用户的密码进行修改
                $res = Db::name('users')->where('uid', $uid)->update(['pwd'=>$pwd]);
                if ($res){
                    exit(json_encode(array('code'=>1, 'msg'=>'密码重置成功！')));
                }else{
                    exit(json_encode(array('code'=>0, 'msg'=>'密码重置失败！')));
                }
            }
        }
        if (request()->isGet()){
            $uid = input('get.uid');
            $data['uid'] = $uid;
            $data['uname'] = Db::name('users')->field('uname')->where('uid',$uid)->find()['uname'];
            return view('resetPwd',$data);
        }
    }

    // 删除用户
    public function delete()
    {
        if (request()->isPost()){
            // 删除用户信息，不仅要删除用户表，还需要删除用户角色关联表
            // 删除用户表
            $uid = intval(input('post.uid'));
            $delsql = "delete u,ur from users u inner join users_role ur on u.uid=ur.uid where u.uid=" . $uid;
            $res = Db::execute($delsql);
            if ($res){
                exit(json_encode(array('code'=>1, 'msg'=>'删除成功！')));
            }
            exit(json_encode(array('code'=>0,'msg'=>'删除失败!')));
        }
    }

    /*****************************规则管理**************************************
     * @param AuthRule $AuthRule
     * @return \think\response\View
     */

    public function rule(AuthRule $AuthRule)
    {
        // 使用了模型，让控制器的操作更加简单，逻辑更加顺畅，数据库操作全部交给模型处理
        if (request()->isPost()){
            $data = request()->param();
            $res = $AuthRule->AuthRule($data);
//            dd($res);
            // 对当前得到的数据进行无限级菜单的处理
            $tableData = Rule::RuleList($res['tableData']);
//            dd($res);


            $res['tableData'] = empty($tableData) ? $res['tableData'] : $tableData;
//            dd($res);
            echo json_encode($res);
        }


        if (request()->isGet()){
            return view('rule');
        }
    }

    // 删除规则
    public function delrule()
    {
        $id = input('post.id');
        // 首先要做的就是查询当前菜单下有没有子菜单，将id当作pid传入数据表中进行查询，如果存在，就不能进行删除
        $res = Db::name('auth_rule')->where('pid', $id)->select()->toArray();
//        dd($res);
        if (!empty($res)){
            exit(json_encode(array('code'=>-1, 'msg'=>'请先删除子菜单再进行此操作！')));
        }else{
            // 如果为空，说明当前菜单下没有子菜单了，可以进行删除
            $res = Db::name('auth_rule')->delete($id);
            if ($res){
                exit(json_encode(array('code'=>1, 'msg'=>'删除成功！')));
            }else{
                exit(json_encode(array('code'=>0, 'msg'=>'删除失败！')));
            }
        }
    }

    // 更新规则
    public function updaterule()
    {
        if (request()->isPost()){
            $data = request()->param();
//            dd($data);

            $id = $data['id'];
            unset($data['id']);

            // 更新auth_rule数据表的信息
            $res = Db::name('auth_rule')->where('id', $id)->update($data);

            if ($res){
                exit(json_encode(array('status'=>1, 'msg'=>'更新成功！')));
            }else{
                exit(json_encode(array('status'=>0, 'msg'=>'更新失败！')));
            }

        }

        if (request()->isGet()){
            $id = input('get.id');
            $data['info'] = Db::name('auth_rule')->where('id',$id)->find();
            return view('updaterule',$data);
        }
    }

    // 新增一个规则，如果没有pid存在就是新增一个一级菜单，如果有的话就是新增一个子菜单
    public function addrule()
    {
        if (request()->isPost()){
            $data = request()->param();
            $res = Db::name('auth_rule')->insert($data);
            if ($res){
                exit(json_encode(array('status'=>1, 'msg'=>'新增成功！')));
            }else{
                exit(json_encode(array('status'=>0, 'msg'=>'新增失败！')));
            }
        }

        if (request()->isGet()){
            // 获取到传进来的当前菜单的id值，作为新增的菜单的pid传到页面中
            $pid = input('get.pid');
            $data['pid'] = $pid ? $pid : 0;
            return view('addrule',$data);
        }
    }

    /*********************************角色管理*****************************
     * @param AuthRole $AuthRole
     * @return \think\response\View
     */

    // 角色列表
    public function group(AuthRole $AuthRole)
    {
        if (request()->isPost()){
            $data = request()->param();
            $res = $AuthRole->AuthRole($data);
//            dd($res);

            echo json_encode($res);
        }

        if (request()->isGet()){
            return view('group');
        }
    }

    // 新增角色
    public function addgroup()
    {
        if (request()->isPost()){
            // 获取规则id数组,tp框架中，从前端接收一个数组，在后端接收的时候，数组字段后边要加上/a
            $rules = input('post.rules/a');
            // auth_role数据表中，存储的权限字段是一串字符串，由逗号隔开
            $rules = implode(',',$rules);
            // 数据拼接
            $data = [
              'title' => input('post.title'),
              'rules' => $rules,
              'status' => input('post.status'),
            ];

            $res = Db::name('auth_role')->insert($data);

            if ($res){
                exit(json_encode(array('code'=>1, 'msg'=>'新增成功!')));
            }else{
                exit(json_encode(array('code'=>0, 'msg'=>'新增失败！')));
            }
        }

        if (request()->isGet()){
            // 取出所有的权限列表，查询auth_rule数据表
            $rules = Db::name('auth_rule')->where('status', 1)->order('id', 'asc')->select();
            // 使用无限级菜单划分
            $data['rules'] = Rule::ruleLayer($rules);
//            dd($data);
            return view('addgroup', $data);
        }
    }

    // 删除角色
    public function delgroup()
    {
        if (request()->isPost()){
            // 删除角色，只需要在一张表中进行修改
            // 其实删除角色的话，还需要对添加了相应角色的用户进行修改，这个以后再说

            $id = intval(input('post.id'));
            $res = Db::name('auth_role')->delete($id);
            if ($res){
                exit(json_encode(array('code'=>1, 'msg'=>'删除成功！')));
            }
            exit(json_encode(array('code'=>0,'msg'=>'删除失败!')));
        }
    }

    // 编辑角色信息
    public function updategroup()
    {
        if (request()->isPost()){
            $data = request()->param();
//            dd($data);
            // 将data中的rules字段取出来，转成字符串的形式存储到数据库
            $data['rules'] = implode(',', $data['rules']);
//            dd($data);

            $res = Db::name('auth_role')->where('id',$data['id'])->update($data);
            if ($res){
                exit(json_encode(array('code'=>1, 'msg'=>'更新成功！')));
            }else{
                exit(json_encode(array('code'=>0, 'msg'=>'更新失败！')));
            }
        }


        if (request()->isGet()){
            // 获得当前编辑的角色信息
            $id = input('get.id');
            // 如果当前进行的是对超级管理员操作，那么直接返回上一级页面，并弹出提示信息
//            if ($id == 1){
//                historyTo('抱歉~你不能对超级管理员的权限进行编辑！');
//                exit;
//            }
            $info = Db::name('auth_role')->where('id',$id)->find();
            // 将获取到的rules字段转换成一个数组格式存储
            $infoRule = explode(',',$info['rules']);

            unset($info['rules']);
            $data['info'] = $info;
            $data['infoRule'] = $infoRule;
//            dd($data);
            // 取出所有的权限列表，查询auth_rule数据表
            $rules = Db::name('auth_rule')->where('status', 1)->order('id', 'asc')->select();
            // 使用无限级菜单划分
            $data['rules'] = Rule::ruleLayer($rules);
//            dd($data);

            return view('updategroup', $data);
        }
    }
}