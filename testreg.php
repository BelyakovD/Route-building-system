<?php
session_start();
		  
if (isset($_POST['login'])) { $login = $_POST['login']; if ($login == '') { unset($login);} }
if (isset($_POST['password'])) { $password=$_POST['password']; if ($password =='') { unset($password);} }

if (empty($login) or empty($password)) 
	exit ("Вы ввели не всю информацию, вернитесь назад и заполните все поля!"); 

include ("bd.php");

$password = md5($password);
$result = mysqli_query($db,"SELECT * FROM users WHERE login='$login' AND password='$password'"); 

$myrow = mysqli_fetch_array($result);
if (empty($myrow['id']))
	exit ("Извините, введённый вами логин или пароль неверный."); 
else {
		$_SESSION['password']=$myrow['password']; 
	  	$_SESSION['login']=$myrow['login']; 
      	$_SESSION['id']=$myrow['id'];
}	

echo "<html><head><meta http-equiv='Refresh' content='0; URL=index.php'></head></html>";


?>