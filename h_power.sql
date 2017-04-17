/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50547
Source Host           : localhost:3306
Source Database       : hqc

Target Server Type    : MYSQL
Target Server Version : 50547
File Encoding         : 65001

Date: 2017-04-10 17:42:07
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for h_power
-- ----------------------------
DROP TABLE IF EXISTS `h_power`;
CREATE TABLE `h_power` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `name` varchar(80) DEFAULT NULL COMMENT '权限名字',
  `route` varchar(80) DEFAULT NULL COMMENT '路由地址',
  `left_key` int(5) DEFAULT NULL COMMENT '左边界',
  `right_key` int(5) DEFAULT NULL COMMENT '右边界',
  `parent_id` int(2) DEFAULT NULL COMMENT '父id',
  `level` int(2) DEFAULT NULL COMMENT '层级',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
