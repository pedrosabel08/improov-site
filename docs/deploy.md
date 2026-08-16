# Deploy

1. Fazer backup do código, banco e `uploads/`.
2. Publicar o código em staging com `APP_BASE_URL=/improov-site` e `APP_ORIGIN=https://improov.com.br`.
3. Executar `database/migrations/002_contatos.sql`.
4. Configurar `upload_max_filesize=20M` e `post_max_size=24M` ou superior.
5. Validar e publicar o pacote de mídia antes de apontar metadados para novos derivados.
6. Testar candidatura; depois atualizar e reiniciar o worker.
7. Testar contato, rotas, redirects e 404.
8. Ativar o `.htaccess` novo e monitorar logs.

Rollback: restaurar o pacote de código anterior e o `.htaccess`. A migration é aditiva e pode permanecer; não apagar contatos ou candidaturas durante rollback.
