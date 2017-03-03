
CREATE TABLE `gmadmin` (
  `adminid` bigint(20)  NOT NULL auto_increment  COMMENT '登陆GM', 
  `psw` varchar(128) CHARACTER SET utf8 NOT NULL COMMENT 'GM密码',
  `level`  varchar(128) CHARACTER SET utf8 NOT NULL COMMENT 'GM的权限'
  `ip` varchar(20) CHARACTER SET utf8 NOT NULL  COMMENT '可以访问的ip地址',
  PRIMARY KEY (`adminid`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE `account` (
  `account` bigint(20)  NOT NULL auto_increment  COMMENT '玩家游戏的id', 
  `thirdid` varchar(128) CHARACTER SET utf8 NOT NULL COMMENT '第三方id',
  `userid`  varchar(128) CHARACTER SET utf8 NOT NULL COMMENT '第三方生成的登录id',
  `uuid`  varchar(128)	 CHARACTER SET utf8 NOT NULL COMMENT '设备uuid',
  `createtime` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '账号创建时间',
  `idfa` varchar(128) CHARACTER SET utf8 NOT NULL  COMMENT '手机号码',
  `devicetype`varchar(20) CHARACTER SET utf8 NOT NULL  COMMENT 'deviceType',
  `version`varchar(20) CHARACTER SET utf8 NOT NULL  COMMENT 'version',
  `clientip`varchar(20) CHARACTER SET utf8 NOT NULL  COMMENT 'clientIp',
  
  PRIMARY KEY (`account`),index(uuid),index(thirdid)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE `player` (
  `account` bigint(20)  NOT NULL  COMMENT '玩家id', 
  `sessionid` varchar(128) CHARACTER SET utf8 NOT NULL COMMENT 'session', 
  `username` varchar(128) CHARACTER SET utf8 NOT NULL COMMENT '姓名',
  `level`      int(10)  COMMENT 'level',
  `vip`        int(10) COMMENT 'vip',
  `createtime` int(10) COMMENT '账号创建时间',
  `logintime`  int(10) COMMENT '账号登录时间',
  `offlinetime`  int(10) COMMENT '账号下线时间',
  `resettime`  int(10) COMMENT '每日重置时间',
  `userinfo` blob NULL COMMENT '玩家基本信息',
  `usermaterial` blob NULL COMMENT '玩家材料信息',
  `usercharacter`  blob NULL COMMENT '玩家角色信息',
  `userpets`  blob NULL COMMENT '玩家宠物信息',
  `userequip` blob NULL COMMENT  '玩家装备信息',
  `userequip2` blob NULL COMMENT  '玩家灵器信息',
  `usegeneralskills` blob NULL COMMENT  '玩家通用技能信息',
  `usegameinfo` blob NULL COMMENT  '玩家一般游戏信息',
  `friends` blob NULL COMMENT  '玩家好友信息',
  `recharge` blob NULL COMMENT  '充值信息',
  `mainmission` blob NULL COMMENT  '主线任务',
  `achievement` blob NULL COMMENT  '成就任务',
  `dailymission` blob NULL COMMENT  '日常任务',
  `activpointreward` blob NULL COMMENT  '活跃度奖励',
  `signreward` blob NULL COMMENT  '签到奖励',
  `mailinfo` blob NULL COMMENT  '邮件',
  `dailyreset` blob NULL COMMENT  '每日重置数据记录',
  PRIMARY KEY (`account`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


---充值信息
CREATE TABLE `bill_info` (
  `bill_id` varchar(32) NOT NULL,
  `userid` varchar(32) NOT NULL,
  `paytype` tinyint(3) NOT NULL,
  `itemid` varchar(32) NOT NULL DEFAULT '',
  `orderid` varchar(32) NOT NULL DEFAULT '',
  `itemcount` int(11) NOT NULL DEFAULT '0',
  `ordermoney` int(11) NOT NULL DEFAULT '0',
  `gold` int(11) NOT NULL DEFAULT '0',
  `billstatus` tinyint(3) NOT NULL DEFAULT '1' COMMENT '1 业务方请求 2 业务完成',
  `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`bill_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;  

---每周积分排行榜数据，每周清理一下
CREATE TABLE `weekly_score` ( 
  `userid`  bigint(20) NOT NULL,
  `score` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;  







 



















