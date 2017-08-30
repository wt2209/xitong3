# Host: localhost  (Version: 5.5.40)
# Date: 2017-08-28 20:28:29
# Generator: MySQL-Front 5.3  (Build 4.120)

/*!40101 SET NAMES utf8 */;


/*************************************   更改或新建表   *****************************/


#
# Structure for table "bh_room"
#

DROP TABLE IF EXISTS `bh_room`;
CREATE TABLE `bh_room` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type_id` smallint(5) unsigned DEFAULT NULL COMMENT '房间类型',
  `display_name` varchar(20) not null default '' comment '房间全名',
  `building` varchar(4) DEFAULT NULL,
  `unit` tinyint(1) unsigned DEFAULT NULL,
  `room_name` varchar(5) DEFAULT NULL,
  `person_number` TINYINT(2) UNSIGNED DEFAULT 0 COMMENT '房间内人数',
  `record` TEXT comment '房间大事记',
  `room_remark` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `display_name` (`display_name`),
  KEY `building` (`building`),
  KEY `unit` (`unit`),
  KEY `room_name` (`room_name`),
  KEY `type_id` (`type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `bh_room_type`;
CREATE TABLE `bh_room_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type_name` varchar(20) DEFAULT NULL COMMENT '房间类型 1|大学生 2|职工 3|派遣工 4|租赁',
  `description` varchar(255) DEFAULT '',
  `created_at` datetime DEFAULT NULL,
  `deleted` TINYINT(1) UNSIGNED DEFAULT 0,
  `deleted_at` DATETIME DEFAULT NULL ,
  PRIMARY KEY (`id`),
  KEY `type_name` (`type_name`)
) ENGINE=InnoDB AUTO_INCREMENT=938 DEFAULT CHARSET=utf8;


/*************************************   新建表结束   *****************************/





#
# Structure for table "bh_access"
#

DROP TABLE IF EXISTS `bh_access`;
CREATE TABLE `bh_access` (
  `role_id` smallint(6) unsigned NOT NULL,
  `node_id` smallint(6) unsigned NOT NULL,
  `level` tinyint(1) NOT NULL,
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=548 DEFAULT CHARSET=utf8;

#
# Structure for table "bh_admin"
#

DROP TABLE IF EXISTS `bh_admin`;
CREATE TABLE `bh_admin` (
  `aid` smallint(5) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户名表主键',
  `username` char(10) NOT NULL DEFAULT '' COMMENT '用户名',
  `display_name` varchar(20) DEFAULT NULL,
  `password` char(32) NOT NULL DEFAULT '' COMMENT '密码',
  `logintime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `rid` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`aid`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

#
# Structure for table "bh_college"
#

DROP TABLE IF EXISTS `bh_college`;
CREATE TABLE `bh_college` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `room_id` int(10) unsigned DEFAULT NULL,
  `department` char(13) NOT NULL DEFAULT '' COMMENT '部门',
  `name` char(4) NOT NULL DEFAULT '' COMMENT '姓名',
  `sex` tinyint(1) NOT NULL DEFAULT '1' COMMENT '性别  1|男  2|女',
  `bed_number` tinyint(1) DEFAULT NULL COMMENT '床号',
  `edu` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0|本科以下',
  `entrancetime` char(10) NOT NULL DEFAULT '' COMMENT '入住时间',
  `tel` char(13) NOT NULL DEFAULT '' COMMENT '电话',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `identify` char(18) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `room_id_key` (`room_id`),
  KEY `name_key` (`name`),
  KEY `tel_key` (`tel`)
) ENGINE=InnoDB AUTO_INCREMENT=946 DEFAULT CHARSET=utf8;

#
# Structure for table "bh_college_move"
#

DROP TABLE IF EXISTS `bh_college_move`;
CREATE TABLE `bh_college_move` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `old_room` varchar(10) NOT NULL DEFAULT '' COMMENT '原房间',
  `department` char(13) NOT NULL DEFAULT '' COMMENT '部门',
  `name` char(4) NOT NULL DEFAULT '' COMMENT '姓名',
  `sex` tinyint(1) NOT NULL COMMENT '性别',
  `bed_number` tinyint(1) DEFAULT NULL COMMENT '床号',
  `edu` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0|本科以下',
  `entrancetime` char(10) NOT NULL DEFAULT '' COMMENT '入住时间',
  `tel` char(13) NOT NULL DEFAULT '' COMMENT '电话',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `move_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '退房时间',
  `new_room` varchar(10) NOT NULL DEFAULT '' COMMENT '新房间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=293 DEFAULT CHARSET=utf8;

#
# Structure for table "bh_college_quit"
#

DROP TABLE IF EXISTS `bh_college_quit`;
CREATE TABLE `bh_college_quit` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `room` char(10) NOT NULL DEFAULT '' COMMENT '房间全名，不关联room表',
  `department` char(13) NOT NULL DEFAULT '' COMMENT '部门',
  `name` char(4) NOT NULL DEFAULT '' COMMENT '姓名',
  `sex` tinyint(1) NOT NULL COMMENT '性别',
  `bed_number` tinyint(1) DEFAULT NULL COMMENT '床号',
  `edu` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0|本科以下',
  `entrancetime` char(10) NOT NULL DEFAULT '' COMMENT '入住时间',
  `tel` char(13) NOT NULL DEFAULT '' COMMENT '电话',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `quit_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '退房时间',
  PRIMARY KEY (`id`),
  KEY `name_key` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=422 DEFAULT CHARSET=utf8;

#
# Structure for table "bh_dispatch"
#

DROP TABLE IF EXISTS `bh_dispatch`;
CREATE TABLE `bh_dispatch` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `room_id` int(10) unsigned DEFAULT NULL,
  `department` char(13) NOT NULL DEFAULT '' COMMENT '部门',
  `name` char(4) NOT NULL DEFAULT '' COMMENT '姓名',
  `sex` tinyint(1) NOT NULL COMMENT '性别',
  `bed_number` tinyint(1) DEFAULT NULL COMMENT '床号',
  `edu` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0|本科以下',
  `entrancetime` char(10) NOT NULL DEFAULT '' COMMENT '入住时间',
  `tel` char(13) NOT NULL DEFAULT '' COMMENT '电话',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  PRIMARY KEY (`id`),
  KEY `room_id_key` (`room_id`),
  KEY `name_key` (`name`),
  KEY `tel_key` (`tel`)
) ENGINE=InnoDB AUTO_INCREMENT=744 DEFAULT CHARSET=utf8;

#
# Structure for table "bh_dispatch_invisible"
#

DROP TABLE IF EXISTS `bh_dispatch_invisible`;
CREATE TABLE `bh_dispatch_invisible` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `room_id` int(10) unsigned DEFAULT NULL,
  `department` char(13) NOT NULL DEFAULT '' COMMENT '部门',
  `name` char(4) NOT NULL DEFAULT '' COMMENT '姓名',
  `sex` tinyint(1) NOT NULL COMMENT '性别',
  `bed_number` tinyint(1) DEFAULT NULL COMMENT '床号',
  `edu` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0|本科以下',
  `entrancetime` char(10) NOT NULL DEFAULT '' COMMENT '入住时间',
  `tel` char(13) NOT NULL DEFAULT '' COMMENT '电话',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  PRIMARY KEY (`id`),
  KEY `room_id_key` (`room_id`),
  KEY `name_key` (`name`),
  KEY `tel_key` (`tel`)
) ENGINE=InnoDB AUTO_INCREMENT=446 DEFAULT CHARSET=utf8;

#
# Structure for table "bh_dispatch_move"
#

DROP TABLE IF EXISTS `bh_dispatch_move`;
CREATE TABLE `bh_dispatch_move` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `old_room` varchar(10) NOT NULL DEFAULT '' COMMENT '原房间',
  `department` char(13) NOT NULL DEFAULT '' COMMENT '部门',
  `name` char(4) NOT NULL DEFAULT '' COMMENT '姓名',
  `sex` tinyint(1) NOT NULL COMMENT '性别',
  `bed_number` tinyint(1) DEFAULT NULL COMMENT '床号',
  `edu` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0|本科以下',
  `entrancetime` char(10) NOT NULL DEFAULT '' COMMENT '入住时间',
  `tel` char(13) NOT NULL DEFAULT '' COMMENT '电话',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `move_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '退房时间',
  `new_room` varchar(10) NOT NULL DEFAULT '' COMMENT '新房间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#
# Structure for table "bh_dispatch_quit"
#

DROP TABLE IF EXISTS `bh_dispatch_quit`;
CREATE TABLE `bh_dispatch_quit` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `room` char(10) NOT NULL DEFAULT '' COMMENT '房间全名，不关联room表',
  `department` char(13) NOT NULL DEFAULT '' COMMENT '部门',
  `name` char(4) NOT NULL DEFAULT '' COMMENT '姓名',
  `sex` tinyint(1) NOT NULL COMMENT '性别',
  `bed_number` tinyint(1) DEFAULT NULL COMMENT '床号',
  `edu` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0|本科以下',
  `entrancetime` char(10) NOT NULL DEFAULT '' COMMENT '入住时间',
  `tel` char(13) NOT NULL DEFAULT '' COMMENT '电话',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `quit_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '退房时间',
  PRIMARY KEY (`id`),
  KEY `name_key` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8;

#
# Structure for table "bh_node"
#

DROP TABLE IF EXISTS `bh_node`;
CREATE TABLE `bh_node` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `title` char(10) DEFAULT NULL,
  `controller` char(30) NOT NULL,
  `action` char(30) NOT NULL,
  `status` tinyint(1) unsigned NOT NULL,
  `remark` varchar(255) DEFAULT NULL,
  `pid` smallint(5) unsigned NOT NULL,
  `level` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=107 DEFAULT CHARSET=utf8;

#
# Structure for table "bh_rent"
#

DROP TABLE IF EXISTS `bh_rent`;
CREATE TABLE `bh_rent` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `room_id` int(10) unsigned DEFAULT NULL COMMENT '外键',
  `department` char(13) NOT NULL COMMENT '部门',
  `name` char(4) NOT NULL COMMENT '姓名',
  `sex` tinyint(1) NOT NULL COMMENT '性别 1|男 2|女',
  `contract_s` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '合同起始日期',
  `contract_e` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '合同终止日期',
  `rent_s` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '租赁开始日期',
  `rent_e` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '租赁结束日期',
  `tel` char(13) NOT NULL COMMENT '电话',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `identify` char(18) DEFAULT NULL COMMENT '身份证号',
  PRIMARY KEY (`id`),
  KEY `room_id_key` (`room_id`),
  KEY `name_key` (`name`),
  KEY `tel_key` (`tel`),
  KEY `rent_s` (`rent_s`),
  KEY `rent_e` (`rent_e`)
) ENGINE=InnoDB AUTO_INCREMENT=594 DEFAULT CHARSET=utf8;

#
# Structure for table "bh_rent_charge"
#

DROP TABLE IF EXISTS `bh_rent_charge`;
CREATE TABLE `bh_rent_charge` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rent_id` int(11) unsigned NOT NULL COMMENT '租赁户id',
  `charge_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1为押金，2为租金，3为物业费，4为水费，5为电费，6为取暖费，7为燃气费，8为电梯费',
  `money` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '金额',
  `charge_remark` varchar(255) NOT NULL DEFAULT '' COMMENT '费用说明（备注）',
  `quarter` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '季度数，1-12',
  `charge_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '缴费时间',
  PRIMARY KEY (`id`),
  KEY `rent_id` (`rent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=22406 DEFAULT CHARSET=utf8;

#
# Structure for table "bh_rent_charge_quit"
#

DROP TABLE IF EXISTS `bh_rent_charge_quit`;
CREATE TABLE `bh_rent_charge_quit` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rent_quit_id` int(11) unsigned NOT NULL COMMENT '已退房租赁户id',
  `charge_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1为押金，2为租金，3为物业费，4为水费，5为电费，6为取暖费，7为燃气费，8为电梯费',
  `money` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '金额',
  `charge_remark` varchar(255) NOT NULL DEFAULT '' COMMENT '费用说明（备注）',
  `quarter` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '季度数，1-12',
  `charge_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '缴费时间',
  PRIMARY KEY (`id`),
  KEY `rent_quit_id_key` (`rent_quit_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20783 DEFAULT CHARSET=utf8;

#
# Structure for table "bh_rent_quit"
#

DROP TABLE IF EXISTS `bh_rent_quit`;
CREATE TABLE `bh_rent_quit` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `room` char(13) NOT NULL COMMENT '房间全名，不关联room表',
  `department` char(13) NOT NULL COMMENT '部门',
  `name` char(4) NOT NULL COMMENT '姓名',
  `sex` tinyint(1) NOT NULL COMMENT '性别',
  `contract_s` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '合同起始日期',
  `contract_e` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '合同终止日期',
  `rent_s` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '租赁开始日期',
  `rent_e` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '租赁结束日期',
  `tel` char(13) NOT NULL COMMENT '电话',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `quit_time` int(10) NOT NULL COMMENT '退租时间',
  `identify` char(18) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `name_key` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8;

#
# Structure for table "bh_rent_renew"
#

DROP TABLE IF EXISTS `bh_rent_renew`;
CREATE TABLE `bh_rent_renew` (
  `renew_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '续签主键',
  `rid` int(11) unsigned NOT NULL,
  `department` char(13) NOT NULL COMMENT '部门',
  `name` char(4) NOT NULL COMMENT '姓名',
  `renew_time` int(11) DEFAULT NULL,
  `contract_s` int(10) NOT NULL COMMENT '合同起始日期',
  `contract_e` int(10) NOT NULL COMMENT '合同终止日期',
  `rent_s` int(10) DEFAULT NULL COMMENT '租赁开始日期',
  `rent_e` int(10) NOT NULL COMMENT '租赁结束日期',
  `tel` char(13) NOT NULL COMMENT '电话',
  `room` char(13) NOT NULL COMMENT '房间号',
  `contract_s_new` int(10) NOT NULL COMMENT '合同起始日期-新',
  `contract_e_new` int(10) NOT NULL COMMENT '合同终止日期-新',
  `rent_e_new` int(10) NOT NULL COMMENT '租赁结束日期-新',
  PRIMARY KEY (`renew_id`),
  KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=376 DEFAULT CHARSET=utf8;

#
# Structure for table "bh_repair"
#

DROP TABLE IF EXISTS `bh_repair`;
CREATE TABLE `bh_repair` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `input_man` varchar(20) DEFAULT NULL COMMENT '录入人',
  `name` varchar(20) DEFAULT NULL COMMENT '报修人',
  `location` varchar(255) DEFAULT NULL COMMENT '位置',
  `content` varchar(255) DEFAULT NULL COMMENT '报修内容',
  `created_at` varchar(20) DEFAULT NULL COMMENT '报修时间',
  `is_reviewed` tinyint(3) DEFAULT '0' COMMENT '是否审核',
  `reviewer` varchar(20) DEFAULT NULL COMMENT '审核人',
  `is_passed` tinyint(3) DEFAULT '0' COMMENT '是否通过审核',
  `review_remark` varchar(255) DEFAULT NULL COMMENT '审核意见',
  `reviewed_at` varchar(20) DEFAULT NULL COMMENT '审核时间',
  `is_printed` tinyint(3) DEFAULT '0' COMMENT '是否已打印',
  `printed_at` varchar(20) DEFAULT NULL COMMENT '第一次打印时间',
  `is_finished` tinyint(3) DEFAULT '0' COMMENT '是否完工',
  `finished_at` varchar(20) DEFAULT NULL COMMENT '完工时间',
  `finish_remark` varchar(255) DEFAULT NULL COMMENT '完工说明',
  `comment` varchar(255) DEFAULT NULL COMMENT '完工评价',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

#
# Structure for table "bh_role"
#

DROP TABLE IF EXISTS `bh_role`;
CREATE TABLE `bh_role` (
  `rid` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `role_name` char(10) NOT NULL,
  `remark` char(100) NOT NULL,
  `issystem` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`rid`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;



#
# Structure for table "bh_single_sd_base"
#

DROP TABLE IF EXISTS `bh_single_sd_base`;
CREATE TABLE `bh_single_sd_base` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `room_id` int(10) unsigned DEFAULT NULL COMMENT '房间id',
  `water_base` int(10) unsigned DEFAULT NULL COMMENT '水表底数',
  `electric_base` int(10) unsigned DEFAULT NULL COMMENT '电表底数',
  `name` char(4) DEFAULT NULL COMMENT '抄表人',
  `month` tinyint(2) unsigned DEFAULT NULL COMMENT '月度',
  `year` smallint(4) unsigned DEFAULT NULL COMMENT '年度',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `read_time` int(10) unsigned DEFAULT NULL COMMENT '抄表时间',
  PRIMARY KEY (`id`),
  KEY `room_id_key` (`room_id`),
  KEY `month_key` (`month`),
  KEY `year_key` (`year`),
  KEY `read_time_key` (`read_time`)
) ENGINE=InnoDB AUTO_INCREMENT=1444 DEFAULT CHARSET=utf8 COMMENT='单身住户水电表底数表';

#
# Structure for table "bh_single_sd_charge"
#

DROP TABLE IF EXISTS `bh_single_sd_charge`;
CREATE TABLE `bh_single_sd_charge` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `room_id` int(10) unsigned DEFAULT NULL COMMENT '房间id',
  `water` int(11) DEFAULT NULL,
  `electric` int(11) DEFAULT NULL,
  `water_money` decimal(10,2) DEFAULT NULL COMMENT '水费',
  `electric_money` decimal(10,2) DEFAULT NULL COMMENT '电费',
  `start_month` tinyint(2) unsigned DEFAULT NULL COMMENT '开始月度',
  `end_month` tinyint(2) unsigned DEFAULT NULL COMMENT '结束月度',
  `start_year` smallint(4) unsigned DEFAULT NULL COMMENT '开始年度',
  `end_year` smallint(4) unsigned DEFAULT NULL COMMENT '结束年度',
  `create_time` int(11) DEFAULT NULL COMMENT '生成时间',
  `is_charged` tinyint(1) unsigned DEFAULT NULL COMMENT '是否缴费 0|没有 1|已缴费',
  `charge_time` int(11) DEFAULT NULL COMMENT '缴费时间',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`id`),
  KEY `room_id_key` (`room_id`),
  KEY `start_month_key` (`start_month`),
  KEY `start_year_key` (`start_year`),
  KEY `end_month_key` (`end_month`),
  KEY `end_year_key` (`end_year`),
  KEY `is_charged_key` (`is_charged`),
  KEY `charge_time_key` (`charge_time`)
) ENGINE=InnoDB AUTO_INCREMENT=1471 DEFAULT CHARSET=utf8 COMMENT='单身住户水电费表';

#
# Structure for table "bh_worker"
#

DROP TABLE IF EXISTS `bh_worker`;
CREATE TABLE `bh_worker` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `room_id` int(10) unsigned DEFAULT NULL,
  `department` char(13) NOT NULL DEFAULT '' COMMENT '部门',
  `name` char(4) NOT NULL DEFAULT '' COMMENT '姓名',
  `sex` tinyint(1) NOT NULL COMMENT '性别',
  `bed_number` tinyint(1) DEFAULT NULL COMMENT '床号',
  `edu` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0|本科以下',
  `entrancetime` char(10) NOT NULL DEFAULT '' COMMENT '入住时间',
  `tel` char(13) NOT NULL DEFAULT '' COMMENT '电话',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `identify` char(18) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `room_id_key` (`room_id`),
  KEY `name_key` (`name`),
  KEY `tel_key` (`tel`)
) ENGINE=InnoDB AUTO_INCREMENT=277 DEFAULT CHARSET=utf8;

#
# Structure for table "bh_worker_copy"
#

DROP TABLE IF EXISTS `bh_worker_copy`;
CREATE TABLE `bh_worker_copy` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `room` char(10) DEFAULT NULL,
  `department` char(13) NOT NULL DEFAULT '' COMMENT '部门',
  `name` char(4) NOT NULL DEFAULT '' COMMENT '姓名',
  `sex` tinyint(1) NOT NULL COMMENT '性别',
  `bed_number` tinyint(1) DEFAULT NULL COMMENT '床号',
  `edu` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0|本科以下',
  `entrancetime` char(10) NOT NULL DEFAULT '' COMMENT '入住时间',
  `tel` char(13) NOT NULL DEFAULT '' COMMENT '电话',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  PRIMARY KEY (`id`),
  KEY `room_id_key` (`room`),
  KEY `name_key` (`name`),
  KEY `tel_key` (`tel`)
) ENGINE=InnoDB AUTO_INCREMENT=168 DEFAULT CHARSET=utf8;

#
# Structure for table "bh_worker_move"
#

DROP TABLE IF EXISTS `bh_worker_move`;
CREATE TABLE `bh_worker_move` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `old_room` varchar(10) NOT NULL DEFAULT '' COMMENT '原房间',
  `department` char(13) NOT NULL DEFAULT '' COMMENT '部门',
  `name` char(4) NOT NULL DEFAULT '' COMMENT '姓名',
  `sex` tinyint(1) NOT NULL COMMENT '性别',
  `bed_number` tinyint(1) DEFAULT NULL COMMENT '床号',
  `edu` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0|本科以下',
  `entrancetime` char(10) NOT NULL DEFAULT '' COMMENT '入住时间',
  `tel` char(13) NOT NULL DEFAULT '' COMMENT '电话',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `move_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '退房时间',
  `new_room` varchar(10) NOT NULL DEFAULT '' COMMENT '新房间',
  PRIMARY KEY (`id`),
  KEY `name_key` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8;

#
# Structure for table "bh_worker_quit"
#

DROP TABLE IF EXISTS `bh_worker_quit`;
CREATE TABLE `bh_worker_quit` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `room` char(10) NOT NULL DEFAULT '' COMMENT '房间全名，不关联room表',
  `department` char(13) NOT NULL DEFAULT '' COMMENT '部门',
  `name` char(4) NOT NULL DEFAULT '' COMMENT '姓名',
  `sex` tinyint(1) NOT NULL COMMENT '性别',
  `bed_number` tinyint(1) DEFAULT NULL COMMENT '床号',
  `edu` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0|本科以下',
  `entrancetime` char(10) NOT NULL DEFAULT '' COMMENT '入住时间',
  `tel` char(13) NOT NULL DEFAULT '' COMMENT '电话',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `quit_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '退房时间',
  PRIMARY KEY (`id`),
  KEY `name_key` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=95 DEFAULT CHARSET=utf8;

#
# Structure for table "bh_worker_rent_charge"
#

DROP TABLE IF EXISTS `bh_worker_rent_charge`;
CREATE TABLE `bh_worker_rent_charge` (
  `charge_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `id` int(10) unsigned NOT NULL COMMENT '人员id',
  `money` decimal(7,2) NOT NULL COMMENT '金额',
  `worker_rent_remark` varchar(255) NOT NULL COMMENT '备注',
  `charge_time` int(11) NOT NULL COMMENT '缴费时间',
  `charge_type` tinyint(1) unsigned DEFAULT NULL COMMENT '1|租金 2|床位费',
  PRIMARY KEY (`charge_id`),
  KEY `id` (`id`,`charge_time`)
) ENGINE=InnoDB AUTO_INCREMENT=962 DEFAULT CHARSET=utf8;

#
# Structure for table "bh_worker_sd_charge"
#

DROP TABLE IF EXISTS `bh_worker_sd_charge`;
CREATE TABLE `bh_worker_sd_charge` (
  `sd_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(4) NOT NULL COMMENT '缴费人姓名',
  `charge_type` tinyint(1) unsigned DEFAULT NULL COMMENT '1|水费 2|电费',
  `room_id` int(10) unsigned NOT NULL,
  `money` decimal(6,2) NOT NULL,
  `remark` varchar(255) NOT NULL,
  `charge_time` int(11) unsigned NOT NULL,
  PRIMARY KEY (`sd_id`),
  KEY `room_id` (`room_id`),
  KEY `name` (`name`),
  KEY `type` (`charge_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
