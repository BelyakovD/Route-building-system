<?php

session_start();

include ("bd.php");
if (isset($_GET['id'])) {$id =$_GET['id']; } 
else
{ exit("Вы зашли на страницу без параметра!");} 
if (!preg_match("|^[\d]+$|", $id)) {
exit("<p>Неверный формат запроса! Проверьте URL</p>");
}
if (!empty($_SESSION['login']) and !empty($_SESSION['password']))
{

$login = $_SESSION['login'];
$password = $_SESSION['password'];
$result = mysqli_query($db,"SELECT id FROM users WHERE login='$login' AND password='$password'"); 
$myrow = mysqli_fetch_array($result);}
?>

<?php
function recursion($deep, $node, $db)
{
	$deep++;
	$query2 = mysqli_query($db,"SELECT * FROM comment WHERE nodeId='$node'");
	$comment[$deep] = mysqli_fetch_array($query2);
	do{
  			if (isset($comment[$deep]['id'])) {
  				$marg = 50 * $deep;
  $radio = $comment[$deep]['id'];
    printf(" <div style=' margin-left:$marg'>
		  <p class='text-right'>%s</p>
		  <p class='lead'>%s</p>
		  <p><input name='nodeId' type='radio' value='$radio'>Ответить</p>
  ",$comment[$deep]['creationTime'],$comment[$deep]['content']);
	  if (!empty($_SESSION['login']) and !empty($_SESSION['password']))
		{printf("
			  <p><a class='btn btn-outline-danger' href='drop_post.php?id=%s'>Удалить</a></p>",$comment[$deep]['id']);
		}
	printf("</div>");
		recursion($deep, $comment[$deep]['id'], $db);
	}}
	while($comment[$deep] = mysqli_fetch_array($query2));  	
  	$deep--;
}
?>

<html>
<head>
	<title>Страница достопримечательности</title>
	<meta charset="utf-8" />
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body style="background-color:#FFFFE1">
<div class="container" style="padding: 0px; background-color:#FAFFFF">
	<nav class="navbar navbar-expand-lg navbar-light" style="background-color: #e3f2fd;">
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarTogglerDemo02">
        <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
          <li class="nav-item ">
            <a class="nav-link" href="index.php" style="margin-right: 10px">Главная</a>
          </li>          
          <li class="nav-item dropdown">
             <a class="nav-link dropdown-toggle" href="category.php?id=1" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
               Категории достопримечательностей
             </a>
             <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
               <a class="dropdown-item" href="category.php?id=3">Памятники</a>
               <a class="dropdown-item" href="category.php?id=4">Музеи</a>
               <a class="dropdown-item" href="category.php?id=5">Религиозные места</a>
               <a class="dropdown-item" href="category.php?id=6">Парки</a>
               <a class="dropdown-item" href="category.php?id=7">Театры</a>
             </div>
          </li>
        </ul><?php if (!isset($myrow['id']) or $myrow['id']==0) {print <<<HERE
        <form class="form-inline my-2 my-lg-0" action="testreg.php" method="post">
          <input class="form-control mr-sm-2" name="login" type="text" size="15" maxlength="15" placeholder="Логин">
          <input class="form-control mr-sm-2" name="password" type="password" size="15" maxlength="15" placeholder="Пароль">
          <button class="btn btn-outline-primary my-2 my-sm-0" type="submit" name="submit">Вход для администратора</button>
        </form>
HERE;
}
else {
print <<<HERE
Вы вошли на сайт, как $_SESSION[login]
<a style="margin-left: 15px" class="btn btn-outline-primary" role="button" href='exit.php'>Выход</a>
HERE;
}?>
      </div>
    </nav><div class="text-center">
<?php
$query = mysqli_query($db,"SELECT * FROM showplace WHERE id='$id'"); 
$showplace = mysqli_fetch_array($query);
$showplaceName = $showplace['name'];
$showplaceDescription = $showplace['description'];
$showplaceLatitude = $showplace['latitude'];
$showplaceLongitude = $showplace['longitude'];

printf("<h2>%s</h2>",$showplace['name']);
printf("<p>%s</p>",$showplace['description']);
$query = mysqli_query($db,"SELECT * FROM image WHERE showplaceId='$id'"); 
$img = mysqli_fetch_array($query);
if (isset($img['id'])) printf("<h3>Изображения достопримечательности</h3>");
do
  {
    printf("<img src='$img[source]'><br>");
  }
while($img = mysqli_fetch_array($query));
$deep = 0;
$query = mysqli_query($db,"SELECT * FROM comment WHERE showplaceId='$id'"); 
$comment[$deep] = mysqli_fetch_array($query);

printf("<form action='post.php' method='post'>");
if (isset($comment[$deep]['id'])) {
printf("<br><h3>Отзывы</h3></div>");
do{	
	if ($comment[$deep]['nodeId'] == 0){
  $marg = 50 * $deep;	
  $radio = $comment[$deep]['id'];
  printf("<div style='margin-left:$marg'>
		  <p class='text-right'>%s</p>
		  <p class='lead'>%s</p>
		  <p><input name='nodeId' type='radio' value='$radio'>Ответить</p>
  ",$comment[$deep]['creationTime'],$comment[$deep]['content']);
  if (!empty($_SESSION['login']) and !empty($_SESSION['password']))
	{printf("
		  <p><a class='btn btn-outline-danger' href='drop_post.php?id=%s'>Удалить</a></p>",$comment[$deep]['id']);
	}
	recursion($deep, $comment[$deep]['id'], $db);							
}}
while($comment[$deep] = mysqli_fetch_array($query));
}
?>
</div>
<?php
print <<<HERE
<br><hr>
<h2>Написать отзыв</h2>
<textarea class="form-control" cols='75' rows='4' name='text'></textarea>
<input type='hidden' name='showplaceId' value='$id'>
<p><input name='nodeId' type='radio' value='0'>Не отвечать</p>
<input class='btn btn-primary' type='submit' name='submit' value='Готово'>
</form><br>
HERE;

if (!empty($_SESSION['login']) and !empty($_SESSION['password']))
{
print <<<HERE
<br><hr>
<h2>Редактирование данных</h2>

<form action='update_showplace.php' method='post'>
<div class="form-group">
<label for='changeName'>Название</label>
<input class='form-control' id='changeName' name='name' type='text' size='40' value = '$showplaceName'></div>
<input type='hidden' name='showplaceId' value='$id'>
<input class='btn btn-primary' type='submit' name='submit' value='Изменить'>
</form>

<form action='update_showplace.php' method='post'>
<div class="form-group">
<label for='changeDescription'>Описание</label>
<textarea class='form-control' id='changeDescription' cols='100' rows='5' name='description' >$showplaceDescription</textarea></div>
<input class='btn btn-primary' type='submit' name='submit' value='Изменить'>
<input type='hidden' name='showplaceId' value='$id'>
</form>

<form action='update_showplace.php' method='post'>
<div class="form-group">
<label for='changeLatitude'>Широта</label>
<input class='form-control' id='changeLatitude' type="text" name="latitude" value = '$showplaceLatitude'></div>
<input class='btn btn-primary' type='submit' name='submit' value='Изменить'>
<input type='hidden' name='showplaceId' value='$id'>
</form>

<form action='update_showplace.php' method='post'>
<div class="form-group">
<label for='changeLongitude'>Долгота</label>
<input class='form-control' id='changeLongitude' type="text" name="longitude" value = '$showplaceLongitude'></div>
<input class='btn btn-primary' type='submit' name='submit' value='Изменить'>
<input type='hidden' name='showplaceId' value='$id'>
</form>

HERE;

}

?></div>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>

</html>
