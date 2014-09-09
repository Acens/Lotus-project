<?php

include 'user.class.php'

$nome = mysql_escape_string($_POST['nome']);
$email = mysql_escape_string($_POST['email']);

if(!verifica_email($nome,$email)){

	echo "email digitado já existe!"."</br>";
}
else if(!validaEmail($email))
{
	echo "formato de email não aceito!"."</br>";
}

else 
{
	mysql_connect('server','username','pass') or die(mysql_error());
	mysql_select_db(database_name) or die(mysql_error());

	$ssl = 'INSERT INTO usuarios(nome,email)
	VALUES(''.$nome,''.$email)';
	$query = mysql_query($ssl);
	if(!$query){
		echo ''.mysql_error();
	}
	if($_POST['lembrar'] == '1')
	{
		session_start();
		lembrarDados($nome,$email);
		header(location.table);
	}
	else{
		session_start();
		header(location.table);	
	}
}

?>
