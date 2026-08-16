# Inventário de rotas

Base atual: `/improov-site/`.

| URL antiga                              | URL nova                                | Ação                   |
| --------------------------------------- | --------------------------------------- | ---------------------- |
| `/index.html`                           | `/`                                     | 301                    |
| `/privacidade.html`                     | `/privacidade`                          | 301                    |
| `/Projetos/index.html`                  | `/projetos`                             | 301                    |
| `/Projetos/aya-kar`                     | `/projetos/aya-kar`                     | 301 confirmado         |
| `/Projetos/adega-luz-sombra`            | `/projetos/adega-luz-sombra`            | 301 confirmado         |
| `/Projetos/academia-energia-urbana`     | `/projetos/academia-energia-urbana`     | 301 confirmado no JSON |
| `/Projetos/brinquedoteca-cor-movimento` | `/projetos/brinquedoteca-cor-movimento` | 301 confirmado no JSON |
| `/Projetos/ars-vie`                     | `/projetos/ars-vie`                     | 301 confirmado no JSON |

As variantes dos cinco slugs confirmados na raiz e em `Projetos/detalhes.html?id=...` também recebem 301. Outros slugs retornam 404; não há fallback genérico.

## Pendências editoriais

Os links antigos `academia-versao-noturna`, `iluminacao-foco-criativo` e `espaco-minimal-vivo` não têm correspondência confirmada. Não recebem redirect até aprovação editorial e consulta aos logs/Search Console.
