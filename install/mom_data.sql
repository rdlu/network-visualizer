/*
SQLyog Ultimate v9.51 
MySQL - 5.1.61-0ubuntu0.11.10.1 : Database - mom_dev
*********************************************************************
*/


/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`mom_dev` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `mom_dev`;

/*Data for the table `metrics` */

insert  into `metrics`(`id`,`name`,`plugin`,`desc`,`profile_id`,`reverse`,`order`) values (1,'throughput','throughput','Vazão de dados',2,0,1),(2,'jitter','jitter','Flutuação na latência',1,1,5),(3,'capacity','bandwidth','Capacidade no gargalo (experimental)',2,0,9),(4,'loss','loss','Perda de pacotes transmitidos ',1,1,4),(5,'owd','owd','Latência unidirecional',1,1,7),(6,'mos','mos','Mean Opinion Score',1,0,8),(7,'pom','pom','Pacotes fora de ordem',1,1,6),(8,'rtt','rtt','Latência ida e volta',1,1,3),(9,'throughputTCP','throughput_tcp','Vazão de dados sob TCP',3,0,2);

/*Data for the table `profiles` */

insert  into `profiles`(`id`,`name`,`polling`,`count`,`probeCount`,`probeSize`,`gap`,`qosType`,`qosValue`,`timeout`,`protocol`,`description`,`status`) values (1,'RTT',300,100,1,100,100,0,0,6,0,'Perfil exclusivo para testes de latência de resposta.',1),(2,'ThroughputUDP',300,10,40,500,2000,0,0,5,0,'Perfil definido exclusivamente para medição de vazão de dados via UDP.',1),(3,'throughputTCP',300,1,600,1488,100,0,0,15,1,'blah!',1);

/*Data for the table `roles` */

insert  into `roles`(`id`,`name`,`description`) values (1,'admin','Administrador'),(2,'login','Usuario que pode se logar'),(3,'config','Usuário que pode visualizar dados e configurar o sistema');

/*Data for the table `thresholdprofiles` */

insert  into `thresholdprofiles`(`id`,`name`,`desc`) values (1,'3G default',NULL);

/*Data for the table `thresholdvalues` */

insert  into `thresholdvalues`(`thresholdprofile_id`,`metric_id`,`min`,`max`,`id`) values (1,1,2457600,6553600,1),(1,2,0.1,0.04,3),(1,4,20,5,5),(1,5,0.125,0.075,6),(1,6,1,4,7),(1,7,10,5,8),(1,8,0.25,0.15,9),(1,9,2457600,6553600,10);

/*Data for the table `users` */

insert  into `users`(`id`,`email`,`username`,`password`,`last_password`,`logins`,`last_login`,`last_pchange`,`active`) values (3,'admin@netmetric.com','admin','7eae0ffa48f709f72c88c4fc62a3407a03a03e1a88f8de5fea5e2a8559c1705f',NULL,373,1331232038,NULL,1);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
