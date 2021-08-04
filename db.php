<?php
require_once "config.php";

$dbLink = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName)
    or die("Error: ".mysqli_connect_error());
mysqli_query($dbLink, "SET CHARACTER SET 'utf8'");

if(!$dbLink) {
	echo "Не удалось подключится к серверу";
}else{
$sql = mysqli_query($dbLink, "CREATE TABLE IF NOT EXISTS `discipline` (
  `id_discipline` int(11) NOT NULL AUTO_INCREMENT,
  `name_discipline` text NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_discipline`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

$sql = mysqli_query($dbLink, "CREATE TABLE IF NOT EXISTS `groups` (
  `id_group` int(11) NOT NULL AUTO_INCREMENT,
  `name_group` int(3) NOT NULL,
  PRIMARY KEY (`id_group`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");


$sql = mysqli_query($dbLink, "CREATE TABLE IF NOT EXISTS `lesson` (
  `id_lesson` int(11) NOT NULL AUTO_INCREMENT,
  `date_lesson` text NOT NULL,
  `control1` text NOT NULL,
  `control2` text NOT NULL,
  `statement_id` int(11) NOT NULL,
  `theme_id` int(11) NOT NULL,
  PRIMARY KEY (`id_lesson`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

$sql = mysqli_query($dbLink, "CREATE TABLE IF NOT EXISTS `record` (
  `id_record` int(11) NOT NULL AUTO_INCREMENT,
  `mark1` text NOT NULL,
  `mark2` text NOT NULL,
  `lesson_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  PRIMARY KEY (`id_record`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");


$sql = mysqli_query($dbLink, "CREATE TABLE IF NOT EXISTS `role` (
  `id_role` int(11) NOT NULL AUTO_INCREMENT,
  `name_role` varchar(15) NOT NULL,
  PRIMARY KEY (`id_role`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;");

$sql = mysqli_query($dbLink, "INSERT INTO `role` (`id_role`, `name_role`) VALUES
(1, 'admin'),
(2, 'moderator'),
(3, 'teacher');");

$sql = mysqli_query($dbLink, "CREATE TABLE IF NOT EXISTS `statement` (
  `id_statement` int(11) NOT NULL AUTO_INCREMENT,
  `date_open` date NOT NULL,
  `date_close` date NOT NULL,
  `discipline_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  PRIMARY KEY (`id_statement`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

$sql = mysqli_query($dbLink, "CREATE TABLE IF NOT EXISTS `student` (
  `id_student` int(11) NOT NULL AUTO_INCREMENT,
  `hash_student` varchar(32) NOT NULL,
  `surname_student` text NOT NULL,
  `name_student` text NOT NULL,
  `patronymic_student` text NOT NULL,
  `group_id` int(11) NOT NULL,
  `login_student` varchar(30) NOT NULL,
  `password_student` varchar(100) NOT NULL,
  PRIMARY KEY (`id_student`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

$sql = mysqli_query($dbLink, "CREATE TABLE IF NOT EXISTS `user` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `hash_user` varchar(32) NOT NULL,
  `login_user` varchar(30) NOT NULL,
  `password_user` varchar(100) NOT NULL,
  `surname_user` text NOT NULL,
  `name_user` text NOT NULL,
  `patronymic_user` text NOT NULL,
  `role_id` int(11) NOT NULL,
  PRIMARY KEY (`id_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

$sql = mysqli_query($dbLink, "INSERT INTO `user` (`id_user`, `hash_user`, `login_user`, `password_user`, `surname_user`, `name_user`, `patronymic_user`, `role_id`) VALUES
(1, '', '".$adminLogin."', '".md5(md5($adminPass))."', '', 'admin', '', '1');");

$sql = mysqli_query($dbLink, "CREATE TABLE IF NOT EXISTS `theme` (
  `id_theme` int(11) NOT NULL AUTO_INCREMENT,
  `name_theme` text NOT NULL,
  `discipline_id` int(11) NOT NULL,
  PRIMARY KEY (`id_theme`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
}
?>