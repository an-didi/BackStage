<!DOCTYPE html>
<html>
<head>
    <title></title>
    <link rel="stylesheet" type="text/css" href="/static/plugins/layui/css/layui.css">
    <script type="text/javascript" src="/static/plugins/layui/layui.js"></script>
    <style type="text/css">
        .header span{
            background: #009688;
            margin-left: 30px;
            padding: 10px;
            color: #ffffff;
        }
        .header button{
            float: right;
            margin-top: -5px;
        }

        .header div{
            border-bottom: solid 2px #009688;
            margin-top: 8px;
        }
    </style>
</head>
<body style="padding: 10px;">
<div class="header">
    <span>角色列表</span>
    <button class="layui-btn layui-btn-sm" onclick="add()">添加</button>
    <div></div>
</div>

<table class="layui-table">
    <thead>
    <tr>
        <th>角色ID</th>
        <th>角色名称</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    {volist name='$lists' id="vo"}
    <tr>
        <td>{$vo.gid}</td>
        <td>{$vo.title}</td>
        <td>
            <button class="layui-btn layui-btn-warm layui-btn-xs" onclick="add({$vo.gid})">编辑</button>
            <button class="layui-btn layui-btn-danger layui-btn-xs" onclick="del({$vo.gid})">删除</button>
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
    function add(gid){
        const pid = $('#pid').val();
        layer.open({
            type:2,
            title:gid>0?'编辑角色':'添加角色',
            shade:0.3,
            area:['580px','520px'],
            content:'/index.php/admins/roles/add?gid='+gid
        });
    }

    // 删除
    function del(gid){
        layer.confirm('确定要删除吗？',{
            icon:3,
            btn:['确定','取消']
        },function(){
            $.post('/index.php/admins/roles/delete',{'gid':gid},function(res){
                if(res.code>0){
                    layer.alert(res.msg,{'icon':2});
                }else{
                    layer.msg(res.msg,{'icon':1});
                    // 调用方法成功后，在一秒后刷新页面
                    setTimeout(function(){window.location.reload();},1000);
                }
            },'json');
        });
    }

</script>
