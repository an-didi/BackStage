## 后台功能及演示

我们知道一款成熟的后台管理系统的功能非常复杂，这些复杂的功能一般都是在一些通用的基础功能上建立起来的。这些基础功能一般可以称为“通用后台管理系统”。

- 基本功能展示
    - 登录功能
    - 主页面
        - 管理员管理
          - 管理员列表（添加，编辑，删除）
        - 权限管理
          - 菜单管理（子菜单，返回上级菜单）
          - 角色管理（编辑，删除）
        - 系统设置
    - 退出登录
    
## 后端使用thinkphp框架开发，前端使用css,js以及第三方组件layui组件

项目创建在tp6的public上，导入的第三方组件放在/backstage/think/public/static/plugins目录下

## 登陆页面

- 在`E:\Visual Studio Code\backstage\think\app\admins\controller`目录下创建登录控制器Account.php
- 在`E:\Visual Studio Code\backstage\think\app\admins\view`创建login.php视图
- 在config目录下的view.php中修改模板后缀为“.php”


## 管理员表设计

```sql
CREATE TABLE `admins` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL COMMENT '用户名',
  `password` varchar(32) NOT NULL COMMENT '密码',
  `gid` int(10) NOT NULL COMMENT '角色id',
  `truename` varchar(20) NOT NULL COMMENT '管理员真实名称',
  `status` tinyint(1) NOT NULL COMMENT '状态：0正常，1禁用',
  `add_time` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

```
### 管理员表的作用

1. 存储管理员信息

2. 满足管理员登陆

3. 可以分配对应的权限

### 管理员表的设计原则

1. 表字段命名清晰，见名知意

2. 数据尽量无冗余

3. 密码非明码


## 数据库访问类的封装

### 封装的目的

1. 自定义方法返回类型

2. 屏弊多余的数据库访问方法

3. 支持链式调用

/extend/Util/SysDb.php


## 防止非法用户访问

达到的要求

- 未登录的用户不允许进入非登录界面

- 未登录的非法请求强行跳转到登陆界面

- 判断是否登录的依据：session

- 针对未登录的用户访问，处理的方式是强制重定向到登录页面


## 菜单及主操作页面

- 使用layui的菜单组件
- 菜单深度（2级）
- 菜单容器高度自适应
- iframe加载主操作页面


## 管理员列表

- layui表格组件

- 管理员数据读取

- 管理员角色处理

- 数据渲染

view/admin/index.php

### 管理员添加

admin/add.php

### 编辑、删除管理员

- 编辑管理员

因为编辑管理员和添加管理员是相同的数据信息，所以不需要重新写一个视图，只需要在相同的视图上进行约束
添加管理员信息时，id的值是undefined，而编辑管理员信息的时候，id的值肯定是大于0的，所以只需要对id进行判断
就可以决定是编辑页面还是添加页面，同一个页面进行两种展示，如果是添加页面，则查询不到信息，无法渲染到页面
，而如果是编辑页面，则可以查询到页面信息，然后对页面进行数据渲染。
而两者都需要将信息添加或者修改到数据库，所以要在save方法中进行id判断，然后再决定怎么修改


- 删除管理员信息

删除管理员信息时，需要给删除按钮添加一个删除事件，将当前id拿到，然后post到后端的Admin中的delete方法中进行数据库操作


## 创建菜单表

菜单表一般是要求要菜单支持无限分类，1. 支持无限级子菜单；2. 权限控制要求精确到控制器的方法

```sql
create table `admins_menus` (
    mid int(10) unsigned auto_increment not null primary key,
    pid int(10) unsigned not null comment '上级菜单',
    oid int(10) unsigned not null default '0' comment '菜单排序',
    title varchar(30) not null comment '菜单名称',
    controller varchar(30) not null comment '控制器名称',
    method varchar(30) not null comment '控制器方法',
    ishidden tinyint(1) not null default '0' comment '是否隐藏：0显示，1隐藏，默认显示',
    status tinyint(1) not null default '0' comment '状态：0正常，1禁用，默认0'
)engine = myisam auto_increment = 1 collate = utf8mb4_unicode_ci;
```

通过添加字段pid（上级菜单id），可以将菜单与菜单之间的逻辑关系确定下来，这样我们可以方便的根据pid字段找到每个菜单的上级菜单和下级菜单

## 菜单列表以及菜单添加

菜单的列表显示和管理员表相似，可以直接使用管理员的index.php进行修改得到menu的index.php，然后为菜单的添加增加功能

### 子菜单

要完成的功能

1. 点击列表中的菜单跳转到子菜单
2. 子菜单点击返回按钮回到上一级菜单
3. 无限级子菜单

通过查找每个表中的pid来获取到子菜单的信息，查询mid获得当前菜单信息

## 角色表的创建

管理员的角色直接决定其拥有哪些权限，创建一个角色表，然后添加权限字段

在管理员表中有一个gid字段，这个字段就是角色表的主键id，也就是说，他俩的关联字段是gid
角色拥有的权限菜单以json保存菜单的id

- 创建角色表

```sql
create table `admins_group`(
    gid int(10) unsigned not null auto_increment primary key,
    name varchar(20) not null comment '角色名称',
    rights text not null comment '菜单的mid，以json的方式存储'
)engine = myisam auto_increment = 1 collate = utf8mb4_unicode_ci;
```

角色拥有的权限可以放到一张单独的表中存储，也可以建立一个字段存储。由于角色所拥有的权限是动态变化的，所以字段的数据类型使用text类型

角色列表是使用layui表格组件来进行渲染的，然后从数据库中取出数据在将其渲染到页面

## 角色添加

为了方便角色添加权限菜单，我们需要将系统中所有可用菜单友好的列出来供用户勾选，将菜单显示为二级从体验上来说是个不错的选择。

由于菜单是无限级菜单菜单的级数不确定，所有就有了需要用递归的方法将菜单改造成二级结构，将无限级菜单向二级菜单转换（方便用户选择）
所有的菜单信息都存在于菜单表中，但是通过返回的数据可以看到的是它是一个没有任何联系的一维数组，我们需要想办法将它变成一个我们想要的数据

1. 先将菜单按照等级划分出来，最终结果可能是一个多维数组，数组的深度取决于菜单的深度
2. 递归将第一步中的到的多维数组处理成一个可用的二维数组

使用递归应该重点关注的问题：需要注意出口条件的设置,不然会陷入死循环

角色的编辑最困难的点是渲染上去的是一个数组，要对选项卡的选中进行判断，使用in_array()函数可以做到

## 角色的权限访问限制

对于用户登录系统之后的权限分配问题，还有当前请求的控制器和方法是怎样获取的，以及怎样知道用户是否有权限访问当前的请求控制的方法

首先在Base控制器中通过当前用户的gid字段查询到admins_group数据表中的rights权限字段，然后判断当前是否有权限
如果有权限的话，将rights字段中的json数据接收并编译过来，然后通过当前访问菜单的request()方法中的controller()方法和action()方法获得当前
的控制器名称controller和方法名称method；通过查询条件controller和方法method来筛选出符合的菜单，然后判断当前的到的菜单中的status和mid来对权限进行控制

接着要在Home控制器中进行动态的菜单加载设置，首先通过当前用户的gid来获取用户当前拥有的权限rights数据，如果当前用户有权限，就将数据编译并接收，否则就设置为null，
然后在通过rights权限数据查询到所有的符合条件的菜单信息，然后将这个菜单信息变成一个多维数组，这个数组是一个父子结构的菜单信息，最后将menus信息和role信息渲染到
视图中，在home下的index视图中通过menu信息动态的生成一级二级菜单。

## setting表设计

setting表中存储系统的设置信息，如微信支付配置信息、支付宝配置信息等

- 要求

1. 满足系统所有设置数据的存储,字段不能太多
2. 模仿redis的key字段和value字段的方式存储各类型数据

```sql
create table `setting`(
names varchar(255) not null primary key comment '设置的名称',
values text comment '设置的值,值为json类型，长度不确定'
) engine = myisam collate = utf8mb4_unicode_ci;
```
