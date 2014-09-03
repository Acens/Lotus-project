<?php
class nome {
	/**
	 * Nome do banco de dados onde está a tabela de usuários
	 * Sobre o método $this = Você usa $this quando quer acessar uma propriedade (vulgarmente, variável de classe) ou um método da mesma classe, da classe-pai (se existir) ou de outra classe instanciada e associada à uma propriedade existente. 
	 * @var string
	 * @since v1.0
	 */
	var $bancoDeDados = 'meu_site';
	
	/**
	 * Nome da tabela de nome
	 * 
	 * @var string
	 */
	var $tabelaNome = 'nome';
	
	/**
	 * nome dos campos onde ficam o nome, o e-mail de cada nome
	 * 
	 * Formato: tipo => nome do campo na tabela
	 * 
	 * 
	 * @var array
	 * @since v1.0
	 */
	var $campos = array(
		'nome' => 'nome',
		'e-mail' => 'e-mail'
	);
	
	/**
	 * nome dos campos que serão pegos da tabela de nome e salvos na sessão,
	 * caso o valor seja false nenhum dado será consultado
	 * 
	 * @var mixed
	 * @since v1.0
	 */
	var $dados = array('nome', 'e-mail');
	
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
	 * O nome e e-mail são case-sensitive?
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
	 * Quantidade (em dias) que o sistema lembrará os dados do nome ("Lembrar meu e-mail")
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
	 * @param string $e-mail O e-mail que será validado
	 * @return boolean Se o Nome existe
	 */
	function nome($nome, $e-mail) {
		
		// Filtra os dados?
		if ($this->filtraDados) {
			$nome = mysql_escape_string($nome);
			$e-mail = mysql_escape_string($e-mail);
		}
		
		// Os dados são case-sensitive?
		$binary = ($this->caseSensitive) ? 'BINARY' : '';

		// Procura por usuários com o mesmo nome e e-mail
		$sql = "SELECT COUNT(*) AS total
				FROM `{$this->bancoDeDados}`.`{$this->nome}`
				WHERE
					{$binary} `{$this->campos['nome']}` = '{$nome}'
					AND
					{$binary} `{$this->campos['e-mail']}` = '{$e-mail}'";
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
	
	/**
	 * Tenta logar um Nome no sistema salvando seus dados na sessão e cookies
	 * 
	 * @access public
	 * @since v1.0
	 * @uses nome::nome()
	 * @uses nome::lembrarDados()
	 *
	 * @param string $nome O Nome que será logado
	 * @param string $e-mail O e-mail do Nome
	 * @param boolean $lembrar Salvar os dados em cookies? (Lembrar meu-email)
	 * @return boolean Se o Nome foi logado
	 */
	function nome($nome, $e-mail, $lembrar = false) {			
		// Verifica se é um Nome válido
		if ($this->nome($nome, $e-mail)) {
		
			// Inicia a sessão?
			if ($this->iniciaSessao AND !isset($_SESSION)) {
				session_start();
			}
		
			// Filtra os dados?
			if ($this->filtraDados) {
				$nome = mysql_real_escape_string($nome);
			}
			
			// Traz dados da tabela?
			if ($this->dados != false) {
				// Adiciona o campo do Nome na lista de dados
				if (!in_array($this->campos['nome'], $this->dados)) {
					$this->dados[] = 'nome';
				}
			
				// Monta o formato SQL da lista de campos
				$dados = '`' . join('`, `', array_unique($this->dados)) . '`';
		
				// Os dados são case-sensitive?
				$binary = ($this->caseSensitive) ? 'BINARY' : '';

				// Consulta os dados
				$sql = "SELECT {$dados}
						FROM `{$this->bancoDeDados}`.`{$this->nome}`
						WHERE {$binary} `{$this->campos['nome']}` = '{$nome}'";
				$query = mysql_query($sql);
				
				// Se a consulta falhou
				if (!$query) {
					// A consulta foi mal sucedida, retorna false
					$this->erro = 'A consulta dos dados é inválida';
					return false;
				} else {
					// Traz os dados encontrados para um array
					$dados = mysql_fetch_assoc($query);
					// Limpa a consulta da memória
					mysql_free_result($query);
					
					// Passa os dados para a sessão
					foreach ($dados AS $chave=>$valor) {
						$_SESSION[$this->prefixoChaves . $chave] = $valor;
					}
				}
			}
			
			// Nome logado com sucesso
			$_SESSION[$this->prefixoChaves . 'logado'] = true;
			
			// Define um cookie para maior segurança?
			if ($this->cookie) {
				// Monta uma cookie com informações gerais sobre o Nome: nome, ip e navegador
				$valor = join('#', array($nome, $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']));
				
				// Encripta o valor do cookie
				$valor = sha1($valor);
				
				// Cria o cookie
				setcookie($this->prefixoChaves . 'token', $valor, 0, $this->cookiePath);
			}
			
			// Salva os dados do Nome em cookies? ("Lembrar meu e-mail")
			if ($lembrar) $this->lembrarDados($nome, $e-mail);
			
			// Fim da verificação, retorna true
			return true;
			
						
		} else {
			$this->erro = 'Nome inválido';
			return false;
		nome	}
	
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
	 * @param boolean $cookies Limpa também os cookies de "Lembrar meu e-mail"?
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
		
		// Limpa também os cookies de "Lembrar meu e-mail"?
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
	 * @param string $e-mail O e-mail do Nome
	 * @return void
	 */
	function lembrarDados($nome, $e-mail) {	
		// Calcula o timestamp final para os cookies expirarem
		$tempo = strtotime("+{$this->lembrarTempo} day", time());

		// Encripta os dados do Nome usando base64
		// O rand(1, 9) cria um digito no início da string que impede a descriptografia
		$nome = rand(1, 9) . base64_encode($nome);
		$e-mail = rand(1, 9) . base64_encode($e-mail);
	
		// Cria um cookie com o Nome
		setcookie($this->prefixoChaves . 'lu', $nome, $tempo, $this->cookiePath);
		// Cria um cookie com a e-mail
		setcookie($this->prefixoChaves . 'ls', $e-mail, $tempo, $this->cookiePath);
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
		// Os cookies de "Lembrar meu e-mail" existem?
		if (isset($_COOKIE[$this->prefixoChaves . 'lu']) AND isset($_COOKIE[$this->prefixoChaves . 'ls'])) {
			// Pega os valores salvos nos cookies removendo o digito e desencriptando
			$nome = base64_decode(substr($_COOKIE[$this->prefixoChaves . 'lu'], 1));
			$e-mail = base64_decode(substr($_COOKIE[$this->prefixoChaves . 'ls'], 1));
			
			// Tenta logar o Nome com os dados encontrados nos cookies
			return $this->loganomes($nome, $e-mail, true);		
		}
		
		// Não há nenhum cookie, dados inválidos
		return false;
	}
	
	/**
	 * Limpa os dados lembrados dos cookies ("Lembrar meu e-mail")
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
		// Deleta o cookie com o e-mail
		if (isset($_COOKIE[$this->prefixoChaves . 'ls'])) {
			setcookie($this->prefixoChaves . 'ls', false, (time() - 3600), $this->cookiePath);
			unset($_COOKIE[$this->prefixoChaves . 'ls']);			
		}
	}
}

?>