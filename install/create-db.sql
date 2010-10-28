/**
* Criacao da tabela mom
*/

CREATE DATABASE `mom` ;

/* Desenvolvimento somente */
CREATE DATABASE `mom_dev` ;

CREATE USER 'mom'@'%' IDENTIFIED BY '***';

GRANT USAGE ON * . * TO 'mom'@'%' IDENTIFIED BY '***' WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0 ;

GRANT ALL PRIVILEGES ON `mom\_%` . * TO 'mom'@'%';
