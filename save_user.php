<?php
	if(isset($_POST['login']))
	{
		$login = $_POST['login'];
		if(empty($login))
		{
			unset($login);
		}
	}
	if(isset($_POST['password']))
	{
		$password = $_POST['password'];
		if(empty($password))
		{
			unset($password);
		}
	}
	if(empty($login) || empty($password))
		exit("You fagot enter login or password");
	$login = stripcslashes($login);
	$login = htmlspecialchars($login);
	$password = stripcslashes($password);
	$password = htmlspecialchars($password);

	$login = trim($login);
	$password = trim($password);

	include("bd.php");

	$result = mysql_query("SELECT id FROM user WHERE login = '$login'", $db);
	$myrow = mysql_fetch_array($result);
	if(!empty($myrow['id']))
	{
		exit("This login is buzy, try change your login <a href='reg.php'>register</a>");
	}
	$result2 = mysql_query("INSERT INTO user (login, password) VALUES ('$login', '$password')");
	if ($result2 == 'TRUE')
	{
		echo "Luck, now you can autorize and start play <a href='index.php'>Signup</a>";  
	}
	else
	{
		echo "Error autorize";
	}

?>