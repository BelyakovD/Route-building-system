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
$myrow = mysqli_fetch_array($result);
}
?>
<html>
<head>
	<title>Категории</title>
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
          <li class="nav-item active dropdown">
             <a class="nav-link dropdown-toggle" href="category.php?id=1" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
               Категории достопримечательностей<span class="sr-only">(current)</span>
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
$query = mysqli_query($db,"SELECT * FROM category WHERE nodeId='$id' ORDER BY name"); 
$category = mysqli_fetch_array($query);
if (isset($category['id'])) printf("<h3>Выберите подкатегорию</h3>");
do
  {
    printf("<a href='category.php?id=%s'>%s</a><br>",$category['id'],$category['name']);
  }
while($category = mysqli_fetch_array($query));

$query = mysqli_query($db,"SELECT * FROM showplace WHERE categoryId='$id' ORDER BY name"); 
$showplaces = mysqli_fetch_array($query);
if (isset($showplaces['id'])) printf("<h3>Достопримечательности этой категории</h3><br>");
do
  {
    printf("<a class='btn btn-outline-info' href='showplace.php?id=%s'>%s</a><br><br>",$showplaces['id'],$showplaces['name']);
  }
while($showplaces = mysqli_fetch_array($query));
?></div>

</div>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>

</html>