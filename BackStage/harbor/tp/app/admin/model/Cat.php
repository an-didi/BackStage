<?php


namespace app\admin\model;

use think\Model;
class Cat extends Model
{
    // 模型,操作数据库,指定数据表
    protected $name = 'cate';
    public function Cate($data): array
    {
        $draw = $data['draw'];
        // order参数，从那一列开始进行排序
        $order_column = $data['order'][0]['column'];
        // 升序还是降序
        $order_dir = $data['order'][0]['dir'];

        // 拼接order sql语句
        if (isset($order_column)){
            // 根据前端显示的规则表可以知道，column==0的时候是根据id排序的
            $column = intval($order_column);
            switch ($column){
                case 0;$order_column = 'catid';break;
                default;$orderSql = '';
            }
        }

        // 分页参数,limit(start,length)
        // 拼接分页的sql语句，接收分页参数，然后对limit sql语句进行拼接
        $start = isset($data['start']) ? intval($data['start']) : null;
        $length = $data['length'] != -1 ? intval($data['length']) : null;

        // 获取数据的总行数
        $total = Cat::select();
        $recordsTotal = count($total);



        // 模糊查询的搜索条件参数
        $search = $data['search']['value'];
        if (strlen($search) > 0){
            $recordsFilteredResult = Cat::where('catid|catname','like', '%' . $search . '%')->select()->toArray();
            $recordsFiltered = count($recordsFilteredResult);
        }else{
            // 没有搜索条件的时候
            $recordsFilteredResult = Cat::field('catid,catname,status,create_time')->order($order_column, $order_dir)->limit($start,$length)->select()->toArray();
            $recordsFiltered = $recordsTotal;
        }


        $res = [
            'draw'=>$draw,
            'recordsTotal'=>$recordsTotal,
            'recordsFiltered'=>$recordsFiltered,
            'tableData'=>$recordsFilteredResult
        ];

        return $res;

    }
}