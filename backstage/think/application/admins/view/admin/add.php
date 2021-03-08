<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title></title>
    <link rel="stylesheet" type="text/css" href="/static/plugins/layui/css/layui.css">
    <script type="text/javascript" src="/static/plugins/layui/layui.js"></script>
</head>
<body style="padding: 10px">
    <form class="layui-form">
        <!--  将当前的id也加载进来，方便编辑和添加选项的判断      -->
        <input type="hidden" name="id" value="{$item.id}">
        <div class="layui-form-item">
            <label class="layui-form-label">用户名</label>
            <div class="layui-input-inline">
                <input class="layui-input" type="text" name="username" value="{$item.username}" <?=$item['id']>0 ? 'readonly' : '' ?>>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">角色</label>
            <div class="layui-input-inline">
                <select name="gid">
                    <option value="1">系统管理员</option>
                    <option value="2">开发人员</option>
                </select>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">密码</label>
            <div class="layui-input-inline">
                <input class="layui-input" type="password" name="password">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">姓名</label>
            <div class="layui-input-inline">
                <input class="layui-input" type="text" name="truename" value="{$item.truename}">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">状态</label>
            <div class="layui-input-inline">
                <input type="checkbox" name="status" lay-skin="primary" title="禁用" value="1" {$item.status? 'checked' : ''}>
            </div>
        </div>
    </form>
    <div class="layui-form-item">
        <div class="layui-input-block">
            <button class="layui-btn" onclick="save()">保存</button>
        </div>
    </div>
</body>
</html>

<script type="text/javascript">
    // 加载layui的弹窗组件layer
    layui.use(['layer','form'], function (){
        layer = layui.layer;
        $ = layui.jquery;
        form  = layui.form;

    })

    function save(){
        // 拿到id值，判断当前是编辑还是添加，添加的话密码就必须填写，如果是编辑的话，密码就可填可不填
        const id = parseInt($('input[name="id"]').val());
        const username = $.trim($('input[name="username"]').val());
        const password = $.trim($('input[name="password"]').val());
        const truename = $.trim($('input[name="truename"]').val());
        const gid = $.trim($('select[name="gid"]').val());

        if (username === ''){
            layer.alert('请输入用户名', {icon:2});
        }
        // 添加一个条件，判断没有正常渲染的id的值是否为空，如果为空，就是添加管理员，如果不为空，就是编辑管理员
        if (isNaN(id) && password === ''){
            layer.alert('请输入密码', {icon:2});
        }
        if (truename === ''){
            layer.alert('请输入真实姓名', {icon:2});
        }

        $.post('/index.php/admins/admin/save',$('form').serialize(), function (res){
            if (res.code>0){
                layer.alert(res.msg,{icon:2});
            }else{
                layer.msg(res.msg,{icon:1});

                setTimeout(function (){parent.window.location.reload();}, 1000);
            }
        },'json');
    }
</script>