<?php
// 验证码的配置文件
return [
    // 验证码的字符集
    'codeSet' => '123456789abcdefghijklmnopqretuvwxyz',
    // 验证码的字体大小
    'fontSize' => 18,
    // 是否添加混淆曲线
    'useCurve' => false,
    // 设置验证码图片的高度和宽度
    'imageW' => 150,
    'imageH' => 35,
    // 验证码的位数
    'length' => 4,
    // 验证码验证成功后是否重置它
    'reset' => true
];