# 基于ThinkPhp6.0版本的一个后台管理系统

## 安装thinkphp
如果配置好了composer的话，直接在项目文件终端直接命令安装`composer create-project topthink/think tp`
如果没有安装composer的话，建议跟着th6的手册先进行安装composer，[tp6手册](https://www.kancloud.cn/manual/thinkphp6_0/1037481)

## 配置多应用模式

th6安装完成后，默认状态就是单应用模式，如果要修改成多应用模式，需要对文件结构进行修改
1. 删掉app目录下的controller目录
2. 安装多应用模式扩展think-multi-app,`composer require topthink/think-multi-app`
3. 创建多应用目录（在根目录下执行），使用命令`php think build 文件名`,这个生成的就是在app目录下的一个多应用的目录，以本示例为例，admin是后台管理
，index是前台显示
4. 使用命令来创建控制器文件，`php think make:controller admin@Login` ,创建成功后会生成控制器Login.php类文件，其中命名空间都已经写好。

## 设置隐藏入口文件（Apache重写规则）

URL重写来隐藏入口文件
可以通过URL重写隐藏应用的入口文件index.php（也可以是其它的入口文件，但URL重写通常只能设置一个入口文件）,下面是相关服务器的配置参考：
- Apache
1. httpd.conf配置文件中加载了mod_rewrite.so模块
2. AllowOverride None 将None改为 All
3. 把下面的内容保存为.htaccess文件放到应用入口文件的同级目录下
```
<IfModule mod_rewrite.c>
  Options +FollowSymlinks -Multiviews
  RewriteEngine On

  RewriteBase /
  RewriteRule ^indx.php$ - [L]
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteRule . /index.php [L]
</IfModule>
```

（将config目录和route目录复制一份，然后放到admin下）


## tp6框架基础-请求对象request刨析

dump()封装了var_dump(), dd()封装了var_dump()和die;

- 依赖注入
  1. 构造方法注入
  2. 操作方法注入
     
- 静态调用请求对象

使用门面技术接管Request，然后直接静态调用, use think\facade\Request;
使用门面技术时，就不能再同时导入原来的think\Request了


## tp6中好用的助手函数

- request()
使用前需要引入Request类，use think\Request;
  
- input()
input()可以直接获取到get或者post的内容，不需要引入

- view()
使用视图引擎的话需要安装组件,`composer require topthink/think-view`
view()传参，view('index')，跳转到index视图页面，view('index', $data),向视图页面传入参数data，data是个数组
  
- json()
json()的作用相当于原生的json_encode()的作用

## 使用jquery.validate.min.js框架进行表单验证


## 验证码think-captcha扩展包安装，并且进行验证码校验

验证码需要放到session中储存，要开启session服务

给admin单独开启session服务：找到文件下的middleware.php

```php
// 全局中间件定义文件
return [
    // 全局请求缓存
    // \think\middleware\CheckRequestCache::class,
    // 多语言加载
    // \think\middleware\LoadLangPack::class,
    // Session初始化
     \think\middleware\SessionInit::class
];
```

## rcba表设计

>RBAC (Role-Based Access Control, 基于角色的访问控制)，就是用户通过角色与权限进行关联，简单地说，一个用户拥有若干角色，每一个角色拥有若干权限，这样，就构成了 ”用户-角色-权限“的授权模型。在这种模型中，用户与角色之间，角色与权限之间，一般这多对多的关系。

|数据表名|描述|表字段解释|表设计|
|---|---|---|---|
|users|用户名|后台管理员集合|uid,uname,pwd,status,create_time,login_ip|
|auth_role|角色表|所有角色的集合，如超级管理员，普通管理员，新闻编辑，控评管理员|id,title,status,rules|
|auth_rule|权限表|所有操作页面路由集合，如系统设置，文章管理，添加新成员，分类管理，评论管理，评论删除，文章新增，文章编辑|id,name,title,pid,is_menu,status|
|users_role|用户-角色关联表|该关联表只有两个字段，并且无主键，只是起到表关联的作用|uid,role_id|

## 在数据表显示的页面，使用了dataTables框架

可以在dataTables中文网查看手册，从而进行配置。[dataTables中文网]("http://datatables.club/")


## 文章管理所应用到的几个框架

> Web Uploader (图片上传)

[Web Uploader 手册](http://fex.baidu.com/webuploader/)

> UEditor (富文本编辑器)

[UEditor 手册](http://fex.baidu.com/ueditor/)



