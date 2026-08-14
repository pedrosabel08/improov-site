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