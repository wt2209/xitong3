<?php
if (!defined("HDPHP_PATH"))exit('No direct script access allowed');
return array(
    /********************************基本参数********************************/
    'AUTO_LOAD_FILE'                => array(),     //自动加载文件
    /********************************数据库********************************/
    'DB_DRIVER'                     => 'mysqli',    //驱动
    'DB_CHARSET'                    => 'utf8',      //字符集
    'DB_HOST'                       => '127.0.0.1', //主机
    'DB_PORT'                       => 3306,        //端口
    'DB_USER'                       => 'root',      //帐号
    'DB_PASSWORD'                   => 'root',          //密码
    'DB_DATABASE'                   => 'bsic3_new',          //数据库
    'DB_PREFIX'                     => 'bh_',          //表前缀
    /********************************模板参数********************************/
    'TPL_PATH'                      => 'View',      //目录
    'TPL_FIX'                       => '.html',     //文件扩展名
    'TPL_TAGS'                      => array(),     //标签类
    /********************************URL路由********************************/
    'ROUTE'                         => array(),     //路由配置

    'FILTER_FUNCTION'               => array('strip_tags','htmlspecialchars'),
    'BACKUP_DIR'=>'backup',


    //自动加载
    'AUTO_LOAD_FILE' => array('hdphp/Extend/Org/PhpRbac/src/PhpRbac/Rbac.php'),

    //单身水电费类型
    'SINGLE_SD_TYPE'=>array(
        //水费及电费的类型（key），及他们对应的键值
        1   => array('title'=>'水费','name'=>'water'),
        2   => array('title'=>'电费','name'=>'electric')
    ),
    //单身床位费及押金
    'SINGLE_RENT_TYPE'=>array(
        //租金及床位费的类型（key），及他们对应的键值
        1   => array('title'=>'押金','name'=>'pledge'),
        2   => array('title'=>'租金','name'=>'rental')
    ),
    //  租赁费用类型
    // 1为押金，2为租金，3为物业费，4为水费，5为电费，6为取暖费，7为燃气费，8为电梯费
    'RENT_CHARGE_TYPE'              => array(
        1   => array('title'=>'押金', 'needQuarter'=>false), //是否需要用季度标注
        2   => array('title'=>'租金', 'needQuarter'=>true),
        3   => array('title'=>'物业费', 'needQuarter'=>true),
        4   => array('title'=>'水费', 'needQuarter'=>true),
        5   => array('title'=>'电费', 'needQuarter'=>true),
        6   => array('title'=>'取暖费', 'needQuarter'=>true),
        8   => array('title'=>'电梯费', 'needQuarter'=>true),
        7   => array('title'=>'燃气费', 'needQuarter'=>true),
    ),

    //水电费单价

    'WATER_PRICE'=>3.35,//2015.11.10水费由2.5调整为3.35
    'ELECTRIC_PRICE'=>0.55,
    //费用精度（小数点后保留几位）
    'CHARGE_PRECISION'=>2,
    // 分页中每页的条数
);
?>