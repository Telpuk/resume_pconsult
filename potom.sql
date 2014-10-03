/*
SQLyog Ultimate v11.52 (64 bit)
MySQL - 5.5.38-0ubuntu0.14.04.1 : Database - resume_pconsult
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`resume_pconsult` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `resume_pconsult`;

/*Table structure for table `profile` */

DROP TABLE IF EXISTS `profile`;

CREATE TABLE `profile` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `registered_user` enum('yes','no') NOT NULL DEFAULT 'no',
  `photo` varchar(100) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

/*Data for the table `profile` */

insert  into `profile`(`id`,`registered_user`,`photo`,`date`) values (1,'no',NULL,'2014-10-02 12:07:08'),(2,'no',NULL,'2014-10-02 12:08:44'),(3,'no',NULL,'2014-10-02 18:42:40'),(4,'no',NULL,'2014-10-02 18:45:01'),(5,'no',NULL,'2014-10-02 19:18:49'),(6,'no',NULL,'2014-10-03 08:21:43'),(7,'no',NULL,'2014-10-03 11:52:59'),(8,'no','8.png','2014-10-03 13:36:29'),(9,'no','9.png','2014-10-03 14:25:12');

/*Table structure for table `users_access` */

DROP TABLE IF EXISTS `users_access`;

CREATE TABLE `users_access` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `permission` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

/*Data for the table `users_access` */

insert  into `users_access`(`id`,`permission`) values (1,'admin'),(2,'manager'),(3,'user');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
