<?php
include ("bd.php"); 

if (isset($_POST['showplaceId'])) { $id = $_POST['showplaceId'];}
if (isset($_POST['text'])) { $text = $_POST['text'];}
if (isset($_POST['nodeId'])) { $nodeId = $_POST['nodeId'];}

if (empty($text)) {
exit ("Вы не ввели текст отзыва, вернитесь назад и введите его");}

$query = mysqli_query($db,"INSERT INTO comment (content, nodeId, showplaceId) VALUES ('$text','$nodeId','$id')");

echo "<html><head><meta http-equiv='Refresh' content='0; URL=showplace.php?id=".$id."'></head></html>";
?>