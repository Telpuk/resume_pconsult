/*
SQLyog Ultimate v11.52 (64 bit)
MySQL - 5.5.40-0ubuntu0.14.04.1 : Database - resume_pconsult
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

/*Table structure for table `education` */

DROP TABLE IF EXISTS `education`;

CREATE TABLE `education` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `level` varchar(250) NOT NULL,
  `names_institutions` varchar(250) NOT NULL,
  `faculties` varchar(250) NOT NULL,
  `specialties_specialties` varbinary(250) NOT NULL,
  `years_graduations` varchar(250) NOT NULL,
  `courses_names` varchar(250) DEFAULT NULL,
  `follow_organizations` varchar(250) DEFAULT NULL,
  `courses_specialties` varchar(250) DEFAULT NULL,
  `course_years_graduations` varchar(250) DEFAULT NULL,
  `tests_exams_names` varchar(250) DEFAULT NULL,
  `tests_exams_follow_organizations` varchar(250) DEFAULT NULL,
  `tests_exams_specialty` varchar(250) DEFAULT NULL,
  `tests_exams_years_graduations` varchar(250) DEFAULT NULL,
  `electronic_certificates_names` varchar(250) DEFAULT NULL,
  `electronic_certificates_years_graduations` varchar(250) DEFAULT NULL,
  `electronic_certificates_links` varchar(250) DEFAULT NULL,
  `native_language` varchar(250) NOT NULL DEFAULT 'Белорусский',
  `language_english` varchar(250) NOT NULL,
  `language_germany` varchar(250) NOT NULL,
  `language_french` varchar(250) NOT NULL,
  `language_further` varchar(250) DEFAULT NULL,
  `language_further_level` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `education_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `profile` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

/*Data for the table `education` */

insert  into `education`(`id`,`id_user`,`level`,`names_institutions`,`faculties`,`specialties_specialties`,`years_graduations`,`courses_names`,`follow_organizations`,`courses_specialties`,`course_years_graduations`,`tests_exams_names`,`tests_exams_follow_organizations`,`tests_exams_specialty`,`tests_exams_years_graduations`,`electronic_certificates_names`,`electronic_certificates_years_graduations`,`electronic_certificates_links`,`native_language`,`language_english`,`language_germany`,`language_french`,`language_further`,`language_further_level`) values (1,3,'','','','','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Белорусский','','','',NULL,NULL),(2,4,'','','','','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Белорусский','','','',NULL,NULL),(3,5,'','','','','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Белорусский','','','',NULL,NULL),(4,6,'','','','','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Белорусский','','','',NULL,NULL);

/*Table structure for table `experience` */

DROP TABLE IF EXISTS `experience`;

CREATE TABLE `experience` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `organizations` varchar(250) NOT NULL,
  `regions` varchar(250) NOT NULL,
  `positions` varchar(250) NOT NULL,
  `sites` varchar(250) DEFAULT NULL,
  `field_activities` varchar(250) NOT NULL,
  `getting_starteds` varchar(250) NOT NULL,
  `closing_works` varchar(250) NOT NULL,
  `at_the_moments` varchar(250) NOT NULL,
  `functions` text NOT NULL,
  `key_skills` text NOT NULL,
  `about_self` text,
  `recommend_names` varchar(250) DEFAULT NULL,
  `recommend_position` varchar(250) DEFAULT NULL,
  `recommend_organization` varchar(250) DEFAULT NULL,
  `recommend_phone` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `experience_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `profile` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

/*Data for the table `experience` */

insert  into `experience`(`id`,`id_user`,`organizations`,`regions`,`positions`,`sites`,`field_activities`,`getting_starteds`,`closing_works`,`at_the_moments`,`functions`,`key_skills`,`about_self`,`recommend_names`,`recommend_position`,`recommend_organization`,`recommend_phone`) values (1,3,'1','минск','программист','','инженер','1-1953','1-0','true','yjhvfkmyj','apache','','','','',''),(2,4,'','','',NULL,'','','','','','',NULL,NULL,NULL,NULL,NULL),(3,5,'','','',NULL,'','','','','','',NULL,NULL,NULL,NULL,NULL),(4,6,'','','',NULL,'','','','','','',NULL,NULL,NULL,NULL,NULL);

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
  `currency` enum('бел. руб.','EUR','USD','рос. руб.') NOT NULL DEFAULT 'бел. руб.',
  `employment` varchar(250) NOT NULL,
  `schedule` varchar(250) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

/*Data for the table `profile` */

insert  into `profile`(`id`,`registered_user`,`photo`,`surname`,`first_name`,`patronymic`,`birth`,`sex`,`city`,`move`,`trip`,`nationality`,`work_permit`,`travel_time_work`,`preferred_communication`,`mobile_phone`,`home_phone`,`work_phone`,`email`,`comment_mobile_phone`,`comment_home_phone`,`comment_work_phone`,`icq`,`skype`,`free_lance`,`my_circle`,`linkedln`,`facebook`,`live_journal`,`other_site`,`desired_position`,`professional_area`,`salary`,`currency`,`employment`,`schedule`,`date`) values (1,'no','no-photo.png','','',NULL,'--','Мужской','','no','never',NULL,NULL,'Не имеет значения','4','',NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'','',NULL,'бел. руб.','','','2014-10-17 14:06:04'),(3,'no','no-photo.png','','',NULL,'--','Мужской','','no','never',NULL,NULL,'Не имеет значения','4','',NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'','',NULL,'бел. руб.','','','2014-10-17 15:16:07'),(4,'no','no-photo.png','','',NULL,'--','Мужской','','no','never',NULL,NULL,'Не имеет значения','4','',NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'','',NULL,'бел. руб.','','','2014-10-17 16:03:24'),(5,'no','no-photo.png','','',NULL,'--','Мужской','','no','never',NULL,NULL,'Не имеет значения','4','',NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'','',NULL,'бел. руб.','','','2014-10-17 17:58:54'),(6,'no','no-photo.png','','',NULL,'--','Мужской','','no','never',NULL,NULL,'Не имеет значения','4','',NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'','',NULL,'бел. руб.','','','2014-10-17 18:52:34');

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
