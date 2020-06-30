<?php
session_start();
include ("bd.php");

if (!empty($_SESSION['login']) and !empty($_SESSION['password']))
{

$login = $_SESSION['login'];
$password = $_SESSION['password'];
$result2 = mysqli_query($db,"SELECT id FROM users WHERE login='$login' AND password='$password'"); 
$myrow2 = mysqli_fetch_array($result2); 
if (empty($myrow2['id']))
   {
   
    exit("Вход на эту страницу разрешен только администратору.");
   }
}
else {

exit("Вход на эту страницу разрешен только администратору."); }

$showplaceId = $_POST['showplaceId'];

if (isset($_POST['name']))
      {
$name = $_POST['name'];

if ($name == '') { exit("Вы не ввели название.");}

$query = mysqli_query($db,"UPDATE showplace SET name='$name' WHERE id='$showplaceId'");
echo "<html><head><meta http-equiv='Refresh' content='0; URL=showplace.php?id=".$showplaceId."'></head></html>";
      } 

if (isset($_POST['description']))
      {
$description = $_POST['description'];

if ($description == '') { exit("Вы не ввели описание.");} 

$query = mysqli_query($db,"UPDATE showplace SET description='$description' WHERE id='$showplaceId'");
echo "<html><head><meta http-equiv='Refresh' content='0; URL=showplace.php?id=".$showplaceId."'></head></html>";
      } 

if (isset($_POST['latitude']))

$latitude = $_POST['latitude'];

if ($latitude == '') { exit("Вы не ввели широту.");}

$query = mysqli_query($db,"UPDATE showplace SET latitude='$latitude' WHERE id='$showplaceId'");
echo "<html><head><meta http-equiv='Refresh' content='0; URL=showplace.php?id=".$showplaceId."'></head></html>";
      } 

if (isset($_POST['longitude']))
      {
$longitude = $_POST['longitude'];

if ($longitude == '') { exit("Вы не ввели долготу.");}

$query = mysqli_query($db,"UPDATE showplace SET longitude='$longitude' WHERE id='$showplaceId'");
echo "<html><head><meta http-equiv='Refresh' content='0; URL=showplace.php?id=".$showplaceId."'></head></html>";
      }  
?>