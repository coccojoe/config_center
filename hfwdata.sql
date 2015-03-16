/**
 confcenter 中间数据库结构
*/
/*建立数据库*/
CREATE DATABASE veconfer DEFAULT CHARACTER SET utf8;
use veconfer;
/*文件表*/
DROP TABLE IF EXISTS `file_tab`;
CREATE TABLE `file_tab`(
   `id` int NOT NULL auto_increment,          /*唯一标志ID*/
   `nme` varchar(64) default null,            /*配置名称*/ 
   `file` varchar(256) default null,          /*文件全路径*/
   `tpe` int(1) not  null default '1',        /*文件类型*/
   `node` int NOT NULL DEFAULT '0',           /*目标节点ID, 0表示所有节点*/
   `ste`  int(1) NOT NULL DEFAULT '0',        /*状态： 1 已启用 0未启用*/
   `dte` datetime default null,               /*创建时间*/
   `sum` varchar(64) default null,	      /*文件MD5值*/ 
   `tag` varchar(300) default null,           /*备注信息*/
  PRIMARY KEY(`id`)
)ENGINE=MyISAM DEFAULT CHARSET=UTF8;

insert into file_tab(`file`,`tpe`,`node`,`ste`,`dte`)values('/usr/local/services/nginx-1.4.7/conf/vhosts/.htaccess',1,'0',0,'2015-01-13 00:46:42');

/*节点信息表*/
DROP TABLE IF EXISTS `node_tab`;
CREATE TABLE `node_tab`(
  `id` int NOT NULL auto_increment,           /*唯一标志ID*/
  `nme` varchar(64) default null,             /*节点名称*/
  `ip` varchar(18) not null,                  /*节点IP*/
  `port` int(2) not null default '3230',      /*节点端口*/
  `ste`  int(1) not null default '0',         /*节点状态,0未激活*/
  `act` datetime default null,                /*最后签到时间*/
  `dte` datetime default null,                /*创建时间*/
  `hkey` varchar(64) not null,                /*节点访问密钥*/
  `tag` varchar(128) default null,            /*备注*/
  PRIMARY KEY(`id`)
)ENGINE=MyISAM DEFAULT CHARSET=UTF8;

insert into node_tab(`ip`,`port`,`ste`,`act`,`dte`,`hkey`,`tag`)values('192.168.62.253',32303,0,'0000-00-00 00:00:00','2015-01-13 01:17:34','70c881d4a26984ddce795f6f71817c9cf4480e79','test');

/*队列表*/
DROP TABLE IF EXISTS `task_tab`;
CREATE TABLE `task_tab`(
    `id` int not null auto_increment,         /*唯一标志ID*/
    `ip` varchar(18) not null,                /*执行者地址*/
    `tkey` varchar(64) not null,              /*任务密钥*/
    `fid`  int not null,                      /*文件ID*/
    `fpath` varchar(256) not null,            /*文件路径*/
    `dte`  datetime not null,                 /*任务时间*/
    `ste` int(1) default '0',                 /*执行状态反馈*/
    `msg` varchar(256) default null,          /*状态消息*/
    PRIMARY KEY(`id`)
)ENGINE=MyISAM DEFAULT CHARSET=UTF8;

/*配置信息表*/
DROP TABLE IF EXISTS `conf_tab`;
CREATE TABLE `conf_tab`(
  `id` int NOT NULL auto_increment,           /*唯一标志ID*/
  `fid` int NOT NULL,                         /*所属文件ID*/
  `nkey` varchar(64) default null,            /*KEY名*/
  `var` MediumText default null,                    /*配置项*/
  `tpy` int(1) not null default '0',          /*数据类型*/
  `act` int(1) not null default '0',          /*启用状态*/
  `son` varchar(64) not null default 'main',  /*节点名称*/
  PRIMARY KEY(`id`)
)ENGINE=MyISAM DEFAULT CHARSET=UTF8;

/*日志记录表*/
DROP TABLE IF EXISTS `fw_log`;
CREATE TABLE `fw_log`(
   `id` int NOT NULL auto_increment,           /*唯一标志ID*/
   `jet` varchar(64) default null,             /*操作说明*/
   `log` LongBlob default NULL,                    /*日志信息*/
   `uid` varchar(65) default NULL,             /*涉及用户*/
   `ip` char(20) default NULL,                 /*涉及IP*/
   `dte` datetime default NULL,                /*记录时间*/
   PRIMARY KEY(`id`)
)ENGINE=MyISAM DEFAULT CHARSET=UTF8;

/*用户表*/
DROP TABLE IF EXISTS `user_tab`;
CREATE TABLE `user_tab`(
    `id` int NOT NULL auto_increment,           /*唯一标志ID*/
    `nme` varchar(64) default null,             /*真实姓名*/
    `uid` varchar(32) not NULL,                 /*用户名*/
    `pwd` varchar(64) not NULL,                 /*密码*/
    `grup` varchar(300) not NULL,               /*权限表*/
    `ste` int(1) default '0',                   /*状态*/
    `dte` datetime not NULL,                    /*创建时间*/
    `lte` datetime default NULL,                /*最后一次登录时间*/
    `ip` char(18) default NULL,                 /*最后一次登录地址*/
    `tag` varchar(300) default NULL,            /*备注*/
    PRIMARY KEY(`id`)
)ENGINE=MyISAM DEFAULT CHARSET=UTF8;

insert into user_tab(nme,uid,pwd,grup,ste,dte,tag)values('管理员','veadmin',md5('vedefaultpwd'),'0',1,'2015.01.30 14:02:59','管理员还是...');


