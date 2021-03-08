<!DOCTYPE html>
<html>
<head>
    <title></title>
    <link rel="stylesheet" type="text/css" href="/static/plugins/layui/css/layui.css">
    <script type="text/javascript" src="/static/plugins/layui/layui.js"></script>
    <style type="text/css">
        .header span{background: #009688;margin-left: 30px;padding: 10px;color: #ffffff;}
        .header button{float: right;margin-top: -5px;}
        .header div{border-bottom: solid 2px #009688;margin-top: 8px;}
    </style>
</head>
<body style="padding: 10px;">
<div class="header">
    <span>管理员列表</span>
    <button class="layui-btn layui-btn-sm" onclick="add()">添加</button>
    <div></div>
</div>
<table class="layui-table">
    <thead>
    <tr>
        <th>ID</th>
        <th>用户名</th>
        <th>真实姓名</th>
        <th>角色</th>
        <th>状态</th>
        <th>添加时间</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    {volist name='$lists' id="vo"}
    <tr>
        <td>{$vo.id}</td>
        <td>{$vo.username}</td>
        <td>{$vo.truename}</td>
        <td>{$vo.gid}</td>
        <td>{$vo.status==0?'正常':'<span style="color: red;">禁用</span>'}</td>
        <td>{:date('Y-m-d H:i:s',$vo.add_time)}</td>
        <td>
            <button class="layui-btn layui-btn-xs" onclick="add({$vo.id})">编辑</button>
            <button class="layui-btn layui-btn-danger layui-btn-xs" onclick="del({$vo.id})">删除</button>
        </td>
    </tr>
    {/volist}
    </tbody>
</table>
</body>
</html>
<script type="text/javascript">
    layui.use(['layer'],function(){
        layer = layui.layer;
        $ = layui.jquery;
    });

    // 添加和编辑
    function add(id){
        layer.open({
            type:2,
            title:id>0?'编辑管理员':'添加管理员',
            shade:0.3,
            area:['480px','420px'],
            content:'/index.php/admins/admin/add?id='+id
        });
    }

    // 删除
    function del(id){
        layer.confirm('确定要删除吗？',{
            icon:3,
            btn:['确定','取消']
        },function(){
            $.post('/index.php/admins/admin/delete',{'id':id},function(res){
                if(res.code>0){
                    layer.alert(res.msg,{'icon':2});
                }else{
                    layer.msg(res.msg,{'icon':1});
                    setTimeout(function(){window.location.reload();},1000);
                }
            },'json');
        });
    }
</script>