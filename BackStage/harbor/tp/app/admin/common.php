<?php
// 这是系统自动生成的公共文件

// 跳转公共函数
function jumpTo($url)
{
    $script = "<script type='text/javascript'>";
    $script .= "location.href='" . $url . "'";
    $script .= "</script>";
    echo $script;
}

if (!function_exists('delete_dir_file'))
{
    function delete_dir_file($dir)
    {
        // 声明一个初始状态，默认情况下缓存未被删除
        $res = false;
        // 检验一个目录是否真实
        if (is_dir($dir)){
            // 成功打开目录流，返回值是一个resource类型数据，如果不成功，返回false
            if ($handle = opendir($dir)){
                while (($file = readdir($handle)) != false){
//                    echo "filename: " . $file . "<br>";
                    /*
                     * filename: .    代表当前访问目录存在同级目录
                     *  filename: ..  代表存在上级目录
                     *  filename: log 子目录
                     *  filename: session 子目录
                     *  filename: temp 子目录
                     */
                    if ($file !== '.' && $file !== '..'){
                        // 判断是否是一个目录
                        if (is_dir($dir . '\\' . $file)){
                            // 拼接目录
                            // 目录只有为空的情况下才能删除
                            delete_dir_file($dir . '\\' . $file);
                        }else{
                            // 不是目录的情况,直接删除
                            // unlink只能删除一个文件
                            unlink($dir . '\\' . $file);
                        }
                    }
                }
            }
            // 关闭目录句柄
            closedir($handle);
            // 目录为空时删除目录
            if ($dir !== "E:\\Visual Studio Code\\harbor\\tp\\runtime\\admin\\session\\"){
               if (rmdir($dir)){
                   $res = true;
               }
            }
        }
        return $res;
    }
}

/**
 * 返回上一步
 * @param $parma: 提示信息
 */
function historyTo($parma)
{
    echo '<script>alert("' . $parma . '"); history.back();</script>';

}