<?php


namespace app\admin\controller;

use think\facade\Db;
class Article extends Common
{
    // 主页面
    public function index()
    {
        if (request()->isPost()){
            $data = request()->param();
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
                    case 1;$orderSql = ' order by a.id ' . $order_dir;break;
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
            $searchSql = " where (a.id LIKE '%" . $search . "%' or a.title LIKE '%" . $search . "%' or u.uname LIKE '%" . $search . "%') and del=0";
//            $searchSql = " where (a.id LIKE '%" . $search . "%' or a.title LIKE '%" . $search . "%')";

            // 获取数据的总行数,del==0代表当前文章没有在回收站
            $sumSql = "SELECT count(uid) as sum FROM article as a where a.del=0";
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
            $totalSql = "select a.id,u.uname,u.uid,a.create_time,a.status,a.title,a.cover,c.catid,c.catname from article as a left join cate as c on c.catid=a.catid left join users as u on u.uid=a.uid";

//            $group = " group by a.id";
            // 要在前端数据表中显示的数据
            $data = Db::query($totalSql . $searchSql . $orderSql . $limitSql);
//            dd($data);

            // 对data数据进行处理再传到前端显示
            foreach ($data as $k => $vl){
                $data[$k]['status'] = $vl['status'] == 1 ? '启用' : '禁用';
                $data[$k]['create_time'] = date('Y-m-d H:i:s', $vl['create_time']);
                // 图片地址序列化之后传入数据库,所以取出来的时候需要反序列化
                $data[$k]['cover'] = json_decode($vl['cover']);
            }

//            dd($data);
            // 给前端返回数据,json_encode()的第二个参数传入常量，避免中文编码出问题
            echo json_encode(array(
                'draw'=>$draw,
                'recordsTotal'=>$recordsTotal,
                'recordsFiltered'=>$recordsFiltered,
                'tableData'=>$data
            ),320);
        }

        if (request()->isGet()){
            return view();
        }

    }


    // 新增文章
    public function add()
    {
        if (request()->isPost()){
            // 图片以字符串字段的形式存入数据库
            // 获取图片路径
            $cover = json_encode(input('post.cover/a'));
            $data = [
                "catid" => input('post.catid'),
                "title" => input('post.title'),
                "content" => input('post.content'),
                "cover" => $cover,
                "status" => input('post.status'),
                "uid" => session()['userInfo']['uid'],
                "create_time" => time(),
            ];

            $res = Db::name('article')->insert($data);
            if ($res){
                exit(json_encode(array('code'=>1, 'msg'=>'新增成功！')));
            }else{
                exit(json_encode(array('code'=>0, 'msg'=>'新增失败！')));
            }
        }

        if (request()->isGet()){
            // 取出分类信息
            $data['info'] = Db::name('cate')->field('catname,catid')->select()->toArray();
            return view('add', $data);
        }
    }

    // 文件上传
    public function upload()
    {
        // 获取表单上传文件
        $files = request()->file();
//        dd($file);
        // 声明一个空数组，接收文件路径
        $savename = [];
        foreach ($files as $file){
            // 上传到本地服务器
            $savename[] = \think\facade\Filesystem::disk('public')->putFile( 'files', $file);
            // 将图片的路径中的反斜线替换掉

            exit(json_encode(str_replace('\\', '/', $savename)));
        }
    }

}