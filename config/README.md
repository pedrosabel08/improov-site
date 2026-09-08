# Configuração do site

## Candidaturas

O formulário envia os dados para `api/candidatura.php`. O endpoint registra a
candidatura no MySQL/MariaDB e envia uma notificação HTML para `MAIL_TO`.

1. Copie `.env.example` para `.env`.
2. Preencha as variáveis `DB_*` com as credenciais do banco.
3. Configure `MAIL_TO` como `contato@improov.com.br` para receber as candidaturas.
4. Configure `CONTACT_MAIL_TO` como `comercial@improov.com.br` para receber os contatos comerciais.
5. Execute `database/schema.sql` no banco configurado.
6. Execute `composer install --no-dev` no servidor para instalar o PHPMailer.
7. Preencha `SMTP_USERNAME` e `SMTP_PASSWORD` com a conta autorizada a enviar.

O exemplo usa `smtp.gmail.com`, porta `587` e STARTTLS. Para contas Google,
use uma senha de aplicativo, não a senha normal da conta.

O e-mail contém um resumo visual da candidatura, links para portfólio,
currículo e LinkedIn, além da experiência e mensagem informadas. O candidato
fica configurado como `Reply-To` para facilitar uma resposta.

Se o banco gravar e o e-mail falhar, o registro permanece salvo com
`email_status = 'falhou'` e um evento de auditoria é criado.

## Idiomas

Os textos fixos ficam centralizados em `script.js`. Os textos específicos de
cada projeto aceitam traduções por slug no mesmo arquivo, com o conteúdo em
português preservado como padrão.
