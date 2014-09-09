<?php
class nome {
	/**
	 * Nome do banco de dados onde está a tabela de usuários
	 * Sobre o método $this = Você usa $this quando quer acessar uma propriedade (vulgarmente, variável de classe) ou um método da mesma classe, da classe-pai (se existir) ou de outra classe instanciada e associada à uma propriedade existente. 
	 * @var string
	 * @since v1.0
	 */
	var $bancoDeDados = 'mcrypt_enc_get_supported_key_sizes(td)te';
	
	/**
	 * Nome da tabela de nome
	 * 
	 * @var string
	 */
	var $tabelaNome = 'usuarios';
	
	/**
	 * nome dos campos onde ficam o nome, o email de cada nome
	 * 
	 * Formato: tipo => nome do campo na tabela
	 * 
	 * 
	 * @var array
	 * @since v1.0
	 */
	var $campos = array(
		'nome' => 'nome',
		'email' => 'email'
	);
	
	/**
	 * nome dos campos que serão pegos da tabela de nome e salvos na sessão,
	 * caso o valor seja false nenhum dado será consultado
	 * 
	 * @var mixed
	 * @since v1.0
	 */
	var $dados = array('nome', 'email');
	
	/**
	 * Inicia a sessão se necessário?
	 * 
	 * @var boolean
	 * @since v1.0
	 */
	var $iniciaSessao = true;
	
	/**
	 * Prefixo das chaves usadas na sessão
	 * 
	 * @var string
	 * @since v1.0
	 */
	var $prefixoChaves = 'nome';
	
	/**
	 * Usa um cookie para melhorar a segurança?
	 * 
	 * @var boolean
	 * @since v1.0
	 */
	var $cookie = true;
	
	/**
	 * O nome e email são case-sensitive?
	 * 
	 * Em valores case-sensitive "casa" é diferente de "CaSa" e de "CASA"
	 * 
	 * @var boolean
	 * @since v1.1
	 */
	var $caseSensitive = true;
	
	/**
	 * Filtra os dados antes de consultá-los usando mysql_real_escape_string()?
	 * mysql_real_escape_string — Escapa os caracteres especiais numa string para usar em um comando SQL, levando em conta o conjunto atual de caracteres.
	 * @var boolean
	 * @since v1.1
	 */
	var $filtraDados = true;
	
	/**
	 * Quantidade (em dias) que o sistema lembrará os dados do nome ("Lembrar meu email")
	 * 
	 * Usado apenas quando o terceiro parâmetro do método nome::nome() for true
	 * Os dados salvos serão encriptados usando base64
	 * 
	 * @var integer
	 * @since v1.1
	 */
	var $lembrarTempo = 7;
	
	/**
	 * Diretório a qual o cookie vai pertencer
	 * Atenção: Não edite se você não souber o que está fazendo!
	 * 
	 * @var string
	 * @since v1.1
	 */
	var $cookiePath = '/';
	
	/**
	 * Armazena as mensagens de erro
	 * 
	 * @var string
	 * @since v1.0
	 */
	var $erro = '';
	
	/**
	 * Verifica se um Nome existe no sistema
	 * 
	 * @access public
	 * @since v1.0
	 * 
	 * @param string $nome O Nome que será validado
	 * @param string $email O email que será validado
	 * @return boolean Se o Nome existe
	 */
	function verifica_email($nome, $email) {
		
		// Filtra os dados?
		if ($this->filtraDados) {
			$nome = mysql_escape_string($nome);
			$email = mysql_escape_string($email);
		}
		
		// Os dados são case-sensitive?
		$binary = ($this->caseSensitive) ? 'BINARY' : '';

		// Procura por usuários com o mesmo nome e email
		$sql = "SELECT COUNT(*) AS total
				FROM `{$this->bancoDeDados}`.`{$this->nome}`
				WHERE
					{$binary} `{$this->campos['nome']}` = '{$nome}'
					AND
					{$binary} `{$this->campos['email']}` = '{$email}'";
		$query = mysql_query($sql);
		if ($query) {
			// Total de usuários encontrados
			$total = mysql_result($query, 0, 'total');
			
			// Limpa a consulta da memória
			mysql_free_result($query);
		} else {
			// A consulta foi mal sucedida, retorna false
			return false;
		}
		
		// Se houver apenas um Nome, retorna true
		return ($total == 1) ? true : false;
	}
	
	// Define uma função que poderá ser usada para validar emails usando regexp
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
	echo "O email inserido é valido!";
	} else {
	echo "O email inserido é invalido!";
	}
	
	/**
	 * Tenta logar um Nome no sistema salvando seus dados na sessão e cookies
	 * 
	 * @access public
	 * @since v1.0
	 * @uses nome::nome()
	 * @uses nome::lembrarDados()
	 *
	 * @param string $nome O Nome que será logado
	 * @param string $email O email do Nome
	 * @param boolean $lembrar Salvar os dados em cookies? (Lembrar meu-email)
	 * @return boolean Se o Nome foi logado
	 */
	
	
	/**
	 * Verifica se há um Nome logado no sistema
	 * 
	 * @access public
	 * @uses nome::verificaDadosLembrados()
	 *
	 * @param boolean $cookies Verifica também os cookies?
	 * @return boolean Se há um Nome logado
	 */
	function emailLogado($cookies = true) {
		// Inicia a sessão?
		if ($this->iniciaSessao AND !isset($_SESSION)) {
			session_start();
		}
		
		// Verifica se não existe o valor na sessão
		if (!isset($_SESSION[$this->prefixoChaves . 'logado']) OR !$_SESSION[$this->prefixoChaves . 'logado']) {
			// Não existem dados na sessão
			
			// Verifica os dados salvos nos cookies?
			if ($cookies) {
				// Se os dados forem válidos o Nome é logado automaticamente
				return $this->verificaDadosLembrados();
			} else {
				// Não há Nome logado
				$this->erro = 'Não há Nome logado';
				return false;
			}
		}
		
		// Faz a verificação do cookie?
		if ($this->cookie) {
			// Verifica se o cookie não existe
			if (!isset($_COOKIE[$this->prefixoChaves . 'token'])) {
				$this->erro = 'Não há Nome logado';
				return false;
			} else {
				// Monta o valor do cookie
				$valor = join('#', array($_SESSION[$this->prefixoChaves . 'nome'], $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']));
	
					// Encripta o valor do cookie
				$valor = sha1($valor);
	
				// Verifica o valor do cookie
				if ($_COOKIE[$this->prefixoChaves . 'token'] !== $valor) {
					$this->erro = 'Não há Nome logado';
					return false;
				}
			}
		}
		
		// A sessão e o cookie foram verificados, há um Nome logado
		return true;
	}
	
	/**
	 * Faz logout do Nome logado
	 * 
	 * @access public
	 * @since v1.0
	 * @uses nome::limpaDadosLembrados()
	 * @uses nome::emailLogado()
	 * 
	 * @param boolean $cookies Limpa também os cookies de "Lembrar meu email"?
	 * @return boolean
	 */
	function logout($cookies = true) {
		// Inicia a sessão?
		if ($this->iniciaSessao AND !isset($_SESSION)) {
			session_start();
		}
		
		// Tamanho do prefixo
		$tamanho = strlen($this->prefixoChaves);

		// Destroi todos os valores da sessão relativos ao sistema de login
		foreach ($_SESSION AS $chave=>$valor) {
			// Remove apenas valores cujas chaves comecem com o prefixo correto
			if (substr($chave, 0, $tamanho) == $this->prefixoChaves) {
				unset($_SESSION[$chave]);
			}
		}
		
		// Destrói asessão se ela estiver vazia
		if (count($_SESSION) == 0) {
			session_destroy();
			
			// Remove o cookie da sessão se ele existir
			if (isset($_COOKIE['PHPSESSID'])) {
				setcookie('PHPSESSID', false, (time() - 3600));
				unset($_COOKIE['PHPSESSID']);
			}
		}
		
		// Remove o cookie com as informações do visitante
		if ($this->cookie AND isset($_COOKIE[$this->prefixoChaves . 'token'])) {
			setcookie($this->prefixoChaves . 'token', false, (time() - 3600), $this->cookiePath);
			unset($_COOKIE[$this->prefixoChaves . 'token']);
		}
		
		// Limpa também os cookies de "Lembrar meu email"?
		if ($cookies) $this->limpaDadosLembrados();
		
		// Retorna SE não há um Nome logado (sem verificar os cookies)
		return !$this->emailLogado(false);
	}
	
	/**
	 * Salva os dados do Nome em cookies ("Lembrar meu email")
	 * 
	 * @access public
	 * 
	 * @param string $nome O Nome que será lembrado
	 * @param string $email O email do Nome
	 * @return void
	 */
	function lembrarDados($nome, $email) {	
		// Calcula o timestamp final para os cookies expirarem
		$tempo = strtotime("+{$this->lembrarTempo} day", time());

		// Encripta os dados do Nome usando base64
		// O rand(1, 9) cria um digito no início da string que impede a descriptografia
		$nome = rand(1, 9) . base64_encode($nome);
		$email = rand(1, 9) . base64_encode($email);
	
		// Cria um cookie com o Nome
		setcookie($this->prefixoChaves . 'lu', $nome, $tempo, $this->cookiePath);
		// Cria um cookie com a email
		setcookie($this->prefixoChaves . 'ls', $email, $tempo, $this->cookiePath);
	}
	
	/**
	 * Verifica os dados do cookie (caso eles existam)
	 * 
	 * @access public
	 * @uses nome::loganomes()
	 * 
	 * @return boolean Os dados são validos?
	 */
	function verificaDadosLembrados() {
		// Os cookies de "Lembrar meu email" existem?
		if (isset($_COOKIE[$this->prefixoChaves . 'lu']) AND isset($_COOKIE[$this->prefixoChaves . 'ls'])) {
			// Pega os valores salvos nos cookies removendo o digito e desencriptando
			$nome = base64_decode(substr($_COOKIE[$this->prefixoChaves . 'lu'], 1));
			$email = base64_decode(substr($_COOKIE[$this->prefixoChaves . 'ls'], 1));
			
			// Tenta logar o Nome com os dados encontrados nos cookies
			return $this->loganomes($nome, $email, true);		
		}
		
		// Não há nenhum cookie, dados inválidos
		return false;
	}
	
	/**
	 * Limpa os dados lembrados dos cookies ("Lembrar meu email")
	 * 
	 * @access public
	 * 
	 * @return void
	 */
	function limpaDadosLembrados() {
		// Deleta o cookie com o Nome
		if (isset($_COOKIE[$this->prefixoChaves . 'lu'])) {
			setcookie($this->prefixoChaves . 'lu', false, (time() - 3600), $this->cookiePath);
			unset($_COOKIE[$this->prefixoChaves . 'lu']);			
		}
		// Deleta o cookie com o email
		if (isset($_COOKIE[$this->prefixoChaves . 'ls'])) {
			setcookie($this->prefixoChaves . 'ls', false, (time() - 3600), $this->cookiePath);
			unset($_COOKIE[$this->prefixoChaves . 'ls']);			
		}
	}
}
	
	function verifica(){

	if(emailLogado($cookies = true)){
		header("local da pagina de tabelas");
	}
	else{
		header("pagina do form");
	}
	
	}

?>