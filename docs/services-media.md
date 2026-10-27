# Mídias de Nossos serviços

A página usa `data/services-media.json`, separado do catálogo de cases. As imagens continuam no mapa compartilhado; os vídeos referenciam o manifesto e o ID existentes, ou a coleção `nossos-servicos`. O tour é lido da seção `tour` do case RDO. Salas Imersivas permanece sem conteúdo.

## Importação

1. Execute `deploy/inventory-services-media.py --source-dir <pasta recebida>` com o Python/Pillow do ambiente. O inventário é somente leitura: registra hashes SHA-256, dimensões, duração e áudio em `cache/services-media/inventory.json`. Compara as fontes com masters e derivados existentes, sem escolher mídia apenas por nome.
2. Execute `deploy/prepare-services-media.py`. Copia apenas fontes ainda necessárias para `C:/improov-media-masters/servicos`, sem alterar a pasta recebida. Verifica hashes dos destinos, preserva masters existentes e grava `master-index.json` fora do repositório. Publica a seleção em `data/services-media.json` e prepara os manifestos das pipelines. Rodar novamente não sobrescreve um master diferente.
3. Execute `deploy/generate-media.py --collection nossos-servicos`. Usa o mesmo conversor AVIF/WebP/JPG e larguras 640/1024/1440/1920 dos cases. A coleção está em `deploy/media-manifest.json`, no campo `collections`; cada entrada contém o master relativo, o identificador público e o nome de saída. A geração pode ser retomada: cada imagem grava o mapa e hashes iguais com todos os derivados existentes são reutilizados. A geração geral preserva as coleções.
4. Execute `deploy/process-videos.py nossos-servicos --source-dir C:/improov-media-masters/servicos --no-clean`. Usa H.264/AAC, faststart, posters WebP e somente a variante 1080 (sem upscale), por meio da pipeline e curadoria compartilhadas. Vídeos idênticos aos cases apontam para seus derivados existentes; não são recodificados.
5. Execute `deploy/prepare-services-media.py --verify` para conferir novamente os hashes da origem e dos masters. Valide todas as referências da página e as saídas do manifesto antes de entregar.

Execute as gerações de imagens em sequência entre si e de vídeos em sequência entre si; os dois tipos usam manifestos diferentes. Não editar os respectivos manifestos durante a conversão. Nunca remover mídia por prefixo ou incluir masters no webroot/Git.

## Curadoria inicial

As listas de filmes mantêm a ordem solicitada. Os dois teasers Phacz e os três capítulos Edify são itens distintos. O JPG recebido dentro de Animações 3D entra na seção de imagens. Duplicatas byte a byte na mesma seção aparecem uma única vez. Plantas e imagens preservam enquadramento e orientação, com ampliação em galeria. Filmes iniciam por ação do visitante; a tela cheia usa o componente compartilhado dos cases. Animações respeitam a seção/slide visível e a preferência de movimento reduzido.

Código e pacote de mídia são entregas separadas. Os derivados ficam em `assets/media`, ignorados pelo Git, e precisam acompanhar a publicação do código. A preparação local não publica o site.

## Apresentação e legendas

Imagens e plantas usam um carrossel editorial centralizado, com os vizinhos parcialmente visíveis. Os clones decorativos são ignorados pela galeria, contagem e acessibilidade. Vídeos mantêm dois cards em desktop/tablet e adaptação para toque no celular. As molduras dos materiais foram removidas; fotografias preenchem o enquadramento, enquanto plantas preservam sua composição completa.

Os carrosséis aceitam arraste com mouse ou caneta e mantêm o deslizamento nativo por toque. O arraste não aciona o clique de ampliação ou reprodução. A altura das seções com conteúdo usa `--service-section-height`, ajustada por tela; nos carrosséis editoriais, o trilho acompanha a altura do card ativo para que os controles permaneçam próximos ao material.

`data/services-labels.json` guarda os nomes aprovados por SHA-256. O padrão é `empreendimento - nome da imagem/cena`. MAX_BRA não exibe legenda, por solicitação; Tracking_VERT_004 mantém essa legenda exata. As importações preservam esse catálogo.

A planta vertical de Vieiras foi preparada com `rotate-floorplans.ps1` (que chama `rotate-floorplans.py`) em `C:/improov-media-masters/servicos/_prepared/plantas-horizontal-20261026`. O script materializa EXIF e aplica 270° no sentido horário somente quando a imagem continua vertical. As outras três plantas já eram horizontais e mantêm seus derivados anteriores. O campo `preparation` da seleção registra o master preparado e seu hash, sem modificar o hash da origem.
