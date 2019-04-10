<?php
	session_start();
	include("bd.php");	
	if(!empty($_SESSION['time_score']))
	{
		$_SESSION['time_score'] = 0;
	}
	if(!empty($_SESSION['id_game']))
	{
		$result_my_point = mysql_query("SELECT game_point FROM user WHERE login = '$login'", $db);
		$my_point_mass = mysql_fetch_array($result_my_point);
		$my_point = $my_point_mass['game_point'];
		if($t_game['Enable'] == 1)
		{
			header("Location: /game.php");
			exit;
		}
		else
		{
			empty($_SESSION['id_game']);
		}
	}
	if(empty($_SESSION['id']) || empty($_SESSION['login'])) // проверка на авторизацию
	{
		exit("You not autorize! <a href=index.php></a>");
	}

	if(isset($_POST['exit'])) // Если человек нажал выйти
	{
		empty($_SESSION);
		header("Location: /index.php");
		exit;
	}
	if(isset($_POST['search']))
	{
		$s_string = 'searching now';
		$min = 10000;
		$id_enemy = -1;
		$login = $_SESSION['login'];
		$id = $_SESSION['id'];
		$result_my_point = mysql_query("SELECT game_point FROM user WHERE login = '$login'", $db);
		$my_point_mass = mysql_fetch_array($result_my_point);
		$my_point = $my_point_mass['game_point'];
		$change_search = mysql_query("UPDATE user SET search = '1' WHERE login='$login'", $db); // обновляем наш статус поиска
		$result_all = mysql_query("SELECT id FROM user WHERE login!='$login' && search='1'", $db);
		while($all_users = mysql_fetch_row($result_all))
		{
			$result_point_now = mysql_query("SELECT game_point FROM user WHERE id='$all_users'", $db);
			$t_point_now = mysql_fetch_array($result_point_now);
			$point_now = $t_point_now['game_point'];
			$tmx = $point_now - $my_point; // временная переменная для разницы твоих и чужих игровых поинтов
			if(abs($tmx) < $min)
			{
				$min = abs($tmx);
				$id_enemy = $all_users[0];
			}
		}
		if($id_enemy != -1)
		{
			$create_room = mysql_query("INSERT INTO game (id1, id2, Enable,score1,score2, turn) VALUES ('$id','$id_enemy','1','0','0','0')");
			$change_search = mysql_query("UPDATE user SET search='2' WHERE id='$id'",$db); // 2 в колонке search означает, что игрок в данный момент находится в комнате
			$change_search = mysql_query("UPDATE user SET search='2' WHERE id='$id_enemy'", $db);
			$result_id_game = mysql_query("SELECT id_game FROM game WHERE id1='$id' && Enable='1'", $db);
			$t_id_game = mysql_fetch_array($result_id_game);
			$id_game = $t_id_game['id_game'];
			$_SESSION['id_game'] = $id_game;
			header("Location: /game.php");
			exit;
			
		}
		while(!isset($_POST['stop_search']))
		{

			$result_search_room = mysql_query("SELECT id_game FROM game WHERE id2='$id' && Enable='1'", $db);
			$t_search_room = mysql_fetch_array($result_search_room);
			$id_game = $t_search_room['id_game'];
			if($result_search_room == true) break;
			{
				$_SESSION['id_game'] = $id_game;
				header("Location: /game.php");
				exit;
			}
		}
		if(isset($_POST['stop_search']))
		{
			$s_string = 'search stop';
			$result_search_room = mysql_query("SELECT search FROM user WHERE id='$id'", $db);
			$search_room = mysql_fetch_array($result_search_room);
			$search_now = $search_room['search'];	 
			if($search_now == 1)
			{
				$change_search = mysql_query("UPDATE user SET search='0' WHERE id='$id'",$db);
			}
			else
			{
				if($search_now == 2)
				{
					header("Location: /game.php");
					exit;
				}
			}
		}
	}
	$login = $_SESSION['login'];
	$result_my_point = mysql_query("SELECT game_point FROM user WHERE login = '$login'", $db);
	$my_point_mass = mysql_fetch_array($result_my_point);
?>
<!DOCTYPE html>
<html>
<head>
	<title>Pig main page <?php echo $s_string ?></title>
	<meta http-equiv="refresh" content="3">
</head>
<body>
<form method="POST">
	<p>
		<label>Hello <?php echo $_SESSION['login'] ?></label>
	</p>
	<p>
		<label>Your game point = <?php echo $my_point_mass['game_point']; ?>
	<p>
		<input type="submit" name="search" value="search">
		<input type="submit" name="stop_search" value="stop search">
	</p>
	<p>
		<input type="submit" name="exit" value="exit">
	</p>
</form>

</body>
</html>