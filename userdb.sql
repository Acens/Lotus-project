CREATE TABLE usuarios(
    nome varchar(50) NOT NULL default '',
    email varchar(100) NOT NULL default '',
    data_cadastro datetime NOT NULL default '0000-00-00 00:00:00',
    data_ultimo_login datetime NOT NULL default '0000-00-00 00:00:00',
    ativado enum('0','1') NOT NULL default '0',
    PRIMARY KEY  (usuario_id)
) ENGINE = MYISAM CHARACTER SET latin1 COLLATE latin1_general_ci COMMENT = '';