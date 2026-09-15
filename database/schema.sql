CREATE TABLE IF NOT EXISTS candidaturas (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    nome VARCHAR(160) NOT NULL,
    email VARCHAR(254) NOT NULL,
    telefone VARCHAR(40) NOT NULL,
    cidade_uf VARCHAR(120) NOT NULL,
    area_cargo VARCHAR(160) NOT NULL,
    disponibilidade_inicio VARCHAR(160) NULL,
    modelos_trabalho VARCHAR(120) NOT NULL,
    portfolio_url VARCHAR(2048) NOT NULL,
    curriculo_url VARCHAR(2048) NOT NULL,
    linkedin_url VARCHAR(2048) NULL,
    experiencia TEXT NULL,
    mensagem TEXT NULL,
    idioma VARCHAR(10) NOT NULL DEFAULT 'pt-BR',
    lgpd_aceito TINYINT(1) NOT NULL DEFAULT 1,
    lgpd_aceito_em DATETIME NOT NULL,
    status VARCHAR(40) NOT NULL DEFAULT 'recebida',
    email_status VARCHAR(20) NOT NULL DEFAULT 'pendente',
    email_enviado_em DATETIME NULL,
    email_erro TEXT NULL,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_candidaturas_email (email),
    KEY idx_candidaturas_status (status),
    KEY idx_candidaturas_criado_em (criado_em)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS candidatura_eventos (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    candidatura_id BIGINT UNSIGNED NOT NULL,
    tipo VARCHAR(50) NOT NULL,
    descricao VARCHAR(255) NULL,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_eventos_candidatura (candidatura_id),
    CONSTRAINT fk_eventos_candidatura FOREIGN KEY (candidatura_id) REFERENCES candidaturas (id) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS contatos (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    nome VARCHAR(160) NOT NULL,
    empresa VARCHAR(160) NULL,
    email VARCHAR(254) NOT NULL,
    telefone VARCHAR(40) NOT NULL,
    cidade_uf VARCHAR(120) NULL,
    tipo_interesse VARCHAR(160) NOT NULL,
    empreendimento VARCHAR(200) NULL,
    mensagem TEXT NOT NULL,
    anexo_url VARCHAR(2048) NULL,
    anexo_nome VARCHAR(255) NULL,
    anexo_mime VARCHAR(120) NULL,
    anexo_tamanho BIGINT UNSIGNED NULL,
    idioma VARCHAR(10) NOT NULL DEFAULT 'pt-BR',
    utm_atribuicao TEXT NULL,
    lgpd_aceito TINYINT(1) NOT NULL DEFAULT 1,
    lgpd_aceito_em DATETIME NOT NULL,
    status VARCHAR(40) NOT NULL DEFAULT 'recebido',
    email_status VARCHAR(20) NOT NULL DEFAULT 'pendente',
    email_enviado_em DATETIME NULL,
    email_erro TEXT NULL,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_contatos_email (email),
    KEY idx_contatos_status (status),
    KEY idx_contatos_criado_em (criado_em)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS contato_eventos (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    contato_id BIGINT UNSIGNED NOT NULL,
    tipo VARCHAR(50) NOT NULL,
    descricao VARCHAR(255) NULL,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_eventos_contato (contato_id),
    CONSTRAINT fk_eventos_contato FOREIGN KEY (contato_id) REFERENCES contatos (id) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS form_rate_limits (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    escopo VARCHAR(40) NOT NULL,
    ip_hash CHAR(64) NOT NULL,
    tentativas SMALLINT UNSIGNED NOT NULL DEFAULT 1,
    janela_inicio DATETIME NOT NULL,
    atualizado_em DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_rate_scope_ip (escopo, ip_hash),
    KEY idx_rate_updated (atualizado_em)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
