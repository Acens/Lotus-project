<html>

<head>
	<?php
	include 'user.class.php'
		if(!emailLogado(cookie = true))
		{
			unset($_SESSION['nome'];
			unset($_SESSION['email'])
			header('location.form');
		}
		else{
			start_session();
		}
		?>
</head>

<body>
<?php
	mysql_connect('host','name','pass') or die(mysql_error());
	mysql_select_db(database_name) or die(mysql_error());
	$ssl = 'SELECT * FROM usuarios';
	$query =  mysql_query($ssl); 

	if($query)
	{
		while($row = mysql_fetch_array($query))
		{
			echo '';
		}
		mysql_close();

	}
	else{
		echo 'erro ao estabelecer conexão com banco de dados';
	}
	?>


</body>