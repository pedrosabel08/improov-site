# Checklist de release

- Conferir as pendências registradas em `docs/migration/environment.md`.
- Backup do código, banco e uploads validado.
- Migrations executadas em staging antes de produção.
- Manifesto de mídia sem arquivos ausentes.
- Rotas novas e redirects sem loops.
- Home, Quem Somos, Projetos, detalhe, Trabalhe Conosco, Contato, Privacidade e 404 testados.
- PT, EN e ES testados com persistência.
- Candidatura e contato validados até banco, fila e worker.
- Uploads bloqueados para acesso HTTP direto.
- Worker ativo após publicação.
- Logs de PHP, Apache e worker revisados.
