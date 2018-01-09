/*
Navicat MySQL Data Transfer

Source Server         : 192.168.144.132
Source Server Version : 50556
Source Host           : localhost:3306
Source Database       : fmp

Target Server Type    : MYSQL
Target Server Version : 50556
File Encoding         : 65001

Date: 2018-01-09 19:01:44
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for BZ_ZGJOB_1
-- ----------------------------
DROP TABLE IF EXISTS `BZ_ZGJOB_1`;
CREATE TABLE `BZ_ZGJOB_1` (
  `BZJOBIDM` bigint(20) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`BZJOBIDM`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of BZ_ZGJOB_1
-- ----------------------------

-- ----------------------------
-- Table structure for FMP_ASSIST
-- ----------------------------
DROP TABLE IF EXISTS `FMP_ASSIST`;
CREATE TABLE `FMP_ASSIST` (
  `ASSIST_ID` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of FMP_ASSIST
-- ----------------------------
INSERT INTO `FMP_ASSIST` VALUES ('2');

-- ----------------------------
-- Table structure for FMP_FLOW
-- ----------------------------
DROP TABLE IF EXISTS `FMP_FLOW`;
CREATE TABLE `FMP_FLOW` (
  `FLOW_ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `FLOW_DIRID` bigint(20) NOT NULL,
  `FLOW_NAME` varchar(64) DEFAULT NULL,
  `FLOW_DESCRIPTION` varchar(255) DEFAULT NULL,
  `FLOW_STATUS` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`FLOW_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of FMP_FLOW
-- ----------------------------
INSERT INTO `FMP_FLOW` VALUES ('1', '1', '新进公务员资格审查', '1212121', '1');

-- ----------------------------
-- Table structure for FMP_FLOW_TABLE
-- ----------------------------
DROP TABLE IF EXISTS `FMP_FLOW_TABLE`;
CREATE TABLE `FMP_FLOW_TABLE` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `FLOW_ID` bigint(20) NOT NULL,
  `FLOW_TABLE_NAME` varchar(255) NOT NULL,
  `FLOW_TABLE_TYPE` tinyint(1) DEFAULT NULL COMMENT '1=主表，2=副表',
  `FLOW_TABLE_DESC` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of FMP_FLOW_TABLE
-- ----------------------------
INSERT INTO `FMP_FLOW_TABLE` VALUES ('5', '1', 'BZ_ZGJOB_1', '1', '新进公务员业务主表');

-- ----------------------------
-- Table structure for FMP_FLOWDIR
-- ----------------------------
DROP TABLE IF EXISTS `FMP_FLOWDIR`;
CREATE TABLE `FMP_FLOWDIR` (
  `FLOW_DIRID` bigint(20) NOT NULL AUTO_INCREMENT,
  `FLOW_DIRNAME` varchar(64) NOT NULL,
  `FLOW_DIRTIME` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`FLOW_DIRID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of FMP_FLOWDIR
-- ----------------------------
INSERT INTO `FMP_FLOWDIR` VALUES ('1', '公务员流程', '2017-12-24 14:47:59');
INSERT INTO `FMP_FLOWDIR` VALUES ('2', '发的所发生的', '2017-12-24 15:05:16');

-- ----------------------------
-- Table structure for FMP_TABLE_FIELD
-- ----------------------------
DROP TABLE IF EXISTS `FMP_TABLE_FIELD`;
CREATE TABLE `FMP_TABLE_FIELD` (
  `FIELD_ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `FIELD_NAME` varchar(64) NOT NULL,
  `FIELD_DESC` varchar(100) DEFAULT NULL,
  `FIELD_TYPE` int(4) DEFAULT NULL,
  `FIELD_CODE` varchar(64) DEFAULT NULL,
  `FIELD_VERIFY` varchar(100) DEFAULT NULL,
  `FIELD_BELONG_NODE` varchar(100) DEFAULT NULL,
  `FIELD_GLOBE_REQUIRE` tinyint(1) NOT NULL DEFAULT '0',
  `FLOW_ID` bigint(20) NOT NULL,
  `TABLE_NAME` varchar(64) NOT NULL,
  PRIMARY KEY (`FIELD_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of FMP_TABLE_FIELD
-- ----------------------------

-- ----------------------------
-- Table structure for FMP_USER
-- ----------------------------
DROP TABLE IF EXISTS `FMP_USER`;
CREATE TABLE `FMP_USER` (
  `USER_ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `USER_NAME` varchar(32) NOT NULL,
  `USER_PWD` char(32) NOT NULL,
  `USER_STATUS` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`USER_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of FMP_USER
-- ----------------------------
INSERT INTO `FMP_USER` VALUES ('1', 'admin', 'fcea920f7412b5da7be0cf42b8c93759', '1');

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `uid` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `name` varchar(128) DEFAULT NULL COMMENT '用户名（身份证号）',
  `realName` varchar(128) DEFAULT NULL COMMENT '真实姓名',
  `password` varchar(128) DEFAULT NULL COMMENT '登录密码',
  `userType` int(1) DEFAULT NULL COMMENT '用户类别（0：具有模块设置权限）',
  `userLoginCount` int(11) DEFAULT NULL COMMENT '登录次数',
  `userRegisterTime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `userLastLoginTime` timestamp NULL DEFAULT NULL COMMENT '最后登录时间',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='用户表';

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES ('1', 'admin', '管理员', 'fcea920f7412b5da7be0cf42b8c93759', '0', '10', '2017-12-23 18:14:21', '2017-12-24 14:20:15');
