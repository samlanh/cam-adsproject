/*
SQLyog Ultimate v10.00 Beta1
MySQL - 5.6.17 : Database - blank
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`blank` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `blank`;

/*Table structure for table `rms_acl_acl` */

DROP TABLE IF EXISTS `rms_acl_acl`;

CREATE TABLE `rms_acl_acl` (
  `acl_id` int(11) NOT NULL AUTO_INCREMENT,
  `module` varchar(100) DEFAULT NULL,
  `controller` varchar(50) DEFAULT NULL,
  `action` varchar(50) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL COMMENT '1: display in menu; 0: disable from menu',
  `rank` int(11) DEFAULT NULL COMMENT 'rank to show in submenu',
  `label` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`acl_id`)
) ENGINE=InnoDB AUTO_INCREMENT=185 DEFAULT CHARSET=utf8;

/*Data for the table `rms_acl_acl` */

insert  into `rms_acl_acl`(`acl_id`,`module`,`controller`,`action`,`status`,`rank`,`label`) values (4,'rsvacl','user','index',1,NULL,'view user'),(5,'rsvacl','user','add',1,NULL,'add user'),(6,'rsvacl','user','edit',1,NULL,'edit user'),(7,'rsvacl','useraccess','index',1,NULL,'view user access'),(8,'rsvacl','useraccess','add',1,NULL,'add user access'),(9,'rsvacl','usertype','index',1,NULL,'view user type'),(10,'rsvacl','usertype','add',1,NULL,'add user type'),(11,'rsvacl','usertype','edit',1,NULL,'edit user type'),(15,'other','province','index',0,1,'province'),(16,'other','province','add',0,1,'add province'),(17,'other','province','edit',0,1,'edit province'),(18,'other','district','index',0,2,'view district'),(19,'other','district','add',0,2,'add district'),(20,'other','district','edit',0,2,'edit district'),(21,'other','commune','index',0,3,'view commune'),(22,'other','commune','add',0,3,'add commune'),(23,'other','commune','edit',0,3,'edit commune'),(24,'other','village','index',0,4,'view village'),(25,'other','village','add',0,4,'add village'),(26,'other','village','edit',0,4,'edit village'),(36,'group','index','index',1,1,'view client'),(37,'group','index','add',1,1,'add client'),(38,'group','index','edit',1,1,'edit client'),(51,'group','index','view',1,1,'view client detail'),(53,'loan','index','add',1,1,'add loan IL'),(54,'loan','index','edit',1,1,'edit loan IL'),(55,'loan','index','view',1,1,'view detail loan IL'),(60,'loan','ilpayment','index',1,2,'view IL paymennt'),(61,'loan','ilpayment','add',1,2,'add Il payment'),(62,'loan','ilpayment','edit',1,2,'edit Il payment'),(63,'loan','ilpayment','view',1,2,'view detail IL payme'),(67,'loan','repaymentschedule','index',1,3,'view repayment sched'),(68,'loan','repaymentschedule','add',1,3,'add repayment schedu'),(69,'loan','repaymentschedule','edit',1,3,'edit repayment sched'),(85,'backup','index','index',1,NULL,'backup'),(86,'home','index','index',1,1,'dashboard'),(89,'other','loantype','index',1,7,'loan type'),(90,'other','loantype','edit',1,7,'edit loan type'),(91,'other','loantype','add',1,7,'add loan type'),(95,'rsvacl','useraccess','edit',NULL,NULL,'edit user access'),(96,'other','term','index',1,6,'term'),(97,'group','land','index',1,2,'land'),(98,'group','land','add',1,2,'land'),(99,'group','land','edit',1,2,'land'),(100,'group','land','view',1,2,'land'),(101,'group','propertiestype','index',1,3,'index'),(102,'group','propertiestype','add',1,3,'add'),(103,'group','propertiestype','view',1,3,'view'),(104,'group','project','index',1,4,NULL),(105,'group','project','add',1,4,NULL),(106,'group','project','edit',1,4,NULL),(107,'group','co','index',1,5,NULL),(108,'group','co','add',1,5,NULL),(109,'group','co','edit',1,5,NULL),(110,'loan','income','index',1,4,NULL),(111,'loan','income','add',1,4,NULL),(112,'loan','income','edit',1,4,NULL),(113,'loan','expense','index',1,5,NULL),(114,'loan','expense','add',1,5,NULL),(115,'loan','expense','edit',1,5,NULL),(116,'loan','loantype','index',1,6,NULL),(117,'loan','loantype','add',1,6,NULL),(118,'loan','loantype','edit',1,6,NULL),(119,'loan','cancel','index',1,8,NULL),(120,'loan','cancel','add',1,8,NULL),(121,'loan','cancel','edit',1,8,NULL),(122,'loan','transferproject','index',1,7,NULL),(123,'loan','transferproject','add',1,7,NULL),(124,'loan','transferproject','edit',1,7,NULL),(125,'setting','label','index',1,0,NULL),(126,'setting','label','edit',1,0,NULL),(127,'setting','database','index',1,1,NULL),(128,'setting','setting','index',1,1,NULL),(129,NULL,'index','changepassword',1,1,NULL),(130,'group','land','copy',1,1,NULL),(131,'group','propertiestype','edit',1,1,NULL),(132,'group','project','copy',1,1,NULL),(133,'loan','index','index',1,1,NULL),(134,'report','loan','index',0,1,NULL),(135,'rsvacl','index','index',1,1,NULL),(136,'rsvacl','index','add',1,1,NULL),(137,'report','paramater','rpt-staff',1,1,'LIST_STAFF'),(139,'report','paramater','rpt-properties',1,3,'LIST_PROPERTY'),(140,'report','paramater','rpt-cancel-sale',1,4,'CANCEL_REPORT'),(141,'report','paramater','rpt-sale-history',1,5,'REPORT_SALE_HISTORY'),(142,'report','paramater','rpt-commission-staff',1,6,'REPORT_COMISSION'),(143,'report','loan','rpt-loan-late',1,7,'LOAN_LATE'),(144,'report','loan','rpt-loancollect',1,8,'REPORT_LOAN_COLLECT'),(145,'report','loan','rpt-payment',1,9,'RPT_CLIENT_PAYMENT'),(146,'report','loan','rpt-payment-history',1,10,'REPORT_LOAN_PAYMENT'),(147,'report','paramater','rpt-income',1,11,'REPORT_INCOME'),(148,'report','paramater','rpt-expense',1,12,'REPORT_EXPENSE'),(150,'report','paramater','rpt-daily-cash',1,13,'REPORT_DAILY_CASH'),(151,'report','loan','rpt-loan-expect-income',1,14,'REPORT_EXPECT_INCOME'),(152,'report','loan','rpt-loan-outstanding',1,15,'LOAN_OUTSTADING'),(153,'report','loan','rpt-sold',1,7,'LOAN_DISBURSE'),(155,'dailywork','index','index',1,1,NULL),(156,'dailywork','project','index',1,1,NULL),(157,'dailywork','category','index',1,1,NULL),(158,'dailywork','workcomplete','index',1,1,NULL),(159,'dailywork','contact','index',1,1,NULL),(160,'report','loan','receipt',0,1,'RECEIPT'),(161,'report','paramater','rpt-agreement',0,1,'AGREEMENT'),(162,'report','loan','rpt-paymentschedules',0,1,NULL),(163,'report','groupmember','rpt-client',1,2,'LIST_OF_CLIENT'),(164,'report','loan','rpt-loan-payoff',1,1,'REPORT_LOAN_PAYOFF'),(165,'property','index','index',1,NULL,'PROPERTY_LIST'),(166,'property','index','add',1,NULL,'ADD_PROPERTY'),(167,'property','index','edit',1,NULL,'EDIT_PROPERTY'),(168,'property','customers','index',1,NULL,'LIST_CUSTOMER'),(169,'property','customers','add',1,NULL,'ADD_CUSTOMER'),(170,'property','customers','edit',1,NULL,'EDIT_CUSTOMER'),(171,'property','blog','index',1,NULL,'LIST_BLOG'),(172,'property','blog','add',1,NULL,'ADD_BLOG'),(173,'property','blog','edit',1,NULL,'EDIT_BLOG'),(174,'property','rent','index',1,NULL,'LIST_RENT'),(175,'property','rent','add',1,NULL,'ADD_RENT'),(176,'property','rent','edit',1,NULL,'EDIT_RENT'),(177,'property','sale','index',1,NULL,'LIST_SALE'),(178,'property','sale','add',1,NULL,'ADD_SALE'),(179,'property','sale','edit',1,NULL,'EDIT_SALE'),(180,'group','contractor','index',1,NULL,'LIST_CONTRACTOR'),(181,'group','contractor','add',1,NULL,'ADD_CONTRACTOR'),(182,'group','contractor','edit',1,NULL,'EDIT_CONTRACTOR'),(183,'report','paramater','rpt-income-rent-sale',1,17,'REPORT_RENT_SALE_INC'),(184,'report','paramater','rpt-book-expire',1,16,'REPORT_BOOKING_EXPIR');

/*Table structure for table `rms_acl_user_access` */

DROP TABLE IF EXISTS `rms_acl_user_access`;

CREATE TABLE `rms_acl_user_access` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `acl_id` int(11) NOT NULL,
  `user_type_id` int(11) NOT NULL,
  `status` tinyint(1) DEFAULT '1',
  `user_id` int(11) DEFAULT NULL COMMENT 'user for access permission',
  PRIMARY KEY (`id`),
  KEY `user_type_id` (`user_type_id`),
  KEY `acl_id` (`acl_id`)
) ENGINE=InnoDB AUTO_INCREMENT=310 DEFAULT CHARSET=utf8;

/*Data for the table `rms_acl_user_access` */

insert  into `rms_acl_user_access`(`id`,`acl_id`,`user_type_id`,`status`,`user_id`) values (7,5,1,1,NULL),(8,6,1,1,NULL),(9,7,1,1,NULL),(10,8,1,1,NULL),(11,9,1,1,NULL),(12,10,1,1,NULL),(13,11,1,1,NULL),(14,12,1,1,NULL),(15,13,1,1,NULL),(16,14,1,1,NULL),(27,10,2,1,NULL),(28,11,2,1,NULL),(29,12,2,1,NULL),(30,13,2,1,NULL),(31,14,2,1,NULL),(32,15,2,1,NULL),(33,19,2,1,NULL),(34,20,2,1,NULL),(35,21,2,1,NULL),(36,22,2,1,NULL),(37,23,2,1,NULL),(38,24,2,1,NULL),(39,19,3,1,NULL),(40,20,3,1,NULL),(41,21,3,1,NULL),(44,27,1,1,NULL),(45,28,1,1,NULL),(46,29,1,1,NULL),(47,25,2,1,NULL),(48,26,2,1,NULL),(49,27,2,1,NULL),(50,28,2,1,NULL),(51,29,2,1,NULL),(52,25,4,1,NULL),(53,26,4,1,NULL),(54,27,4,1,NULL),(55,28,5,1,NULL),(56,25,5,1,NULL),(57,29,4,1,NULL),(58,30,1,1,NULL),(59,31,1,1,NULL),(60,32,1,1,NULL),(61,33,1,1,NULL),(62,33,2,1,NULL),(63,32,2,1,NULL),(64,31,2,1,NULL),(65,30,2,1,NULL),(66,22,3,1,NULL),(67,23,3,1,NULL),(68,31,3,1,NULL),(69,24,3,1,NULL),(70,33,3,1,NULL),(71,32,4,1,NULL),(72,30,4,1,NULL),(73,37,1,1,NULL),(74,38,1,1,NULL),(112,4,1,1,NULL),(114,3,1,1,NULL),(115,2,1,1,NULL),(138,59,1,1,NULL),(140,57,1,1,NULL),(147,50,1,1,NULL),(148,49,1,1,NULL),(149,48,1,1,NULL),(150,47,1,1,NULL),(151,46,1,1,NULL),(152,45,1,1,NULL),(153,44,1,1,NULL),(154,43,1,1,NULL),(155,42,1,1,NULL),(159,36,1,1,NULL),(160,35,1,1,NULL),(161,34,1,1,NULL),(167,86,1,1,NULL),(169,87,1,1,NULL),(170,82,1,1,NULL),(171,85,1,1,NULL),(172,83,1,1,NULL),(173,84,1,1,NULL),(177,55,1,1,NULL),(178,56,1,1,NULL),(179,58,1,1,NULL),(180,60,1,1,NULL),(181,61,1,1,NULL),(182,62,1,1,NULL),(183,64,1,1,NULL),(184,81,1,1,NULL),(185,80,1,1,NULL),(186,78,1,1,NULL),(187,79,1,1,NULL),(188,77,1,1,NULL),(189,76,1,1,NULL),(190,75,1,1,NULL),(191,74,1,1,NULL),(192,73,1,1,NULL),(193,72,1,1,NULL),(194,71,1,1,NULL),(195,70,1,1,NULL),(196,69,1,1,NULL),(197,68,1,1,NULL),(198,65,1,1,NULL),(199,66,1,1,NULL),(200,67,1,1,NULL),(201,88,1,1,NULL),(202,40,1,1,NULL),(203,41,1,1,NULL),(204,39,1,1,NULL),(205,63,1,1,NULL),(207,51,1,1,NULL),(208,89,1,1,NULL),(209,90,1,1,NULL),(210,91,1,1,NULL),(212,92,1,1,NULL),(213,93,1,1,NULL),(214,94,1,1,NULL),(219,1,1,1,NULL),(220,52,1,1,NULL),(221,53,1,1,NULL),(222,54,1,1,NULL),(223,128,1,1,NULL),(224,127,1,1,NULL),(225,126,1,1,NULL),(226,125,1,1,NULL),(227,124,1,1,NULL),(228,123,1,1,NULL),(229,122,1,1,NULL),(230,121,1,1,NULL),(231,120,1,1,NULL),(232,119,1,1,NULL),(233,118,1,1,NULL),(234,117,1,1,NULL),(235,116,1,1,NULL),(236,115,1,1,NULL),(237,114,1,1,NULL),(238,113,1,1,NULL),(239,111,1,1,NULL),(240,110,1,1,NULL),(241,109,1,1,NULL),(242,112,1,1,NULL),(243,108,1,1,NULL),(244,107,1,1,NULL),(245,106,1,1,NULL),(246,105,1,1,NULL),(247,104,1,1,NULL),(248,103,1,1,NULL),(249,102,1,1,NULL),(250,101,1,1,NULL),(251,100,1,1,NULL),(252,99,1,1,NULL),(253,98,1,1,NULL),(254,97,1,1,NULL),(255,96,1,1,NULL),(256,129,1,1,NULL),(257,130,1,1,NULL),(258,131,1,1,NULL),(259,132,1,1,NULL),(260,133,1,1,NULL),(261,134,1,1,NULL),(262,135,1,1,NULL),(263,136,1,1,NULL),(264,137,1,1,NULL),(265,138,1,1,NULL),(266,139,1,1,NULL),(267,140,1,1,NULL),(268,141,1,1,NULL),(269,142,1,1,NULL),(270,143,1,1,NULL),(271,144,1,1,NULL),(272,145,1,1,NULL),(273,146,1,1,NULL),(274,147,1,1,NULL),(275,148,1,1,NULL),(276,149,1,1,NULL),(277,151,1,1,NULL),(278,150,1,1,NULL),(279,152,1,1,NULL),(280,153,1,1,NULL),(281,155,1,1,NULL),(282,159,1,1,NULL),(283,158,1,1,NULL),(284,156,1,1,NULL),(285,157,1,1,NULL),(286,160,1,1,NULL),(287,161,1,1,NULL),(288,162,1,1,NULL),(289,163,1,1,NULL),(290,165,1,1,NULL),(291,166,1,1,NULL),(292,167,1,1,NULL),(293,169,1,1,NULL),(294,168,1,1,NULL),(295,170,1,1,NULL),(296,171,1,1,NULL),(297,172,1,1,NULL),(298,173,1,1,NULL),(299,174,1,1,NULL),(300,175,1,1,NULL),(301,176,1,1,NULL),(302,177,1,1,NULL),(303,178,1,1,NULL),(304,179,1,1,NULL),(305,182,1,1,NULL),(306,181,1,1,NULL),(307,180,1,1,NULL),(308,183,1,1,NULL),(309,184,1,1,NULL);

/*Table structure for table `rms_acl_user_type` */

DROP TABLE IF EXISTS `rms_acl_user_type`;

CREATE TABLE `rms_acl_user_type` (
  `user_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_type` varchar(50) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`user_type_id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

/*Data for the table `rms_acl_user_type` */

insert  into `rms_acl_user_type`(`user_type_id`,`user_type`,`parent_id`,`status`) values (1,'Admininstrator',0,1),(2,'Accountant',1,1),(3,'Sale Officer',2,1);

/*Table structure for table `rms_users` */

DROP TABLE IF EXISTS `rms_users`;

CREATE TABLE `rms_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(128) DEFAULT NULL,
  `last_name` varchar(128) DEFAULT NULL,
  `user_name` varchar(128) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `user_type` tinyint(1) DEFAULT '0' COMMENT '0: transfer; 1:admin',
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

/*Data for the table `rms_users` */

insert  into `rms_users`(`id`,`first_name`,`last_name`,`user_name`,`password`,`user_type`,`active`) values (1,'channy','mok','admin','e10adc3949ba59abbe56e057f20f883e',1,1),(2,'គីមសឿន','កិ់ៈ','soeurn','5f4dcc3b5aa765d61d8327deb882cf99',2,1),(3,'dara','dara','dara','e10adc3949ba59abbe56e057f20f883e',1,0);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
