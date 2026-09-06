# Fluxo de mídia

O procedimento completo para novos cases está em [CASE_ONBOARDING.md](CASE_ONBOARDING.md). Leia-o antes de incluir ou substituir mídia.

- Masters originais: `C:\improov-media-masters\projetos\{slug}`, fora do Git e do webroot. Não modificar nem usar derivados como nova fonte.
- Imagens: `deploy/generate-media.py`, com Pillow AVIF/WebP/JPG. Use `--project` e `--media` juntos para geração parcial. A entrada do projeto precisa existir no manifesto de imagens. Sem filtros, o script regenera o catálogo e mídias institucionais.
- Vídeos: `deploy/process-videos.py {slug} --no-clean`, com FFmpeg/ffprobe e curadoria em `data/video-curation.json`. `--no-clean` preserva saídas anteriores; revisar referências antes de permitir exclusões.
- Saídas: `assets/media/{slug}/v1`, incluindo `videos/{categoria}` e `posters/{categoria}`. Somente derivados necessários. Pacote de mídia separado, ignorado pelo Git.
- Mapas: `data/media-map.json`, `deploy/media-manifest.json` e `data/video-manifest.json`. São dados versionados e devem acompanhar os arquivos públicos.

As imagens usam larguras de até 640, 1024, 1440 e 1920, em três formatos, sem upscale. Vídeos usam H.264/AAC e faststart; posters WebP. AYA e Alpes publicam apenas a variante 1080, compatível com o seletor atual do frontend.

Execute as gerações de imagem em sequência entre si e os projetos de vídeo em sequência entre si: escrevem manifestos compartilhados. Não modificar os mesmos manifestos durante o processamento.

As pipelines atuais escrevem em `v1`; alterar somente `mediaVersion` não muda o diretório gerado. O runtime acrescenta a data de modificação às URLs via `asset()`. O cache Apache é imutável por um ano, portanto é necessário conferir a mudança das URLs quando substituir derivados.

`deploy/media.ps1` é uma alternativa anterior; o fluxo consolidado usa os scripts Python citados acima. `thumb.php` permanece como fallback do acervo legado; cases com mapa usam os derivados para imagens, miniaturas e Open Graph.
