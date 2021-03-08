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
<input type="hidden" id="pid" value="{$pid}">
<div class="header">
    <span>菜单列表</span>
    <button class="layui-btn layui-btn-sm" onclick="add()">添加</button>
    <div></div>
</div>

<?php if(isset($pid)) : ?>
<?php if ($pid>0) :?>
<!--    backid是pid的上级pid-->
<button class="layui-btn layui-btn-primary layui-btn-sm" style="float: right; margin-top: 10px; margin-bottom: 10px" onclick="backs({$backid})">返回上级菜单</button>
<?php endif ?>
<?php endif ?>
<table class="layui-table">
    <thead>
    <tr>
        <th>菜单ID</th>
        <th>排序</th>
        <th>菜单名称</th>
        <th>控制器</th>
        <th>方法</th>
        <th>是否隐藏</th>
        <th>状态</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    {volist name='$lists' id="vo"}
    <tr>
        <td>{$vo.mid}</td>
        <td>{$vo.ord}</td>
        <td>{$vo.title}</td>
        <td>{$vo.controller}</td>
        <td>{$vo.method}</td>
        <td>{$vo.ishidden==1? '<span style="color: red;">隐藏</span>':'显示'}</td>
        <td>{$vo.status==0?'正常':'<span style="color: red;">禁用</span>'}</td>
        <td>
            <button class="layui-btn layui-btn-xs" onclick="childs({$vo.mid})">子菜单</button>
            <button class="layui-btn layui-btn-warm layui-btn-xs" onclick="add({$vo.mid})">编辑</button>
            <button class="layui-btn layui-btn-danger layui-btn-xs" onclick="del({$vo.mid})">删除</button>
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
    function add(mid){
        // 获取pid，判断是否是当前菜单
        const pid = $('#pid').val();
        layer.open({
            type:2,
            title:mid>0?'编辑菜单':'添加菜单',
            shade:0.3,
            area:['480px','420px'],
            content:'/index.php/admins/menu/add?mid='+mid+'&pid='+pid
        });
    }

    // 删除
    function del(mid){
        layer.confirm('确定要删除吗？',{
            icon:3,
            btn:['确定','取消']
        },function(){
            $.post('/index.php/admins/menu/delete',{'mid':mid},function(res){
                if(res.code>0){
                    layer.alert(res.msg,{'icon':2});
                }else{
                    layer.msg(res.msg,{'icon':1});
                    setTimeout(function(){window.location.reload();},1000);
                }
            },'json');
        });
    }

    // 返回上级菜单
    function backs(pid){
        window.location.href = '?pid=' + pid;
    }

    // 点击事件，实现一个页面跳转
    function childs(mid){
        // 通过get方法传参，获得当前要跳转的页面的位置
      window.location.href = '?pid=' + mid;
    }
</script>