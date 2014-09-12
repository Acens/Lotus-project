<?php
 
$nome = trim($_POST['nome']);
$email = trim($_POST['email']);

    // Define uma função que poderá ser usada para validar e-mails usando regexp
    function validaEmail($email) {
        $conta = "^[a-zA-Z0-9\._-]+@";
        $domino = "[a-zA-Z0-9\._-]+.";
        $extensao = "([a-zA-Z]{2,4})$";

        $pattern = $conta.$domino.$extensao;

        if (ereg($pattern, $email))
        return true;
        else
        return false;
    }

    // Define uma variável para testar o validador
    $input = "meuemail@dominio.com.br";

    // Faz a verificação usando a função
    if (validaEmail($input)) {
    echo "O e-mail inserido é valido!";
    } else {
    echo "O e-mail inserido é invalido!";
    }
 
    /* Vamos checar se o nome de Usuário escolhido e/ou Email já existem no banco de dados */
 
    $sql_email_check = mysql_query(
 
        "SELECT COUNT(usuario_id) FROM usuarios WHERE email='{$email}'"
 
        );
 
    $sql_nome_check = mysql_query(
 
        "SELECT COUNT(usuario_id) FROM usuarios WHERE usuario='{$usuario}'"
 
        );
 
    $eReg = mysql_fetch_array($sql_email_check);
    $uReg = mysql_fetch_array($sql_usuario_check);
 
    $email_check = $eReg[0];
    $usuario_check = $uReg[0];
 
    if (($email_check > 0) || ($usuario_check > 0)){
 
        echo "<strong>ERRO</strong>: <br /><br />";
 
        if ($email_check > 0){
 
            echo "Este email já está sendo utilizado.<br /><br />";
 
            unset($email);
 
        }
 
        if ($usuario_check > 0){
 
            echo "Este nome de usuário já está sendo
                 utilizado.<br /><br />";
 
            unset($usuario);
 
        }
 
        include "formulario_cadastro.php";
 
    }else{
 
        /* Se passarmos por esta verificação ilesos é hora de
        finalmente cadastrar os dados. Vamos utilizar uma função para gerar a senha de
        forma randômica*/
 
        function makeRandomPassword(){
 
            $salt = "abchefghjkmnpqrstuvwxyz0123456789";
            srand((double)microtime()*1000000);
            $i = 0;
 
            while ($i <= 7){
 
                $num = rand() % 33;
                $tmp = substr($salt, $num, 1);
                $pass = $pass . $tmp;
                $i++;
 
            }
 
            return $pass;
 
        }
 
        $senha_randomica   =  makeRandomPassword();
        $senha = md5($senha_randomica);
 
        // Inserindo os dados no banco de dados
 
        $info = htmlspecialchars($info);
 
        $sql = mysql_query(
 
                "INSERT INTO usuarios
                (nome, email, data_cadastro)
 
                VALUES
                ('$nome', '$email', now())")
 
                or die( mysql_error()
 
                );
 
        if (!$sql){
 
            echo "Ocorreu um erro ao criar sua conta, entre em contato.";
 
        }

    $grava = $_POST['grava']; 
    if($grava=="ok") { 
    $nome = $_POST['nome']; 
    $email = $_POST['email']; 
    $tempo_cookie = '(time() + (3 * 24 * 3600))'; // tempo em segundos - 60 para um minuto 
    setcookie("seunome", $nome, time()+($tempo_cookie)); 
    setcookie("seuemail", $email, time()+($tempo_cookie)); 
    echo "<script>location.href='teste.php'</script>"; 
    } 

    $o_cookie_nome = $HTTP_COOKIE_VARS['seunome']; 
    $o_cookie_nome=="" ? $valor_nome = "coloque seu nome" : $valor_nome = $HTTP_COOKIE_VARS['seunome']; 

    $o_cookie_email = $HTTP_COOKIE_VARS['seuemail']; 
    $o_cookie_email=="" ? $valor_email = "coloque seu e-mail" : $valor_email = $HTTP_COOKIE_VARS['seuemail'];  
}
 
?>