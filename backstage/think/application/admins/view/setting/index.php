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
<body style="padding: 10px;">
    <form class="layui-form">
        <div class="layui-form-item">
            <label class="layui-form-label">网站名称</label>
            <div class="layui-input-inline">
                <input type="text" class="layui-input" name="title" value="{$item.values.title}">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">网站关键词</label>
            <div class="layui-input-block">
                <input type="text" class="layui-input" name="key" value="{$item.values.key}">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">网站描述</label>
            <div class="layui-input-block">
                <input type="text" class="layui-input" name="desc" value="{$item.values.desc}">
            </div>
        </div>
    </form>
    <div class="layui-form-item">
        <div class="layui-input-block">
            <button class="layui-btn" onclick="save()">提交</button>
        </div>
    </div>

</body>
</html>
<script type="text/javascript">
    layui.use(['layer'], function (){
        $ = layui.jquery;
        layer = layui.layer;
    });


    function save(){
        const title = $.trim($('input[name="title"]').val());
        const key = $.trim($('input[name="key"]').val());
        const desc = $.trim($('input[name="desc"]').val());

        if (title === ''){
            layer.msg('网站不能为空',{'icon':2});
            return;
        }
        // 由于setting表中只有两个字段，所以需要将表单中的信息放到一个对象中去处理
        const values = new Object;
        values.title = title;
        values.key = key;
        values.desc = desc;

        const data = new Object;
        data.names = 'site_setting';
        data.values = values;

        // 提交data
        $.post('/index.php/admins/setting/save', data, function (res){
            if (res.code>0){
                layer.msg(res.msg, {'icon':2});
            }else{
                layer.msg(res.msg, {'icon':1});
                setTimeout(function (){window.location.reload();}, 1000);
            }
        },'json');
    }
</script>