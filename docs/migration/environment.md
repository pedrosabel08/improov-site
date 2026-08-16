# Inventário do ambiente local

Data da verificação: 15/08/2026.

## Runtime disponível

- Apache: 2.4.58 (Win64); `httpd -t` retorna `Syntax OK`.
- PHP do XAMPP: 8.2.12.
- Extensões confirmadas no PHP do XAMPP: Fileinfo, Mysqli, mysqlnd e mbstring.
- GD não está habilitada no PHP do XAMPP. `thumb.php` permanece funcional, mas entrega a imagem original quando não há thumbnail previamente gerada.
- `upload_max_filesize` e `post_max_size` do PHP do XAMPP estão em 10 GB; os endpoints continuam aplicando os limites menores de 10 MB e 20 MB no servidor.
- Existe outro PHP no `PATH` (`C:\php\php.exe`, 8.3.8) com limites de 2 MB/8 MB. O worker, Apache e tarefas operacionais não devem usar esse binário por engano.
- ImageMagick não está disponível no `PATH`; os derivados AVIF/WebP dependem da instalação na máquina de processamento de mídia.

## Verificações que dependem do ambiente de staging/produção

- O Apache local não estava escutando na porta 80 durante a validação; as rotas foram exercitadas no servidor embutido do PHP do XAMPP e a sintaxe Apache foi validada separadamente.
- O cliente MySQL local não concluiu a autenticação por incompatibilidade do plugin `caching_sha2_password`. Migrations, fila e worker precisam do smoke test em staging com as credenciais corretas.
- O status do worker systemd não pode ser consultado no Windows. Validar `deploy/systemd/improov-email-worker.service` no host Linux antes da publicação.
- Logs e Search Console não estão disponíveis neste workspace; redirects adicionais continuam pendentes de evidência.
