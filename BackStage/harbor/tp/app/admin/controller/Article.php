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

    // 文章编辑
    public function update()
    {
        if (request()->isPost()){
            // 获取post过来的数据,其中有catid,title, content,file,cover[],status,id
            $data = request()->param();
            unset($data['file']);
            $id = $data['id'];
            unset($data['id']);
            // 将旧图片地址json_decode()，新图片地址json_encode();
            $data['cover'] = json_encode($data['cover']);
            // 判断新传进来的图片地址是否为空，如果为空就不需要对图片地址进行更新，如果不为空，就需要删除原来的图片
            if (!$data['cover']){
                unset($data['cover']);
            }else{
                // cover[]是前端接收到的新的图片地址，想要修改的话，有必要将原来的图片删掉，所以需要获取原来的地址，然后通过文件删除操作删除原图片
                $cover = Db::name('article')->where('id',$id)->field('cover')->find()['cover'];
                $cover = json_decode($cover);
                foreach ($cover as $c){
                    // 拼接完整的图片地址
                    $file = $_SERVER['DOCUMENT_ROOT'] . "/storage/" . $c;
//                    dd($file);
                    // 判断是否是一个合法路径
                    if (file_exists($file)){
                        // 删除原图片
                        unlink($file);
                    }
                }
            }

//            dd($data);
            // 更新数据表
            $res = Db::name('article')->where('id', $id)->update($data);
            if ($res){
                exit(json_encode(array('code'=>1, 'msg'=>"更新成功！")));
            }else{
                exit(json_encode(array('code'=>0, 'msg'=>"更新失败！")));
            }


        }

        if (request()->isGet()){
            // 获取要编辑的文章的id
            $id = input('get.id');
            // 获取文章的相关信息
            $data['cate'] = Db::name('cate')->field('catid,catname')->select()->toArray();
            $data['info'] = Db::name('article')->alias('a')->leftJoin('cate c','a.catid=c.catid')->field('a.id,a.title,a.content,a.status,a.cover,a.catid,c.catname')->where('id',$id)->find();
            $data['info']['cover'] = json_decode($data['info']['cover']);
//            dd($data);
            return view('update', $data);
        }
    }

    // 移入回收站或者还原到文章列表
    public function recycle()
    {
        $id = input('post.id');
        $type = input('post.type');
        // 将article数据表中的del字段变为type的值
        $res = Db::name('article')->where('id', $id)->update(['del'=>$type]);
        if ($res){
            exit(json_encode(array('code'=>1, 'msg'=>'移入回收站成功！')));
        }else{
            exit(json_encode(array('code'=>0, 'msg'=>'移入回收站失败！')));
        }
    }

    // 回收站和文章列表唯一的区别就是查询语句的不同，其他都相同
    public function recycleBin()
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
            $searchSql = " where (a.id LIKE '%" . $search . "%' or a.title LIKE '%" . $search . "%' or u.uname LIKE '%" . $search . "%') and del=1";
//            $searchSql = " where (a.id LIKE '%" . $search . "%' or a.title LIKE '%" . $search . "%')";

            // 获取数据的总行数,del==1代表当前文章在回收站
            $sumSql = "SELECT count(uid) as sum FROM article as a where a.del=1";
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
            return view('recycleBin');
        }
    }

    // 批量回收
    public function bulkRecycle()
    {
        $id = input('post.ids/a');
        if (empty($id)){
            historyTo('请勾选需要移入回收站的文章');
            exit;
        }

        // 数组转字符串
        $ids = implode(',', $id);
        $sql = "update article set del='1' where id in ($ids)";

        $res = Db::execute($sql);
        if ($res){
            jumpTo('/article/index');
            exit;
        }else{
            historyTo('操作失败');
            exit;
        }
    }

    // 批量还原
    public function revertAll()
    {
        $id = input('post.ids/a');
        if (empty($id)){
            historyTo('请勾选需要移入回收站的文章');
            exit;
        }

        // 数组转字符串
        $ids = implode(',', $id);
        $sql = "update article set del='0' where id in ($ids)";

        $res = Db::execute($sql);
        if ($res){
            jumpTo('/article/recycleBin');
            exit;
        }else{
            historyTo('操作失败');
            exit;
        }
    }

    // 删除文章
    public function del()
    {
        // 删除文章时，本地服务器上的图片也需要删除
        $id = input('post.id');
        $cover = Db::name('article')->where('id', $id)->field('cover')->find()['cover'];
        $cover = json_decode($cover);
        if (empty($cover)){
            foreach ($cover as $c){
                // 拼接完整的图片地址
                $file = $_SERVER['DOCUMENT_ROOT'] . "/storage/" . $c;
//                    dd($file);
                // 判断是否是一个合法路径
                if (file_exists($file)){
                    // 删除原图片
                    unlink($file);
                }
            }
        }
        $res = Db::name('article')->delete($id);
        if ($res){
            exit(json_encode(array('code'=>1, 'msg'=>'删除成功！')));
        }else{
            exit(json_encode(array('code'=>0, 'msg'=>'删除失败！')));
        }
    }

}