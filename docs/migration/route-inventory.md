# Inventário de rotas

Base atual: `/improov-site/`. As regras Apache estão em `.htaccess` e `Projetos/.htaccess`. O diretório físico legado possui regras próprias; por isso os redirects dos links reais também são definidos nele. A condição usa a capitalização original de `THE_REQUEST` para distinguir `/Projetos/` de `/projetos/` e evitar ciclos no Windows.

| URL antiga | URL atual | Ação |
| --- | --- | --- |
| `/index.html` | `/` | 301 |
| `/privacidade.html` | `/privacidade` | 301 |
| `/Projetos/index.html` | `/projetos` | 301 |
| `/Projetos/aya-kar` | `/projetos/aya-kar` | 301 |
| `/Projetos/ars-vie` | `/projetos/ars-vie` | 301 |

As variantes dos dois slugs reais acima na raiz e em `Projetos/detalhes.html?id=...` também recebem 301. A página de demonstração antiga foi removida; o redirect é resolvido antes da existência física do arquivo.

Rotas de cases publicados: `/projetos/ars-vie`, `/projetos/aya-kar` e `/projetos/alp-sc`. Novos slugs cadastrados passam pelo roteador genérico; não precisam de página PHP ou regra de rewrite exclusiva. Slugs sem projeto publicado retornam 404. Não há redirect dos projetos fictícios removidos para cases reais.

O servidor de desenvolvimento `deploy/dev-router.php` serve as rotas PHP e arquivos físicos, mas não executa as regras Apache de redirect.
