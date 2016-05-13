-- phpMyAdmin SQL Dump
-- version 4.0.4
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2016 年 05 月 10 日 02:37
-- 服务器版本: 5.6.12-log
-- PHP 版本: 5.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `siront`
--
CREATE DATABASE IF NOT EXISTS `siront` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `siront`;

-- --------------------------------------------------------

--
-- 表的结构 `siront_admin`
--

CREATE TABLE IF NOT EXISTS `siront_admin` (
  `aid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `password` char(32) NOT NULL,
  `role` tinyint(8) NOT NULL COMMENT '1:管理员、2:运维、3财务、4:客服、5:编辑',
  `status` tinyint(8) NOT NULL,
  PRIMARY KEY (`aid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='管理员表' AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `siront_admin`
--

INSERT INTO `siront_admin` (`aid`, `name`, `password`, `role`, `status`) VALUES
(1, 'admin', 'cde91694ce003d6125e4da50a011c73b', 1, 1);

-- --------------------------------------------------------

--
-- 表的结构 `siront_admin_action`
--

CREATE TABLE IF NOT EXISTS `siront_admin_action` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `aid` bigint(20) unsigned NOT NULL,
  `action` varchar(45) NOT NULL COMMENT '操作',
  `time` int(10) NOT NULL COMMENT '时间',
  `ip` varchar(45) NOT NULL COMMENT 'ip地址',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='管理员操作记录' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `siront_category`
--

CREATE TABLE IF NOT EXISTS `siront_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `cid` int(10) unsigned NOT NULL,
  `status` tinyint(8) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='工种表' AUTO_INCREMENT=32 ;

--
-- 转存表中的数据 `siront_category`
--

INSERT INTO `siront_category` (`id`, `name`, `cid`, `status`) VALUES
(1, '分类一', 0, 1),
(26, '阿什顿发', 1, 1),
(9, '分类二', 0, 0),
(10, '分类三', 0, 1),
(27, '阿萨德', 1, 0),
(28, 'asdf ', 1, 1),
(29, '2.1', 9, 1),
(30, '2.2', 9, 1);

-- --------------------------------------------------------

--
-- 表的结构 `siront_comment`
--

CREATE TABLE IF NOT EXISTS `siront_comment` (
  `cid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `from_uid` bigint(20) unsigned NOT NULL,
  `work_uid` bigint(20) unsigned NOT NULL,
  `score` tinyint(8) unsigned NOT NULL DEFAULT '1' COMMENT '用户给商家的评分',
  `comment` varchar(255) DEFAULT NULL COMMENT '用户对商家的评价',
  `time` int(10) unsigned NOT NULL COMMENT '时间',
  PRIMARY KEY (`cid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='评价表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `siront_company_info`
--

CREATE TABLE IF NOT EXISTS `siront_company_info` (
  `uid` bigint(20) unsigned NOT NULL,
  `cid` varchar(255) NOT NULL,
  `lon` int(10) NOT NULL,
  `lat` int(10) DEFAULT NULL,
  `score` tinyint(8) DEFAULT NULL COMMENT '评分',
  `phone` varchar(45) DEFAULT NULL COMMENT '联系方式',
  `level` tinyint(8) DEFAULT NULL COMMENT '级别',
  `desc` text COMMENT '企业介绍',
  `sfzurl` varchar(255) DEFAULT NULL COMMENT '法人身份证图片',
  `licenseurl` varchar(255) DEFAULT NULL COMMENT '营业执照图片',
  `status` tinyint(8) DEFAULT NULL COMMENT '企业用户状态同user表里面的company_status',
  `place` varchar(45) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='企业信息表';

-- --------------------------------------------------------

--
-- 表的结构 `siront_complaint`
--

CREATE TABLE IF NOT EXISTS `siront_complaint` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `from_uid` bigint(20) unsigned NOT NULL,
  `work_uid` bigint(20) unsigned NOT NULL,
  `content` varchar(255) NOT NULL COMMENT '投诉类容',
  `time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='投诉' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `siront_person_info`
--

CREATE TABLE IF NOT EXISTS `siront_person_info` (
  `uid` bigint(20) unsigned NOT NULL,
  `cid` varchar(255) NOT NULL COMMENT '分类数据json',
  `lon` int(10) NOT NULL DEFAULT '1164600000' COMMENT '经度 *10^7',
  `lat` int(10) NOT NULL DEFAULT '399200000' COMMENT '纬度 *10^7',
  `score` tinyint(8) DEFAULT NULL COMMENT '十分制',
  `phone` varchar(45) DEFAULT NULL COMMENT '工作联系方式',
  `sfzurl` varchar(255) DEFAULT NULL COMMENT '身份证存储的地址',
  `status` tinyint(8) DEFAULT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='个体户信息';

-- --------------------------------------------------------

--
-- 表的结构 `siront_task`
--

CREATE TABLE IF NOT EXISTS `siront_task` (
  `tid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `from_uid` bigint(20) unsigned NOT NULL COMMENT '发布任务人的ID',
  `create_time` int(10) unsigned NOT NULL COMMENT '创建任务的时间',
  `operate_time` int(10) unsigned NOT NULL COMMENT '开始任务的时间',
  `title` varchar(255) NOT NULL COMMENT '任务标题',
  `desc` text COMMENT '任务描述',
  `tel` varchar(45) DEFAULT NULL COMMENT '发布人联系方式',
  `address` varchar(255) DEFAULT NULL COMMENT '任务地址',
  `lat` int(10) DEFAULT NULL,
  `lon` int(10) DEFAULT NULL,
  `status` tinyint(8) DEFAULT NULL COMMENT '（状态）[1：发布中，2已接单，3已完成，4：已评价，5：放弃]',
  `work_uid` bigint(20) DEFAULT NULL COMMENT '工人ID',
  `money` decimal(7,2) DEFAULT NULL COMMENT '任务金额',
  `cid` int(10) DEFAULT NULL COMMENT '任务类别',
  `paytype` tinyint(8) DEFAULT NULL COMMENT '支付方式',
  PRIMARY KEY (`tid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `siront_user`
--

CREATE TABLE IF NOT EXISTS `siront_user` (
  `uid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `phone` char(11) NOT NULL COMMENT '手机号',
  `nickname` varchar(45) DEFAULT NULL COMMENT '昵称',
  `password` varchar(45) DEFAULT NULL COMMENT 'md5加密后的密码长度限制6,16',
  `headimgurl` varchar(45) DEFAULT NULL COMMENT '用户图像存储位置',
  `money` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '账户余额',
  `coin` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户积分',
  `user_status` tinyint(8) unsigned NOT NULL DEFAULT '0' COMMENT '普通用户状态0：注册用户，1：认证用户，2：黑名单',
  `person_status` tinyint(8) unsigned NOT NULL DEFAULT '0' COMMENT '个体户状态 0：非个体户，1：认证中，2：已认证，3黑名单',
  `company_status` tinyint(8) unsigned NOT NULL DEFAULT '0' COMMENT '企业状态0：非企业用户，1：企业用户，2拉黑企业用户',
  `create_time` int(10) NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- 转存表中的数据 `siront_user`
--

INSERT INTO `siront_user` (`uid`, `phone`, `nickname`, `password`, `headimgurl`, `money`, `coin`, `user_status`, `person_status`, `company_status`, `create_time`) VALUES
(1, '15753962833', '普通用户', 'e10adc3949ba59abbe56e057f20f883e', NULL, '0.00', 0, 1, 0, 0, 0),
(2, '15753962834', '个体户', 'c20ad4d76fe97759aa27a0c99bff6710', NULL, '0.00', 0, 1, 2, 0, 0),
(3, '15753962835', '企业用户', 'e10adc3949ba59abbe56e057f20f883e', NULL, '0.00', 0, 2, 0, 2, 0);

-- --------------------------------------------------------

--
-- 表的结构 `siront_user_info`
--

CREATE TABLE IF NOT EXISTS `siront_user_info` (
  `uid` bigint(20) unsigned NOT NULL,
  `address` varchar(45) DEFAULT NULL COMMENT '联系地址',
  `phone` varchar(45) DEFAULT NULL COMMENT '联系方式',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户基本信息表';

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
