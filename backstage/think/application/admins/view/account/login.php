<!DOCTYPE html>
<html>
<head>
    <title>后台登录</title>
    <link rel="stylesheet" type="text/css" href="/static/plugins/layui/css/layui.css">
    <script type="text/javascript" src="/static/plugins/layui/layui.js"></script>
    <style type="text/css">
        body{background: #0094ff;}
    </style>
</head>
<body>
<div style="position: absolute;left: 50%;top: 50%;width: 500px;margin-left: -250px;margin-top: -200px;">
    <div style="background: #ffffff;padding: 20px;border-radius: 4px;box-shadow:5px 5px 20px #444444;">
        <div class="layui-form">
            <div class="layui-form-item" style="color: gray;">
                <h2>后台管理系统</h2>
            </div>
            <hr>
            <div class="layui-form-item">
                <label class="layui-form-label">用户名</label>
                <div class="layui-input-block">
                    <input type="text" class="layui-input" id="username">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">密&nbsp;&nbsp;&nbsp;&nbsp;码</label>
                <div class="layui-input-block">
                    <input type="password" class="layui-input" id="password">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">验证码</label>
                <div class="layui-input-inline">
                    <input type="text" class="layui-input" id="verifycode">
                </div>
                <img src="{:captcha_src()}" id="img" onclick="reloadImg()">
            </div>
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button class="layui-btn" onclick="dologin()">登录</button>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
<script type="text/javascript">
    layui.use('layer',function(){
        layer = layui.layer;
        $ = layui.jquery;

        // 获取焦点
        $('#username').focus();

        // 直接回车登录
        $('input').keydown(function(e){
            if(e.keyCode == 13){
                dologin();
            }
        });
    });

    // 重新加载验证码
    function reloadImg(){
        $('#img').attr('src','{:captcha_src()}?rand='+Math.random());
    }


    function dologin(){
        const username = $.trim($('#username').val());
        const password = $('#password').val();
        const verifycode = $('#verifycode').val();
        if(username==''){
            layer.alert('请输入用户名',{'icon':2});
            return;
        }
        if(password == ''){
            layer.alert('密码不能为空',{'icon':2});
            return;
        }
        if(verifycode == ''){
            layer.alert('验证码不能为空',{'icon':2});
            return;
        }
        $.post('/index.php/admins/account/dologin',{'username':username,'password':password,'verifycode':verifycode},function(res){
            if(res.code>0){
                reloadImg();
                layer.alert(res.msg,{'icon':2});
            }else{
                layer.msg(res.msg);
                setTimeout(function(){window.location.href='/index.php/admins/home/index'},1000);
            }
        },'json');
    }
</script>