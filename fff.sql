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

/*Table structure for table `preferred_communication` */

DROP TABLE IF EXISTS `preferred_communication`;

CREATE TABLE `preferred_communication` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `feedback` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

/*Data for the table `preferred_communication` */

insert  into `preferred_communication`(`id`,`feedback`) values (1,'Мобильный телефон'),(2,'Домашний телефон'),(3,'Рабочий телефон'),(4,'Эл. почта');

/*Table structure for table `profile` */

DROP TABLE IF EXISTS `profile`;

CREATE TABLE `profile` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `registered_user` enum('yes','no') NOT NULL DEFAULT 'no',
  `photo` varchar(100) NOT NULL DEFAULT 'no-photo.png',
  `surname` varchar(100) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `patronymic` varchar(100) DEFAULT NULL,
  `birth` varchar(50) DEFAULT '--',
  `sex` enum('Мужской','Женский') NOT NULL DEFAULT 'Мужской',
  `city` varchar(100) NOT NULL,
  `move` enum('no','yes','desirable') NOT NULL DEFAULT 'no',
  `trip` enum('never','ready','sometimes') NOT NULL DEFAULT 'never',
  `nationality` varchar(100) DEFAULT NULL,
  `work_permit` varchar(100) DEFAULT NULL,
  `travel_time_work` enum('Не имеет значения','Не более часа','Не более полутора часов') NOT NULL DEFAULT 'Не имеет значения',
  `preferred_communication` enum('1','2','3','4') NOT NULL DEFAULT '4',
  `mobile_phone` varchar(100) NOT NULL,
  `home_phone` varchar(100) DEFAULT NULL,
  `work_phone` varchar(100) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `comment_mobile_phone` varchar(100) DEFAULT NULL,
  `comment_home_phone` varchar(100) DEFAULT NULL,
  `comment_work_phone` varchar(100) DEFAULT NULL,
  `icq` varchar(100) DEFAULT NULL,
  `skype` varchar(100) DEFAULT NULL,
  `free_lance` varchar(100) DEFAULT NULL,
  `my_circle` varchar(100) DEFAULT NULL,
  `linkedln` varchar(100) DEFAULT NULL,
  `facebook` varchar(100) DEFAULT NULL,
  `live_journal` varchar(100) DEFAULT NULL,
  `other_site` varchar(100) DEFAULT NULL,
  `desired_position` varchar(100) NOT NULL,
  `professional_area` varchar(100) NOT NULL,
  `salary` varchar(100) DEFAULT NULL,
  `currency` enum('бел. руб.','рос. руб','EUR','USD') NOT NULL DEFAULT 'бел. руб.',
  `employment` varchar(100) NOT NULL,
  `schedule` varchar(100) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

/*Data for the table `profile` */

insert  into `profile`(`id`,`registered_user`,`photo`,`surname`,`first_name`,`patronymic`,`birth`,`sex`,`city`,`move`,`trip`,`nationality`,`work_permit`,`travel_time_work`,`preferred_communication`,`mobile_phone`,`home_phone`,`work_phone`,`email`,`comment_mobile_phone`,`comment_home_phone`,`comment_work_phone`,`icq`,`skype`,`free_lance`,`my_circle`,`linkedln`,`facebook`,`live_journal`,`other_site`,`desired_position`,`professional_area`,`salary`,`currency`,`employment`,`schedule`,`date`) values (1,'no','no-photo.png','Сергей','',NULL,'--','Мужской','','no','never','Беларусь','Беларусь','Не имеет значения','1','','','','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'','',NULL,'бел. руб.','','','2014-10-07 12:57:09'),(2,'no','no-photo.png','Вася','',NULL,'--','Мужской','','no','never','Беларусь','Беларусь','Не имеет значения','1','','','','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'','',NULL,'бел. руб.','','','2014-10-07 12:57:08'),(3,'no','no-photo.png','jghj','ijihuihuh','цукк','--','Женский','uujhrty','desirable','never','Беларусь','Беларусь','Не более полутора часов','1','','','','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'','',NULL,'бел. руб.','','','2014-10-07 12:11:38'),(4,'no','4.jpg','15jio','Сашка','Какашка','31-2-2000','Женский','жхзгшоргпо','desirable','sometimes','other','Беларусь','Не имеет значения','1','','','','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'','',NULL,'бел. руб.','','','2014-10-07 11:04:46'),(5,'no','no-photo.png','htryjy','regreg','efewtfrewte','2-2-1998','Мужской','ertretertre','no','never','erwfgreg','ergerger','Не имеет значения','1','','','','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'','',NULL,'бел. руб.','','','2014-10-07 13:08:08'),(6,'no','no-photo.png','укуцкуцк','уцкуцкуц','pppкцукцукуцк','4-2-1999','Мужской','Минск','no','ready','цукуцкуц','цаукпкуп','Не имеет значения','1','','','','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'','',NULL,'бел. руб.','','','2014-10-07 17:10:51'),(7,'no','no-photo.png','wefewfwef','цукауцас','fwefewfewfwefw','3-7-1991','Мужской','Минск','no','ready','Беларусь','Беларусь','Не имеет значения','1','','','','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'','',NULL,'бел. руб.','','','2014-10-07 22:01:46'),(8,'no','no-photo.png','','',NULL,'--','Мужской','','no','never',NULL,NULL,'Не имеет значения','1','111111111','3453453453','375447108588','telpuk.sergey@gmail.com','sdfdsfsdfdsfds','45435435345','534543543','345435435345','435345','3454353','45345','dfgfdhf','345435345','3453454353','43543534534','sdfsdf','sdfsd','5656','бел. руб.','Проектная/Временная работа','Сменный график','2014-10-08 16:09:35'),(9,'no','no-photo.png','','',NULL,'--','Мужской','','no','never',NULL,NULL,'Не имеет значения','4','',NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'','',NULL,'бел. руб.','','','2014-10-08 14:06:45');

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
