# Mídias de Nossos serviços

A página usa `data/services-media.json`, separado do catálogo de cases. As imagens continuam no mapa compartilhado; os vídeos referenciam o manifesto e o ID existentes, ou a coleção `nossos-servicos`. O tour é lido da seção `tour` do case RDO. O fluxo principal apresenta os seis serviços do wireframe e, em seguida, filmes documentais e institucionais sempre visíveis, com acesso também pela navegação. Salas Imersivas, sem material, não aparece nesse fluxo.

## Importação

1. Execute `deploy/inventory-services-media.py --source-dir <pasta recebida>` com o Python/Pillow do ambiente. O inventário é somente leitura: registra hashes SHA-256, dimensões, duração e áudio em `cache/services-media/inventory.json`. Compara as fontes com masters e derivados existentes, sem escolher mídia apenas por nome.
2. Execute `deploy/prepare-services-media.py`. Copia apenas fontes ainda necessárias para `C:/improov-media-masters/servicos`, sem alterar a pasta recebida. Verifica hashes dos destinos, preserva masters existentes e grava `master-index.json` fora do repositório. Publica a seleção em `data/services-media.json` e prepara os manifestos das pipelines. Rodar novamente não sobrescreve um master diferente.
3. Execute `deploy/generate-media.py --collection nossos-servicos`. Usa o mesmo conversor AVIF/WebP/JPG e larguras 640/1024/1440/1920 dos cases. A coleção está em `deploy/media-manifest.json`, no campo `collections`; cada entrada contém o master relativo, o identificador público e o nome de saída. A geração pode ser retomada: cada imagem grava o mapa e hashes iguais com todos os derivados existentes são reutilizados. A geração geral preserva as coleções.
4. Execute `deploy/process-videos.py nossos-servicos --source-dir C:/improov-media-masters/servicos --no-clean`. Usa H.264/AAC, faststart, posters WebP e somente a variante 1080 (sem upscale), por meio da pipeline e curadoria compartilhadas. Vídeos idênticos aos cases apontam para seus derivados existentes; não são recodificados.
5. Execute `deploy/prepare-services-media.py --verify` para conferir novamente os hashes da origem e dos masters. Valide todas as referências da página e as saídas do manifesto antes de entregar.

Execute as gerações de imagens em sequência entre si e de vídeos em sequência entre si; os dois tipos usam manifestos diferentes. Não editar os respectivos manifestos durante a conversão. Nunca remover mídia por prefixo ou incluir masters no webroot/Git.

## Curadoria inicial

As listas de filmes mantêm a ordem solicitada. Os dois teasers Phacz e os três capítulos Edify são itens distintos. O JPG recebido dentro de Animações 3D entra na seção de imagens. Duplicatas byte a byte na mesma seção aparecem uma única vez. Plantas preservam enquadramento e orientação, com ampliação em galeria. Filmes iniciam por ação do visitante, na própria página, com controles nativos e opção de tela cheia. O script compartilhado dos cases continua cuidando de pausar vídeos fora da área visível e de impedir reprodução simultânea. Animações respeitam a seção/slide visível, economia de dados e a preferência de movimento reduzido.

Código e pacote de mídia são entregas separadas. Os derivados ficam em `assets/media`, ignorados pelo Git, e precisam acompanhar a publicação do código. A preparação local não publica o site.

## Apresentação e legendas

Hero, navegação horizontal sticky e seções com título e frase curta organizam a página. Imagens 3D vem diretamente após a navegação, sem bloco de atalhos visuais. A identidade clara, tipografia Montserrat, botões, ícones locais e tokens vêm do site existente. A navegação usa o mesmo componente visual numerado dos cases (`assets/css/chapter-nav.css`), fica abaixo do header, acompanha a seção ativa e permite rolagem horizontal no mobile.

`partials/service-gallery.php` usa o mesmo componente visual de carrossel dos cases. Os estilos foram extraídos de `case.css` para `assets/css/gallery-carousel.css`, carregado nas duas páginas: material centralizado, prévias laterais com opacidade e escala reduzidas, setas circulares nas extremidades e legenda com contador diretamente abaixo da mídia. A faixa de thumbnails foi removida. O frame preserva a proporção original, com até 70svh no desktop. O cabeçalho mantém apenas número e título, sem descrição.

No mobile, número e título ficam em sequência vertical. O carrossel usa a largura de slide dos cases, com swipe nativo, scroll-snap e um trabalho em destaque por vez. As imagens mantêm altura natural. Pequenas setas abaixo do trilho oferecem uma alternativa ao gesto. Todas as galerias de serviço, inclusive teasers, usam o mesmo padrão centralizado; o viewer 360º continua independente.

Os carrosséis reaproveitam arraste com mouse ou caneta, toque, setas do teclado e Home/End. O cálculo do slide ativo considera o centro do trilho, como nos cases. O arraste não aciona ampliação ou reprodução. Slides fora da área visível ficam fora da sequência de foco. O lightbox existente abre ao clicar na imagem e usa fundo escuro, imagem completa, Escape, teclado, swipe e retorno de foco; não adiciona biblioteca de zoom. Os players mantêm controles nativos, incluindo tela cheia, reprodução inline e pausa fora da viewport.

Imagens, plantas, poster do tour e imagem do case preservam o enquadramento completo com proporção natural e `object-fit: contain`. As áreas de mídia têm fundo transparente, incluindo wrappers de vídeos, sem painéis coloridos atrás dos materiais.

O tour Valence carrega seu iframe somente após clique, sem delegar permissão de giroscópio ou acelerômetro. A página oferece a indicação “Arraste para explorar” e link direto como alternativa ao embed. Existe apenas um tour estruturado; não são inventados ambientes nem thumbnails de panoramas inexistentes.

O hero usa a animação definida no case `hsa-mon` (Monticello Residenziale), com poster, loop e reprodução muted pelo helper compartilhado `lazy_video()`. `service_hero_video()` prioriza os derivados do case e, quando ausentes localmente, reutiliza um derivado disponível com o mesmo SHA-256 da origem; não escolhe por semelhança de nome nem duplica vídeos. A transição “Da imagem à narrativa” usa uma imagem real da sauna em tratamento monocromático. O destaque final usa o nome cadastrado do projeto e verifica os IDs de seções do case antes de listar imagens, plantas, animações e filmes conceitos. Não atribui 360º ou teaser a esse empreendimento. CTAs usam as rotas existentes de contato e cases. Os textos estão em `data/revision-copy.json` em PT/EN/ES.

Imagens usam `responsive_image()` e a pipeline existente de AVIF/WebP/JPG, com prioridade somente para o hero e lazy loading nas demais mídias. Vídeos têm posters adiados, `preload="none"` e fontes hidratadas apenas ao reproduzir ou ao entrar uma animação ativa na área visível. Não há clones nem bibliotecas novas.

`data/services-labels.json` guarda os nomes editoriais por SHA-256, refletidos em `data/services-media.json`. Os nomes longos de 35 imagens/cenas foram encurtados nos dois catálogos para manter a curadoria nas próximas importações, preservando empreendimento, ambiente e ângulo/unidade quando relevantes. A apresentação usa `empreendimento — nome da imagem/cena`, com peso 500 e cor um pouco mais forte no empreendimento, inclusive no lightbox. Todas as mídias exibem a identificação disponível, inclusive os nomes de ambientes MAX_BRA antes ocultos. Tracking_VERT_004 mantém essa legenda exata.

Pendências editoriais preservadas: quatro imagens MAX_BRA e Tracking_VERT_004 não têm empreendimento informado. Seus nomes disponíveis são exibidos, sem inventar o empreendimento. Preencher esses dados no catálogo de legendas para identificação completa. O tour não possui catálogo de ambientes individuais. Outros tours e materiais adicionais para o case destacado dependem de cadastro real; o layout não usa nomes ou arquivos fictícios.

A planta vertical de Vieiras foi preparada com `rotate-floorplans.ps1` (que chama `rotate-floorplans.py`) em `C:/improov-media-masters/servicos/_prepared/plantas-horizontal-20261026`. O script materializa EXIF e aplica 270° no sentido horário somente quando a imagem continua vertical. As outras três plantas já eram horizontais e mantêm seus derivados anteriores. O campo `preparation` da seleção registra o master preparado e seu hash, sem modificar o hash da origem.
