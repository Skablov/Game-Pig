<?php
	session_start();
	include("bd.php");
	$id = $_SESSION['id'];
	if(empty($_SESSION['id']) || empty($_SESSION['login']))
	{
		exit("You not autorize in game <a href='index.php'></a>");
	}
	if(empty($_SESSION['id_game']))
	{
		$result_search_game = mysql_query("SELECT id_game FROM game WHERE id1='$id' || id2='$id' && Enable='1'", $db); // ищем нашу игру если она первая
		$search_game = mysql_fetch_array($result_search_game);
		$_SESSION['id_game'] = $search_game['id_game'];
	}
	if(!empty($_SESSION['id_game']))
	{
		$x = $_SESSION['id_game'];
		$result_search_game = mysql_query("SELECT Enable FROM game WHERE id_game = '$x'", $db);
		$res = mysql_fetch_array($result_search_game);
		if($res['Enable'] == 0)
		{
			exit("This game is over <a href=/index.php>Main page</a>");
		}
	}
	$login = $_SESSION['login'];
	$id_game = $_SESSION['id_game'];
	$login_enemy;
	$turn;
	$our_turn; // когда мы ходим
	$time_score = $_SESSION['time_score'];
	$our_score;
	$enemy_score;
	$login = $_SESSION['login'];
	$id_game = $_SESSION['id_game'];
	$tmx = mysql_query("SELECT * FROM game WHERE id_game='$id_game'", $db);
	$tm = mysql_fetch_array($tmx);
	if ($id == $tm['id1']) // нашли логин противника и наш ход по счету
	{
		$id2 = $tm['id2'];
		$yx = mysql_query("SELECT login FROM user WHERE id='$id2'", $db);
		$y = mysql_fetch_array($yx);
		$login_enemy = $y['login'];
		$our_turn = 1;
		$our_score = $tm['score1'];
		$enemy_score = $tm['score2'];
		$id_enemy = $tm['id2'];

	}
	else
	{
		$id2=$tm['id1'];
		$yx = mysql_query("SELECT login FROM user WHERE id='$id2'", $db);
		$y = mysql_fetch_array($yx);
		$login_enemy = $y['login'];
		$our_turn = 0;
		$our_score = $tm['score2'];
		$enemy_score = $tm['score1'];
		$id_enemy = $tm['id1'];
	}
	$turn = $tm['turn']; // получаем значение текущего хода
	if(isset($_POST['roll']))
	{
		if($turn != 10)
		{
			if($our_turn == 0)
			{
				if($turn % 2 == 0)
				{
					$t_score = rand(1,6); // то что выпало на кубике
					if ($t_score != 1)
					{
						$time_score += $t_score;
						$_SESSION['time_score'] = $time_score;
						header("Location: /game.php");
						exit;
					}
					else
					{
						$_SESSION['time_score'] = 0;
						$update_turn = mysql_query("UPDATE game SET turn='$turn+1' WHERE id_game='$id_game'", $db);
						header("Location: /game.php");
						exit;
					}
				}

			}
			else
			{
				if($turn % 2 == 1)
				{
					$t_score = rand(1,6); // то что выпало на кубике
					if ($t_score != 1)
					{
						$time_score += $t_score;
						$_SESSION['time_score'] = $time_score;
						header("Location: /game.php");
						exit;
					}
					else
					{
						$_SESSION['time_score'] = 0;
						$update_turn = mysql_query("UPDATE game SET turn='$turn+1' WHERE id_game='$id_game'", $db);
						header("Location: /game.php");
						exit;
					}
				}
			}
		}
	}
	if (isset($_POST['hold'])) // пишем кнопку hold
	{
		if($turn != 10)
		{
			if($our_turn == 0)
			{
				if($turn % 2 == 0) // если все-таки твой ход
				{
					$id_game = $_SESSION['id_game'];
					$our_score += $time_score;
					$update_score = mysql_query("UPDATE game SET score2='$our_score' WHERE id_game='$id_game'", $db);   // если остаток 0 - тогда ты id2
					$z = $tm['turn'] + 1;
					$update_turn = mysql_query("UPDATE game SET turn='$z' WHERE id_game='$id_game'", $db);
					$_SESSION['time_score'] = 0;
					header("Location: /game.php");
					exit;
				}
			}
			else
			{
				if($turn % 2 == 1)
				{
					$id_game = $_SESSION['id_game'];
					$our_score += $time_score;
					$update_score = mysql_query("UPDATE game SET score1='$our_score' WHERE id_game='$id_game'", $db);   // если остаток 1 - тогда ты id1
					$z = $tm['turn'] + 1;
					$update_turn = mysql_query("UPDATE game SET turn='$z' WHERE id_game='$id_game'", $db);
					$_SESSION['time_score'] = 0;
					header("Location: /game.php");
					exit;
				}
			}
		}		
	}
	if(isset($_POST['exit']))
	{
		if($our_turn == 0) // ты ид 2 значит
		{
			$update_Enable = mysql_query("UPDATE game SET Enable='0' WHERE id_game='$id_game'", $db);
			$tmp = mysql_query("SELECT game_point FROM user WHERE id='$id_enemy'", $db);
			$t_p = mysql_fetch_array($tmp);
			$t = $t_p['game_point'] + 1;
			$update_Point = mysql_query("UPDATE user SET game_point ='$t' WHERE id='$id_enemy'", $db); // дали противнику одно очко поинта
			$tmp = mysql_query("SELECT game_point FROM user WHERE id='$id'", $db);
			$t_p = mysql_fetch_array($tmp);
			$t = $t_p['game_point'] - 1;
			$update_our_Point = mysql_query("UPDATE user SET game_point = '$t' WHERE id='$id'", $db);
			$_SESSION['time_score'] = 0;
			header("Location: /search_game.php");
			exit;
		}
		if($our_turn == 1) // ты ид 1
		{
			$update_Enable = mysql_query("UPDATE game SET Enable='0' WHERE id_game='$id_game'", $db);
			$tmp = mysql_query("SELECT game_point FROM user WHERE id='$id_enemy'", $db);
			$t_p = mysql_fetch_array($tmp);
			$t = $t_p['game_point'] + 1;
			$update_Point = mysql_query("UPDATE user SET game_point ='$t' WHERE id='$id_enemy'", $db); // дали противнику одно очко поинта
			$tmp = mysql_query("SELECT game_point FROM user WHERE id='$id'", $db);
			$top = mysql_fetch_array($tmp);
			$t = $t_p['game_point'] - 1;
			$update_our_Point = mysql_query("UPDATE user SET game_point = '$t' WHERE id='$id'", $db);
			header("Location: /search_game.php");
			exit;
		}
	}
	if($turn == 10) // Добавляем очки и закрываем игру
	{
		$q = $tm['id1'];
		$n = $tm['id2'];
		$tmp1 = mysql_query("SELECT game_point FROM user WHERE id='$q'", $db);
		$top1 = mysql_fetch_array($tmp1);
		$t1 = $top1['game_point'];
		$tmp2 = mysql_query("SELECT game_point FROM user WHERE id='$n'", $db);
		$top2 = mysql_fetch_array($tmp2);
		$t2 = $top2['game_point'];
		if($tm['score1'] > $tm['score2'])
		{

			$change = mysql_query("UPDATE user SET game_point ='$t1 + 1'WHERE id='$q'", $db);
			$change = mysql_query("UPDATE user SET game_point = '$t2 - 1'WHERE id='$n'", $db);
			$update_Enable = mysql_query("UPDATE game SET Enable='0' WHERE id_game='$id_game'", $db);
			header("Location: /search_game.php");
			exit;
		}
		if($tm['score1'] > $tm['score2'])
		{

			$change = mysql_query("UPDATE user SET game_point ='$t1 + 1'WHERE id='$q'", $db);
			$change = mysql_query("UPDATE user SET game_point = '$t2 - 1'WHERE id='$n'", $db);
			$update_Enable = mysql_query("UPDATE game SET Enable='0' WHERE id_game='$id_game'", $db);
			header("Location: /search_game.php");
			exit;
		}
		if($tm['score1'] == $tm['score2']) // то никто не получает очков, игра закрывается
		{
			$update_Enable = mysql_query("UPDATE game SET Enable='0' WHERE id_game='$id_game'", $db);
			header("Location: /search_game.php");
			exit;
		}
	}

?>
<!DOCTYPE html>
<html>
<head>
	<title>Svin</title>
</head>
<body>
	<form method="POST">
	<meta http-equiv="refresh" content="15">
	<p>
		<label>Your score</label>
		<label><?php echo $our_score ?></label>
	</p>
	<p>
		<label>Score your enemy</label>
		<label><?php echo $enemy_score ?></label>

	</p>
	<p>
		<label> Time score</label>
		<label> <?php echo $time_score ?></label>
	</p>
	<p>
		<label>Your enemy:<?php echo $login_enemy?></label>
	</p>
	<p>
		<label> Turn: <?php echo $turn?></label>
	</p>
	<p>
		<input type="submit" name="roll" value="roll">
	</p>
	<p>
		<input type="submit" name="hold" value="hold">
	</p>
	<p>
		<input type="submit" name="exit" value="exit">
	</p>
</form>
</body>
</html>


