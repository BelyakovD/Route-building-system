<?php
session_start();
include ("bd.php");

if (!empty($_SESSION['login']) and !empty($_SESSION['password']))
{

$login = $_SESSION['login'];
$password = $_SESSION['password'];
$result = mysqli_query($db,"SELECT id FROM users WHERE login='$login' AND password='$password'"); 
$myrow = mysqli_fetch_array($result); 
if (empty($myrow['id']))
   {
   
    exit("Это действие разрешено только для администратора. Выполните вход.");
   }
}
else {

exit("Это действие разрешено только для администратора. Выполните вход."); }

if (isset($_GET['id'])) { $id = $_GET['id'];}

$query = mysqli_query($db,"SELECT * FROM comment WHERE id='$id'"); 
$comment = mysqli_fetch_array($query);
$showplaceId = $comment['showplaceId'];

$query = mysqli_query ($db,"DELETE FROM comment WHERE id = '$id' LIMIT 1");
if ($query == 'true') {
echo "<html><head><meta http-equiv='Refresh' content='0; URL=showplace.php?id=".$showplaceId."'></head></html>";
}
else {
echo "<html><head><meta http-equiv='Refresh' content='2; URL=showplace.php?id=".$showplaceId."'></head><body>Ошибка! Отзыв не удален. Возврат через 2 сек.</body></html>"; }

?>