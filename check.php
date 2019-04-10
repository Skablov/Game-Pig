<?php
	session_start();

if (isset($_POST['login']))
{
	$login = $_POST['login'];
	if(empty($login))
		unset($login);
}
if (isset($_POST['password']))
{
	$password = $_POST['password'];
	if(empty($password))
		unset($password);
}
if (empty($login) || empty($password))
{
	exit("You not enter password or login");
}
$login = stripcslashes($login);
$password = stripcslashes($password);
$login = htmlspecialchars($login);
$password = htmlspecialchars($password);

$login = trim($login);
$password = trim($password);

include("bd.php");
$result = mysql_query("SELECT * FROM user WHERE login = '$login'", $db);
$myrow = mysql_fetch_array($result);
if(empty($myrow['login']))
{
	exit ("login - not found");
}
else
{
	if($myrow['password'] == $password)
	{
		$_SESSION['id'] = $myrow['id'];
		$_SESSION['login'] = $myrow['login'];
		header("Location: /search_game.php");
		exit();
	}
	else
		exit("Wrong password or login");
}



?>
