<!DOCTYPE html>
<html>
<head>
    <title>欢迎</title>
    <link rel="stylesheet" type="text/css" href="/static/plugins/layui/css/layui.css">
    <script type="text/javascript" src="/static/plugins/layui/layui.js"></script>
    <style type="text/css">
        body{margin: 0px;}
        .header{width: 100%;height: 50px;line-height: 50px;background: #01AAED;color:#ffffff;}
        .title{margin-left: 20px;font-size: 20px;}
        .userinfo{float: right;margin-right: 10px;}
        .userinfo a{text-decoration: none;color: #ffffff;}

        .menu{width:200px;background: #333744;position: absolute;}
        .main{position: absolute;left: 200px;right: 0px;}

        .layui-collapse{border: none;}
        .layui-colla-item{border-top: none;}
        .layui-colla-title{background: #42485b;color: #ffffff;}
        .layui-colla-content{border-top: none;padding: 0px;}
    </style>
</head>
<body>
<!--header-->
<div class="header">
    <span class="title">后台管理系统</span>
    <span class="userinfo">{$admin.username}【{$role.title}】<a href="javascript:;" onclick="logout()">退出</a></span>
</div>
<!--menu-->
<div class="menu" id="menu">
    <div class="layui-collapse" lay-accordion>
        {volist name="$menus" id="vo"}
        <div class="layui-colla-item">
            <!--  一级菜单              -->
            <h2 class="layui-colla-title">{$vo.title}</h2>

            <div class="layui-colla-content">
                <?php if(isset($vo['children']) && $vo['children']){?>
                    <ul class="layui-nav layui-nav-tree">
                        <!--    二级菜单            -->
                        {volist name="vo.children" id="cvo"}
                        <li class="layui-nav-item"><a href="javascript:;" onclick="menufire(this)" src="/index.php/admins/{$cvo.controller}/{$cvo.method}">{$cvo.title}</a></li>
                        {/volist}
                    </ul>
                <?php }?>
            </div>
        </div>
        {/volist}

    </div>
</div>

<!--主操作区-->
<div class="main">
    <iframe src="/index.php/admins/home/welcome" onload="resetMainHeight(this)" style="width: 100%;height: 100%;" frameborder="0" scrolling="0"></iframe>
</div>
</body>
</html>
<script type="text/javascript">
    layui.use(['element','layer'], function(){
        var element = layui.element;
        $ = layui.jquery;
        layer = layui.layer;

        resetMenuHeight();
    });

    // 重新设置页面高度
    function resetMenuHeight(){
        var height = document.documentElement.clientHeight - 50;
        $('#menu').height(height);
    }

    // 重新设置主操作区高度
    function resetMainHeight(obj){
        var height = parent.document.documentElement.clientHeight - 53;
        $(obj).parent('div').height(height);
    }

    // 菜单点击
    // 点击管理员列表时，将a标签中的src内容提取出来，赋给iframe的src
    function menufire(obj){
        // 获取url
        const src = $(obj).attr('src');
        // 设置iframe的src
        $('iframe').attr('src',src);
    }


    // 退出登录
    function logout(){
        // 退出前确认
        layer.confirm('确定要退出吗？',{
            icon:3,
            btn:['确定','取消']
        },function(){
            $.get('/index.php/admins/account/logout',function(res){
                if(res.code>0){
                    layer.msg(res.msg,{'icon':2});
                }else{
                    layer.msg(res.msg,{'icon':1});
                    setTimeout(function(){window.location.href='/index.php/admins/account/login';},1000);
                }
            },'json');
        });
    }
</script>