<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtl">

<head>
<title>formulario</title>
<?php

if(!isset($_SESSION['user'] == true && !issset($_SESSION['email'] == true))
	{
		head('formulário');
	}
	else{
		head("index(tabela)");
	}
	 ?>
</head>


<body>
 <form action = 'valida.php' method = 'POST'>
 	Email:
 	<input type = 'email' name = 'email'>
 	<br>Nome: 
 	<input type = 'text' name = 'name'>
 	<input type = 'checkbox' name = 'lembrar' value = '1'></br>
 	<input type = 'submit' value = 'enviar' >
 </form>

</body>

</html>

