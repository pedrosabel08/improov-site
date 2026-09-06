# Inclusão de cases

## Leia antes de implementar

Este é o procedimento consolidado após a inclusão de ARS Vieiras (`ars-vie`), AYA Karioó (`aya-kar`) e Alpes Catarina (`alp-sc`). Leia este arquivo antes de incluir um case e confirme rapidamente a existência dos caminhos, scripts e componentes citados. Pequenas diferenças não justificam redesenhar a arquitetura. Informe divergências estruturais relevantes antes de seguir. O código atual é a referência final quando esta documentação estiver comprovadamente desatualizada. Atualize este documento junto com qualquer alteração estrutural futura no processo.

## Arquitetura e fontes de verdade

O fluxo é: pasta recebida → masters externos → pipeline compartilhada → derivados públicos → dados do case → componentes compartilhados.

| Responsabilidade                                | Caminho real                                                          |
| ----------------------------------------------- | --------------------------------------------------------------------- |
| Repositório local                               | `C:\xampp\htdocs\improov-site`                                        |
| Originais dos projetos                          | `C:\improov-media-masters\projetos`                                   |
| Masters atuais                                  | `ars-vie`, `aya-kar`, `alp-sc` dentro da raiz acima                   |
| Catálogo, cards, textos e inventário de imagens | `data/projects.json`                                                  |
| Composição editorial e seleção de mídia         | `data/cases.json`                                                     |
| Mapa de imagens derivadas em runtime            | `data/media-map.json`                                                 |
| Manifesto de fontes das imagens                 | `deploy/media-manifest.json`                                          |
| Curadoria explícita de vídeos                   | `data/video-curation.json`                                            |
| Inventário e derivados de vídeo                 | `data/video-manifest.json`                                            |
| Imagens públicas                                | `assets/media/{slug}/v1/`                                             |
| Vídeos e posters públicos                       | `assets/media/{slug}/v1/videos/{categoria}/` e `posters/{categoria}/` |

`data/projects.json` registra o projeto; `data/cases.json` determina a página. Não existe arquivo PHP por slug. `index.php` e `app/routes.php` resolvem `/projetos/{slug}`; `pages/project-detail.php` usa `partials/case-detail.php` quando há configuração editorial. `app/projects.php` filtra os publicados e ordena o catálogo. `pages/projetos.php`, `pages/home.php` e `partials/project-grid.php` reutilizam esse cadastro.

`app/cases.php` resolve vídeos, tamanhos, configuração e próximo projeto. `app/content.php` monta imagens responsivas, miniaturas e vídeos dos cards. O mapa permite usar um identificador lógico `assets/projetos/...` sem manter o JPG master no webroot: todas as variantes públicas devem existir em `assets/media`. `thumbnail_url()` e o Open Graph também usam derivados quando há mapa. `thumb.php` é apenas fallback legado.

Os arquivos de código e JSON pertencem ao Git. `assets/media` é um pacote de publicação separado, ignorado pelo `.gitignore`. Gerar arquivos nessa pasta não os inclui automaticamente num commit ou deploy. Não usar `git add -f` para incluir masters ou todos os assets. `assets/projetos/projetos.json` é somente compatibilidade com o leitor HTML legado, também ignorada; não é a fonte do site PHP.

## Slugs, originais e nomenclatura

Os códigos editoriais AYA_KAR, ALP_SC e ARS_VIE correspondem aos diretórios `aya-kar`, `alp-sc` e `ars-vie`: lowercase e hífen. Use o mesmo slug nos dois cadastros, nos manifestos e na pasta master. O código com underscore não é uma chave obrigatória de runtime.

As subpastas usadas são `imagens`, `plantas`, `animacoes`, `filmes` e, quando houver material, `pilulas`. No ARS e nas novas cópias de AYA/Alpes, nomes ASCII normalizados identificam categoria, código e conteúdo, por exemplo:

- `alp-sc/imagens/imagem-1-alp-sc-fotomontagem-1-r00-2-1.jpg`;
- `alp-sc/animacoes/animacao-2-alp-sc-conceito.mp4`;
- `alp-sc/filmes/filme-alpes-sc-institucional-en.mp4`.

AYA já possuía oito JPGs com nomes antigos na raiz de seus masters. Foram preservados e reutilizados após comparação de hash. Não renomeie material existente só para uniformizar estética. Para novos arquivos, evite colisões de nomes normalizados, inclusive entre subpastas. O resolvedor de imagem compara nomes normalizados dentro do projeto, não apenas o caminho completo; o ID público do vídeo deriva do nome do arquivo, sem extensão.

Ao receber outra pasta externa:

1. Inventarie arquivos, dimensões, duração, presença de áudio e categorias. Diferencie animação de Hero, animação editorial, plantas, pílulas e filmes. Nomes são pistas; confirme o conteúdo.
2. Defina a composição usando apenas material existente e dados aprovados. Não imponha a sequência do ARS a todo projeto.
3. Compare com a pasta master existente. Reutilize arquivos idênticos; não sobrescreva um arquivo diferente com o mesmo nome.
4. Copie os originais para a pasta oficial, sem mover a pasta recebida ou converter seus arquivos. Reorganização e nomes de destino não podem alterar os bytes.
5. Registre correspondências e SHA-256. `aya-kar/master-index.json` e `alp-sc/master-index.json` são exemplos reais: caminho de origem, destino, categoria, tamanho, hash e indicação de arquivo reutilizado. Esse índice é de auditoria, não é configuração exigida pelas pipelines.
6. Após gerar derivados, compare novamente os hashes tanto da pasta recebida como dos masters oficiais.

Nunca substitua originais por arquivos otimizados, use derivados como entrada quando existe original, ou copie masters pesados para o repositório.

## Cadastro do projeto

Em `data/projects.json`, acrescente um objeto em `projects`, seguindo Alpes como exemplo mínimo:

- `slug`, `status: "published"`, `order`, `placement: "standard"`, `showOnHome` e `homeOrder` controlam listagem e Home. `order` e `homeOrder` são ordens distintas.
- `title` e `location` são objetos com `pt-BR`, `en`, `es`.
- `media.hero` contém `src`, `width`, `height` e `alt` traduzido. É a imagem estática do card/fallback e dos metadados.
- `media.gallery` contém todas as demais imagens que a pipeline deve gerar, cada uma com `src`, `width`, `height`. A primeira é `gallery-01`, a segunda `gallery-02`, etc. Referenciar uma imagem só em `cases.json` não a gera.
- `media.animation`, quando houver card animado, usa `{ "manifest": "alp-sc", "id": "animacao-2-alp-sc-conceito" }`. O manifesto precisa ter esse vídeo publicado.
- `detail.subtitle`, `detail.description` por idioma e `detail.info` (`client`, `architect`, `year`) guardam dados aprovados. Campos ausentes ficam vazios, sem texto fictício. `mediaVersion` atual é `1`.

Os cards apontam para `base_url('projetos/' + slug)`. Não criar cards fixos para preencher layout. Depois da inclusão, os únicos projetos atuais são ARS, AYA e Alpes. Se o HTML legado ainda for distribuído, mantenha seu espelho de dados coerente com os projetos reais e os derivados.

Em `data/cases.json`, adicione a chave do slug dentro de `cases`. Reutilize `theme: "light"`, `motion: "slow"`, `hero`, `services`, `sections`, `credits` e `nextProject` conforme necessário. Não condicionar PHP/JS/CSS ao nome do projeto.

## Composição e componentes

`sections` define a ordem e as mídias; não há preenchimento automático pelo inventário nem limite arbitrário de plantas ou animações. Seções vazias são filtradas. `closing` é a ficha técnica e o componente a posiciona antes das demais seções. IDs de seção devem ser únicos; o HTML acrescenta `case-`. `label` e `title` podem ser traduzidos. `navigation: false` omite uma seção do menu de capítulos e sua numeração, sem ocultar o conteúdo. No AYA, implantação/acesso, piscinas, rooftop e animações seguem na página, mas não entram nas âncoras; o menu lista ficha técnica, fachadas/áreas comuns, apartamentos, plantas e os dois filmes. Nas galerias e carrosséis, `showHeading: false` também omite o cabeçalho visível. AYA usa essa opção nos trechos editoriais sem divisão em texto; as imagens e animações permanecem no fluxo.

| Tipo             | Dados principais                                | Uso e comportamento                                                           |
| ---------------- | ----------------------------------------------- | ----------------------------------------------------------------------------- |
| `gallery`        | `groups`, `items` ou `moments`                  | Imagens editoriais, pares, trios e mosaicos; ampliação compartilhada          |
| `carousel`       | `items: [{src, label}]` ou `[{mediaId, label}]` | Imagens, plantas ou animações, trilho horizontal com snap, setas e teclado    |
| `editorialBlock` | `items` com um vídeo e duas imagens             | Composição editorial mista; só renderiza com esse conjunto completo           |
| `interlude`      | `items` com imagens e/ou vídeos                 | Intervalo visual usando componente compartilhado                              |
| `stillMotion`    | `environment`                                   | Alterna still e animação de um ambiente cadastrado em `environments`          |
| `animations`     | `steps`, cada um com `layout` e `items`         | Narrativa de animações por etapas; adaptação compacta em tablet/mobile        |
| `motion`         | `items` com IDs de vídeo                        | Lista de animações com reprodução por visibilidade                            |
| `floorplans`     | `items: [{id, src, label}]`                     | Visualizador com abas e dialog; alternativa existente ao carrossel de plantas |
| `moments`        | `items` com IDs de ambientes                    | Pílulas dos ambientes, trilho e controles de interação existentes             |
| `film`           | `videos: [{video, label, title?}]`              | Um ou vários filmes com controles nativos e capítulos individuais             |
| `closing`        | Dados em `credits.items` do case                | Ficha técnica; valores vazios não aparecem                                    |

Para campos opcionais e layouts avançados, use uma entrada existente do mesmo tipo e confira o ramo correspondente de `partials/case-detail.php`. Não escolha layouts não suportados. `environments` relaciona `id`, `label`, `still`, `motion` e `pill`; referências de planta/hotspot existentes não tornam plantas interativas automaticamente.

### Exemplos reais mínimos

Hero do Alpes:

```json
"hero": {
  "video": "animacao-2-alp-sc-conceito",
  "label": {"pt-BR":"Improov / Case","en":"Improov / Case","es":"Improov / Case"}
}
```

O poster vem do vídeo no manifesto. `posterVideo` também pode selecionar um registro de poster. Sem vídeo válido, usa a imagem estática do cadastro. `heroFooter` é opcional; `services` fornece a lista de entregas.

Galeria editorial: `groups[].type: "editorialStory"`, `lightboxSet` comum e `moments` com `layout: "feature"` e uma imagem por momento. Essa é a estrutura usada pelas três imagens do Alpes. Os layouts `mosaic-a` e `mosaic-b` reutilizam o componente existente quando há volume e composição aprovados para mosaico.

O carrossel `plantas-humanizadas` de AYA usa 21 itens `{src, label}`; esse ID identifica o tratamento visual de planta no carrossel. Todas mantêm sua orientação natural. Não existe script de rotação automática. Use as imagens completas com `contain`.

Filme do Alpes, idêntico em todos os idiomas:

```json
{
  "type": "film",
  "id": "filme",
  "label": { "pt-BR": "Filme", "en": "Film", "es": "Película" },
  "videos": [
    {
      "video": "filme-alpes-sc-institucional-en",
      "label": {
        "pt-BR": "Filme Conceito",
        "en": "Concept Film",
        "es": "Película Concepto"
      }
    }
  ]
}
```

O idioma da interface não exige uma cópia de vídeo por idioma. Não traduzir nem dublar o arquivo sem pedido. A ficha técnica de Alpes contém somente o nome do projeto; cliente, arquitetura, localização e ano aguardam informação. AYA preserva os dados existentes.

`nextProject: {"mode":"editorial"}` percorre os projetos publicados que possuem case. Também admite `slug` explícito para um próximo projeto aprovado. Não apontar para conteúdo fictício.

## Pipeline de imagens

Script oficial atual: `deploy/generate-media.py`, Python com Pillow e suporte a AVIF/WebP. Não duplicar o conversor por projeto. Ele procura recursivamente em `C:\improov-media-masters`, priorizando a pasta correspondente ao slug em `projetos` e comparando o nome normalizado do master. Confirme no log o master escolhido.

Antes da primeira geração parcial, adicione a entrada do novo slug em `deploy/media-manifest.json`: `{ "slug": "alp-sc", "mediaVersion": 1, "media": [] }`. Preserve todas as outras entradas e campos do manifesto. O script preenche `media` com a fonte relativa ao diretório master.

Comandos reais, executados a partir do repositório:

```powershell
$casePython = 'C:\Users\pedro\.cache\codex-runtimes\codex-primary-runtime\dependencies\python\python.exe'
& $casePython deploy/generate-media.py --project alp-sc --media hero
& $casePython deploy/generate-media.py --project alp-sc --media gallery-01
& $casePython deploy/generate-media.py --project alp-sc --media gallery-02
```

`--project` e `--media` devem ser usados juntos. Não há modo parcial apenas por projeto. `--media` aceita `hero` e `gallery-NN`, de acordo com o índice em `projects.json`. Execute uma chamada por imagem nova, em sequência: cada execução lê e grava os mesmos manifestos, portanto chamadas de imagem concorrentes podem perder entradas.

Sem filtros, o script regenera o catálogo e mídias institucionais e reescreve os manifestos. Não use esse modo para adicionar apenas um case.

Saídas: AVIF (qualidade 90), WebP (88) e JPG (90), larguras 640, 1024, 1440 e 1920. Corrige orientação EXIF na cópia, mantém proporções, não aumenta um original menor e evita gerar larguras repetidas. Um original com largura suficiente produz 12 variantes. Exemplo: `assets/media/alp-sc/v1/hero-640.avif` até `hero-1920.jpg`. Atualiza `data/media-map.json` e `deploy/media-manifest.json`.

Não reordene `media.gallery` de um projeto já publicado sem revisar os nomes derivados e o cache; os nomes são baseados em posição. Para novas imagens, prefira acrescentar entradas. A posição visual na página é independente e fica em `cases.json`.

## Pipeline de vídeos e posters

Script oficial: `deploy/process-videos.py`, com FFmpeg e ffprobe. O argumento posicional é o slug; a raiz padrão é `C:\improov-media-masters\projetos`. A categoria vem da organização do master. Use `data/video-curation.json` para publicar apenas material referenciado pelo case ou card.

Exemplo real de regra do Alpes (a outra fonte é o filme):

```json
"alp-sc": {
  "defaults": {"publish":false,"poster":false,"variants":[]},
  "sources": {
    "animacoes/animacao-2-alp-sc-conceito.mp4": {
      "publish":true,"poster":true,"variants":["1080"]
    },
    "filmes/filme-alpes-sc-institucional-en.mp4": {
      "publish":true,"poster":true,"variants":["1080"]
    }
  }
}
```

As chaves `sources` são caminhos relativos exatos dentro do master do projeto. O inventário inclui também fontes não publicadas, mas elas não precisam produzir arquivos públicos.

```powershell
& $casePython deploy/process-videos.py aya-kar --no-clean
& $casePython deploy/process-videos.py alp-sc --no-clean
```

Execute os projetos de vídeo em sequência; compartilham `data/video-manifest.json`. A geração de imagens e a de vídeos usam manifestos diferentes. Não faça alterações manuais nos respectivos manifestos durante uma execução.

Opções existentes:

- `--masters-root`: outra raiz de masters de projetos;
- `--source-dir`, `--output-slug`, `--manifest-key`: fonte e destino explícitos, usados por mídia institucional;
- `--output-root`: raiz de saída; `--curation`: configuração de curadoria;
- `--ffmpeg`, `--ffprobe`: executáveis explícitos quando não encontrados;
- `--inventory-only`: coleta inventário sem converter, mas **grava o manifesto**, não é apenas leitura;
- `--reindex-existing`: reconstrói os registros validando derivados existentes;
- `--no-clean`: preserva saídas anteriores. Sem essa opção, o script remove derivados de vídeo/poster que não estão no conjunto esperado. Revise referências antes de usar limpeza.

As variantes aceitas na curadoria são `1080`, `720`, `source` e `all`. Os perfis limitam o maior eixo a 1920/1280 e não fazem upscale; o rótulo não implica altura fixa em vídeo vertical. O frontend seleciona a maior variante numérica, sem streaming adaptativo. AYA e Alpes usam somente `1080`, suficiente para o comportamento atual; não gerar uma segunda variante sem consumidor real.

Saída MP4 H.264, pixel format compatível e `+faststart`, mantendo proporção e áudio quando existente (AAC). Perfis: animações/outros CRF 19, preset slow; filmes CRF 21, slow; pílulas CRF 23, medium. Áudio 160k nos primeiros e 128k em pílulas. Posters WebP usam frame de aproximadamente 35% da duração e maior eixo até 1600. O script valida codecs, dimensões, áudio, faststart e posters. Hash, perfil e existência do derivado permitem reutilização numa repetição sem alteração.

`data/video-manifest.json` contém inventário, `videos`, `posterOnly`, `errors` e `summary`. Exija `errors: []`. `case_video_source()` escolhe uma fonte publicada. Não editar caminhos para apontar para masters. A geração concluída para AYA registra 18 vídeos e 18 posters; Alpes, 2 e 2. As imagens utilizadas são 45 no AYA e 3 no Alpes, totalizando 540 e 36 variantes respectivamente.

## Comportamentos compartilhados

Reutilize `partials/case-detail.php`, `app/cases.php`, `app/content.php`, `assets/js/case.js` e `assets/css/case.css`. Não copiar blocos para um arquivo por case.

- Hero usa vídeo mudo, inline e loop, condicionado à viewport e à preferência de movimento reduzido. O poster/imagem fornece fallback.
- Imagens usam `<picture>` AVIF/WebP/JPG, `srcset` e `sizes` definidos pelo layout; dimensões intrínsecas evitam saltos. Predomina lazy loading, com prioridade para mídia inicial e pré-carga de imagens adjacentes ao carrossel.
- Hero e mosaicos podem usar `cover`; fotos editoriais feature, carrosséis, plantas, filmes e dialog preservam a imagem com `contain` ou dimensões naturais conforme o componente. Não aplicar um `cover` global.
- Carrosséis têm scroll/snap, arraste, setas, Home/End e navegação horizontal por teclado. Só a animação ativa e visível é reproduzida. Os slides vizinhos podem ser carregados antes da interação.
- Vídeos editoriais são carregados perto da viewport e pausados fora dela. Animações/pílulas curtas podem usar `loopCandidate`; nem todo vídeo é um loop. Respeitar `prefers-reduced-motion`.
- Pílulas reutilizam as interações de hover, clique e áudio já existentes; não configurar uma seção se o projeto não possui pílulas.
- Filmes usam controles nativos, `playsinline`, `preload="none"`, poster e reprodução iniciada pelo visitante. O clique em qualquer ponto da área do filme inicia a reprodução e solicita tela cheia. O gatilho também funciona por teclado; os controles nativos ficam disponíveis em tela cheia e como fallback se ela não for permitida. Ao sair da tela cheia, a reprodução pausa. Não há autoplay ao carregar a página. Pausam fora da viewport ou quando a página fica oculta; iniciar um filme pausa os demais.
- Dialog de imagens abre por clique/teclado, agrupa imagens por conjunto, tem contador, anterior/próxima, setas e Escape; devolve foco ao gatilho. Mantém proporção e orientação original. Não depende de script que gire plantas verticais.
- Os breakpoints principais são 767 e 1024 px. Mobile mantém trilhos deslizáveis e converte composições densas conforme as regras existentes; o desktop usa o espaço editorial. Conferir em 390, 768 e 1280 px, incluindo transição entre larguras.

## SEO, cache e publicação

Título, descrição e imagem do case vêm de `projects.json`. Sem descrição aprovada, usa a descrição geral de Projetos. `partials/head.php` monta canonical, Open Graph e breadcrumbs. Adicione a URL canônica ao `sitemap.xml`. Não cadastrar redirect legado sem uma URL real correspondente.

O runtime usa `asset()` com a data de modificação do arquivo como parâmetro de versão. As pipelines atuais escrevem em `v1`: mudar só `mediaVersion` no JSON **não** muda a pasta gerada. Preserve coerência entre arquivos e mapas e confira as URLs quando substituir um derivado. A política Apache usa cache imutável de um ano; uma evolução real de diretórios de versão exige ajuste conjunto da pipeline e dos manifestos.

Código e pacote de mídia precisam ser publicados de forma coordenada, com os derivados presentes antes de disponibilizar as referências novas. Inclusão local não equivale a deploy. Não enviar masters. Não publicar sem autorização para o ambiente de destino.

## Sequência operacional e validação

1. Receber a pasta; ler este documento e confirmar os pontos de entrada.
2. Inventariar as mídias e aprovar composição e dados, identificando pendências.
3. Criar/completar o diretório master com cópias preservadas e índice de hashes.
4. Cadastrar projeto e case; adicionar entrada inicial de imagens e curadoria de vídeo.
5. Gerar somente os derivados necessários com os scripts oficiais.
6. Conferir todos os identificadores e arquivos referenciados, incluindo posters, variantes, Hero, metadados, cards e próximo projeto. Uma seção filtrada por mídia ausente não conta como sucesso.
7. Conferir a listagem e Home: somente projetos reais, ordem e links corretos.
8. Abrir os cases em desktop, tablet e mobile; testar navegação, carrosséis, dialogs, orientação das plantas, reprodução e pausa.
9. Conferir PT/EN/ES, srcset/sizes, imagens carregadas sem erro, controles de vídeo e console.
10. Comparar hashes dos masters e preservar os casos existentes; não recomprimir suas mídias sem necessidade.
11. Rodar verificações aplicáveis e revisar o diff. Buscar globalmente slugs removidos e caminhos quebrados, inclusive arquivos ignorados usados pelo site.
12. Preparar código e pacote de mídia para a publicação autorizada e atualizar esta documentação se o processo mudou.

Comandos locais de verificação:

```powershell
& 'C:\xampp\php\php.exe' -l partials/case-detail.php
& 'C:\xampp\php\php.exe' -l app/content.php
& 'C:\xampp\php\php.exe' -l partials/head.php
& 'C:\Users\pedro\.cache\codex-runtimes\codex-primary-runtime\dependencies\node\bin\node.exe' --check assets/js/case.js
& 'C:\php\php.exe' vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.php --dry-run --using-cache=no --path-mode=intersection app/content.php partials/head.php partials/case-detail.php
git diff --check
```

O PHP CS Fixer instalado exige PHP compatível com suas dependências; neste ambiente use `C:\php\php.exe` (8.3), pois o PHP 8.2 do XAMPP não interpreta uma dependência atual do linter. Não alterar o Composer para contornar essa diferença. A configuração `.php-cs-fixer.php` é local; confirme disponibilidade antes de executar.

Servidor opcional de preview, se não houver Apache ativo:

```powershell
& 'C:\xampp\php\php.exe' -S 127.0.0.1:8080 -t C:\xampp\htdocs C:\xampp\htdocs\improov-site\deploy\dev-router.php
```

Abra `http://127.0.0.1:8080/improov-site/projetos`. O roteador de desenvolvimento não testa redirects de `.htaccess`; estes precisam ser conferidos no Apache.

### Checklist final

- [ ] Masters presentes no diretório oficial e hashes preservados nas duas origens.
- [ ] Derivados gerados pela pipeline oficial; nenhum master pesado no repositório.
- [ ] Projeto cadastrado, sem conteúdo fictício ou informação inventada.
- [ ] Hero, imagens, vídeos, áudio quando aplicável e posters funcionando.
- [ ] Todos os `src`, `srcset`, `sizes`, IDs de vídeo e caminhos dos mapas conferidos.
- [ ] Carrosséis, setas, snap, dialog, Escape e retorno de foco funcionando.
- [ ] Plantas completas e orientação natural preservada.
- [ ] Autoplay condicionado à viewport e pause verificados; filme sem autoplay.
- [ ] Desktop, tablet e mobile validados, sem overflow horizontal da página.
- [ ] Página Projetos e Home atualizadas; ordem, links e próximo projeto corretos.
- [ ] Idiomas, metadados, sitemap e console conferidos.
- [ ] Nenhum caminho inexistente, placeholder introduzido ou código específico desnecessário.
- [ ] Cases existentes preservados, verificações aplicáveis executadas e diff revisado.

Não redesenhar o site, refatorar áreas não relacionadas, duplicar CSS/JS/pipeline ou inventar outra convenção. Não alterar cases existentes sem necessidade. Nunca remover arquivos por prefixo: confirme referências compartilhadas antes de limpar qualquer asset.
