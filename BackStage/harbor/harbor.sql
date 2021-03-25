/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50726
Source Host           : 127.0.0.1:3306
Source Database       : harbor

Target Server Type    : MYSQL
Target Server Version : 50726
File Encoding         : 65001

Date: 2021-03-25 08:04:11
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for article
-- ----------------------------
DROP TABLE IF EXISTS `article`;
CREATE TABLE `article` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL COMMENT '作者id',
  `title` varchar(255) NOT NULL,
  `cover` varchar(1000) NOT NULL COMMENT '文件保存路径',
  `content` text NOT NULL,
  `status` tinyint(11) unsigned NOT NULL DEFAULT '1',
  `catid` int(11) unsigned NOT NULL,
  `del` tinyint(11) unsigned NOT NULL DEFAULT '0' COMMENT '0未删除 1回收站',
  `hit` int(11) unsigned DEFAULT '0' COMMENT '点击量',
  `link` varchar(225) DEFAULT NULL,
  `create_time` int(11) unsigned NOT NULL,
  `comment_count` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of article
-- ----------------------------
INSERT INTO `article` VALUES ('1', '1', '测试', '[\"files\\/20210323\\/20d8cab128467fd54a00c5950acfd778.jpg\"]', '<p>这是一个测试</p>', '1', '1', '0', '0', null, '1616404850', '0');
INSERT INTO `article` VALUES ('2', '1', '第一年第二季度状态', '[\"files\\/20210323\\/92a51368428b4cd424c1e9dc5a97711f.jpg\"]', '<p>第一年度第二季度状态</p>', '1', '3', '0', '0', null, '1616465710', '0');

-- ----------------------------
-- Table structure for auth_role
-- ----------------------------
DROP TABLE IF EXISTS `auth_role`;
CREATE TABLE `auth_role` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` char(60) NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `rules` longtext NOT NULL COMMENT '角色所拥有的权限',
  PRIMARY KEY (`id`),
  KEY `title` (`title`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of auth_role
-- ----------------------------
INSERT INTO `auth_role` VALUES ('1', '超级管理员', '1', '1,2,3,4,117,128,5,6,7,8,9,10,11,12,15,16,17,18,19,13,20,21,22,23,24,14,25,26,27,28,29,30,31,32,33,34,35,37,38,39,116,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,89,90,92,93,94,95,96,97,98,99,100,101,102,104,25,105,106,107,109,110,118,119,120,121,122,123,124,125,126,127');
INSERT INTO `auth_role` VALUES ('2', '普通管理员', '1', '1,2,3,4,117,5,6,7,8,9,10,11,13,20,14,25,28,29,30,33,34,35,37,38,39,116,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,89,90,92,93,94,95,96,97,98,99,100,101,102,104,105,106,107,109,110,118,119,120,121,122,123,124,125,126,127');
INSERT INTO `auth_role` VALUES ('3', '控评管理员', '1', '33,34,40,41,77,78,79,80,81,106,107,109,110');
INSERT INTO `auth_role` VALUES ('4', '文章编辑', '1', '82,83,84,85,86,87,89,90,92,93,94,95,96,97,98,99,100,101,102,103,104,105,106,107,109,110');
INSERT INTO `auth_role` VALUES ('5', '图片管理员', '1', '1,2,3,4,117,128,5,6,7,8,9,10,118,119,120,121,122');

-- ----------------------------
-- Table structure for auth_rule
-- ----------------------------
DROP TABLE IF EXISTS `auth_rule`;
CREATE TABLE `auth_rule` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL DEFAULT '',
  `title` varchar(20) NOT NULL DEFAULT '',
  `pid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '父级分类id',
  `is_menu` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否被选为菜单展示,1是0否',
  `condition` char(100) DEFAULT NULL COMMENT '规则表达式，为空表示存在就验证，不为空表示按照条件验证',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '菜单类型',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '菜单状态',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `name` (`name`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=131 DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED;

-- ----------------------------
-- Records of auth_rule
-- ----------------------------
INSERT INTO `auth_rule` VALUES ('1', 'Index/menu', '后端管理', '0', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('2', 'Index/index', '菜单管理', '1', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('3', 'Index/welcome', '欢迎页', '1', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('4', 'Index/logout', '退出', '1', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('5', 'Setting/menu', '系统设置', '0', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('6', 'Setting/index', '列表', '5', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('7', 'Setting/add', '新增', '5', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('8', 'Setting/update', '编辑', '5', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('9', 'Setting/del', '删除', '5', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('10', 'Setting/upload', '文件上传', '5', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('11', 'Auth/menu', '权限管理', '0', '1', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('12', 'Auth/index', '用户管理', '11', '1', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('13', 'Auth/rule', '规则管理', '11', '1', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('14', 'Auth/group', '角色管理', '11', '1', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('15', 'Auth/add', '新增', '12', '1', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('16', 'Auth/update', '编辑', '12', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('17', 'Auth/delete', '删除', '12', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('18', 'Auth/checkuname', '用户名检测', '12', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('19', 'Auth/resetpwd', '重置密码', '12', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('20', 'Auth/addrule', '新增', '13', '1', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('21', 'Auth/updaterule', '编辑', '13', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('22', 'Auth/delrule', '删除', '13', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('23', 'Auth/scomp', '菜单状态', '13', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('24', 'Auth/stats', '修改状态', '13', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('25', 'Auth/addgroup', '新增', '14', '1', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('26', 'Auth/updategroup', '编辑', '14', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('27', 'Auth/delgroup', '删除', '14', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('28', 'Models/menu', '模型管理', '0', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('29', 'Models/index', '列表', '28', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('30', 'Models/add', '新增', '28', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('31', 'Models/update', '编辑', '28', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('32', 'Models/del', '删除', '28', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('33', 'Cate/menu', '分类管理', '0', '1', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('34', 'Cate/index', '列表', '33', '1', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('35', 'Cate/add', '新增', '33', '1', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('37', 'Cate/del', '删除', '33', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('38', 'Cate/upload', '上传', '33', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('39', 'Cate/checkcatname', '名称检测', '33', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('40', 'Article/menu', '文章管理', '0', '1', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('41', 'Article/index', '列表', '40', '1', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('42', 'Article/add', '新增', '40', '1', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('43', 'Article/update', '编辑', '40', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('44', 'Article/del', '删除', '40', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('45', 'Article/upload', '上传', '40', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('46', 'Article/checktitle', '标题重复检测', '40', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('47', 'Article/sorts', '排序', '40', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('48', 'Article/sortcomp', '排序对比', '40', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('49', 'Article/recycle', '移到回收站/还原', '40', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('50', 'Article/bulkRecycle', '批量移到回收站', '40', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('51', 'Article/revertAll', '批量还原', '40', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('52', 'Article/recycleBin', '回收站', '40', '1', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('53', 'Attr/menu', '属性管理', '0', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('54', 'Attr/index', '列表', '53', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('55', 'Attr/add', '新增', '53', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('56', 'Attr/update', '编辑', '53', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('57', 'Attr/del', '删除', '53', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('58', 'Position/menu', '广告位管理', '0', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('59', 'Position/index', '列表', '58', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('60', 'Position/add', '新增', '58', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('61', 'Position/update', '编辑', '58', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('62', 'Position/del', '删除', '58', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('63', 'Ad/menu', '广告管理', '0', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('64', 'Ad/index', '列表', '63', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('65', 'Ad/add', '新增', '63', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('66', 'Ad/update', '编辑', '63', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('67', 'Ad/del', '删除', '63', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('68', 'Ad/upload', '上传', '63', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('69', 'Ad/checkname', '标题重复检测', '63', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('70', 'Links/menu', '友链管理', '0', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('71', 'Links/index', '列表', '70', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('72', 'Links/add', '新增', '70', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('73', 'Links/update', '编辑', '70', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('74', 'Links/del', '删除', '70', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('75', 'Links/upload', '上传', '70', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('76', 'Links/checkname', '名称重复性检测', '70', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('77', 'Fsmsg/menu', '留言管理', '0', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('78', 'Fsmsg/index', '列表', '77', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('79', 'Fsmsg/add', '新增', '77', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('80', 'Fsmsg/update', '编辑', '77', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('81', 'Fsmsg/del', '删除', '77', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('82', 'Spaces/menu', '类型管理', '0', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('83', 'Spaces/index', '列表', '82', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('84', 'Spaces/add', '新增', '82', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('85', 'Spaces/update', '编辑', '82', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('86', 'Spaces/del', '删除', '82', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('87', 'Areas/menu', '面积管理', '0', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('89', 'Areas/add', '新增', '87', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('90', 'Areas/update', '编辑', '87', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('92', 'Designers/menu', '设计师管理', '0', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('93', 'Designers/index', '列表', '92', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('94', 'Designers/add', '新增', '92', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('95', 'Designers/update', '编辑', '92', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('96', 'Designers/del', '删除', '92', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('97', 'Designers/upload', '上传', '92', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('98', 'Designers/checkname', '名称检测', '92', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('99', 'Cases/menu', '案例管理', '0', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('100', 'Cases/index', '列表', '99', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('101', 'Cases/add', '新增', '99', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('102', 'Cases/update', '编辑', '99', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('114', 'Cases/delete', '删除', '99', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('104', 'Cases/upload', '上传', '99', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('105', 'Cases/checkname', '名称检测', '99', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('106', 'Comment/menu', '评论管理', '0', '1', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('107', 'Comment/index', '列表', '106', '1', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('109', 'Comment/update', '编辑', '106', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('110', 'Comment/del', '删除', '106', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('116', 'cate/update', '编辑', '33', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('117', 'Index/clearcache', '清除缓存', '1', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('118', 'Swiper/menu', '图片管理', '0', '1', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('119', 'Swiper/add', '新增', '118', '1', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('120', 'Swiper/update', '编辑', '118', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('121', 'Swiper/del', '删除', '118', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('122', 'Swiper/index', '列表', '118', '1', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('123', 'Product/menu', '产品管理', '0', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('124', 'Product/index', '列表', '123', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('125', 'Product/add', '新增', '123', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('126', 'Product/update', '编辑', '123', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('127', 'Product/del', '删除', '123', '0', null, '1', '1');
INSERT INTO `auth_rule` VALUES ('128', 'Index/languageSwitch', '语言切换', '1', '1', null, '1', '1');


-- ----------------------------
-- Table structure for cate
-- ----------------------------
DROP TABLE IF EXISTS `cate`;
CREATE TABLE `cate` (
  `catid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `catname` varchar(120) NOT NULL,
  `status` int(11) unsigned NOT NULL DEFAULT '1',
  `create_time` int(11) unsigned NOT NULL,
  PRIMARY KEY (`catid`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of cate
-- ----------------------------
INSERT INTO `cate` VALUES ('1', '最新', '1', '1609229004');
INSERT INTO `cate` VALUES ('2', '大公司', '1', '1609230202');
INSERT INTO `cate` VALUES ('3', '内容', '1', '1602497581');
INSERT INTO `cate` VALUES ('4', '消费', '1', '1602497681');
INSERT INTO `cate` VALUES ('5', '娱乐', '1', '1602497689');
INSERT INTO `cate` VALUES ('6', '区块链', '1', '1602497697');
INSERT INTO `cate` VALUES ('7', '前端', '1', '1958545454');
INSERT INTO `cate` VALUES ('8', '后端', '1', '1585412525');



-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `uid` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `uname` varchar(20) DEFAULT NULL COMMENT '用户名',
  `pwd` varchar(100) DEFAULT NULL COMMENT '密码',
  `login_ip` varchar(40) DEFAULT NULL COMMENT '用户登录的ip地址',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态：1正常，0禁用',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `u_uname` (`uname`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES ('1', 'admin', '$2y$10$5ZMAWrkirbMqM5uoT5uDjeaDYY.EA8FA0PzJd0FiOdbOImdGV2YTC', '127.0.0.1', '1', '1615536102');
INSERT INTO `users` VALUES ('2', '鲨鱼大辣椒', '$2y$10$iT.WDUT3jA/VvqEVP.Br5uNhTrTvAVmTS8Tla1/DU2K6am97E6KF.', '127.0.0.1', '1', '1616038900');
INSERT INTO `users` VALUES ('3', '金融大大亨', '$2y$10$JEUwiH55TFtjWJ8sncsIZOEIAgnrK934.8xO9lfS3yHJCyTKpz9Fu', '127.0.0.1', '1', '1615536378');
INSERT INTO `users` VALUES ('4', '娱乐一百分', '$2y$10$Z8qch3iRU35UtqTnbC8e3OIeNSegIghukkSrf0LNuV.tsJFObAIgi', '127.0.0.1', '1', '1616050147');
INSERT INTO `users` VALUES ('5', '金牌编辑员', '$2y$10$Rwsuck18t89yiobEunDh9OaaPT9GAR7okw6Uz1dDewwN.AgWHtdaa', '127.0.0.1', '1', '1616050228');
INSERT INTO `users` VALUES ('6', '纵横演艺圈', '$2y$10$8BZsHxhWIXCXBiPT.kyCwOQ7fKJXgSjt0twSl4KWXPb5vNu4BpwQ6', '127.0.0.1', '1', '1616050304');
INSERT INTO `users` VALUES ('7', '美食评论家', '$2y$10$/LKMahG8tWmbvezC/G.nHu9l.i2p8sSyRTsOh9M8MVeaKDwj/rW/S', '127.0.0.1', '1', '1616055490');
INSERT INTO `users` VALUES ('8', '跨界评议员', '$2y$10$rzNLUHgKNFf1cR.KOSHN8uF/aLAxzPwLnrVV5hH1dXNVaMR/4tbAa', '127.0.0.1', '1', '1616055613');
INSERT INTO `users` VALUES ('9', '留言分析家', '$2y$10$wA6mJCHlSm0yom7rDfiT7uc.XsDjm/Ed6WwnQLxhU26SIAEUeM.1m', '127.0.0.1', '1', '1616055672');
INSERT INTO `users` VALUES ('10', '新闻30分', '$2y$10$/abvAQHnjivpqTEoTGts2.PuD4PeAlG.jEAdwFYXf1r4dje9GMGBu', '127.0.0.1', '1', '1616055999');
INSERT INTO `users` VALUES ('11', '娱乐看天下', '$2y$10$GlmYTjch8ZOlSt0lJkRAeeiXxAc0IcVEPDm9lNKPK9c8aQyYi5BFi', '127.0.0.1', '1', '1616056732');
INSERT INTO `users` VALUES ('12', '神秘评议员', '$2y$10$eS8hyI.6epSPQQuT1dR0UuGL6JN.s1sBUBXfEOd1x2SaN.A2cq51i', '127.0.0.1', '1', '1616056325');
INSERT INTO `users` VALUES ('13', '明星八爪鱼', '$2y$10$JBdWddRXnDNXdDXJ0Bl2UuNNnfOPyoEZx/x8rpCnq4WDJZPmUD7bW', '127.0.0.1', '1', '1616056374');
INSERT INTO `users` VALUES ('14', '蒙面猜猜猜', '$2y$10$SVw1QGkeFb4JO4Efm/z02Oxz53fyYbCs7Fets4VNG8k4.mKwU/8Wy', '127.0.0.1', '1', '1616056413');
INSERT INTO `users` VALUES ('15', 'peter zhu', '$2y$10$3IgtUvGW2bEuqA1/h/.c9.sS0id0FPyr9hX7zHCex2BBUExfMdPbW', '127.0.0.1', '1', '1616056913');

-- ----------------------------
-- Table structure for users_role
-- ----------------------------
DROP TABLE IF EXISTS `users_role`;
CREATE TABLE `users_role` (
  `uid` int(11) unsigned NOT NULL,
  `role_id` int(11) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of users_role
-- ----------------------------
INSERT INTO `users_role` VALUES ('1', '1');
INSERT INTO `users_role` VALUES ('2', '2');
INSERT INTO `users_role` VALUES ('3', '4');
INSERT INTO `users_role` VALUES ('5', '3');
INSERT INTO `users_role` VALUES ('4', '4');
INSERT INTO `users_role` VALUES ('6', '2');
INSERT INTO `users_role` VALUES ('7', '4');
INSERT INTO `users_role` VALUES ('10', '4');
INSERT INTO `users_role` VALUES ('11', '2');
INSERT INTO `users_role` VALUES ('12', '3');
INSERT INTO `users_role` VALUES ('13', '3');
INSERT INTO `users_role` VALUES ('14', '3');
INSERT INTO `users_role` VALUES ('15', '2');
INSERT INTO `users_role` VALUES ('8', '2');
INSERT INTO `users_role` VALUES ('9', '3');
