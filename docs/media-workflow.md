# Fluxo de mídia

## Organização

- Masters: fora do Git e fora do webroot, em `improov-media-masters/projetos/{slug}/`, podendo usar subpastas semânticas (por exemplo `imagens/`, `plantas/`, `animacoes/`, `filmes/` e `pilulas/`).
- Derivados: pacote separado em `assets/media/{slug}/v{mediaVersion}/`.
- Código, manifesto e metadados: versionados.

## Inclusão ou atualização

1. Adicionar o master na pasta externa do projeto.
2. Atualizar `deploy/media-manifest.json` e incrementar `mediaVersion` quando substituir mídia existente.
3. Executar `deploy/media.ps1` em uma máquina com ImageMagick e codecs AVIF/WebP.
4. Conferir os arquivos 640, 1024, 1440 e 1920, além dos posters.
5. Publicar o pacote de mídia separado.
6. Publicar o código somente após o validador confirmar todos os caminhos do manifesto.

Os derivados usam cache imutável de um ano. A mudança de `mediaVersion` invalida o cache pela URL. `thumb.php` permanece como fallback para o acervo legado até não haver referências ou acessos relevantes.

## Conversor atual

O ambiente local desta migração não possui ImageMagick, mas possui Pillow com suporte a JPEG, WebP e AVIF. O conversor executável é:

```powershell
& "C:\Users\pedro\.cache\codex-runtimes\codex-primary-runtime\dependencies\python\python.exe" deploy/generate-media.py
```

Ele lê os masters em `C:\improov-media-masters` (resolvendo recursivamente as subpastas de cada projeto), gera o pacote em `assets/media`, atualiza `data/media-map.json` e atualiza o manifesto. Os arquivos gerados em `assets/media` são pacote de deploy e permanecem fora do Git conforme o `.gitignore`.

## Vídeos

O pipeline de vídeo reutilizável está em `deploy/process-videos.py`. Ele recebe o slug, descobre os masters em `C:\improov-media-masters\projetos\{slug}`, coleta o inventário com `ffprobe`, gera MP4 H.264 com `+faststart`, posters WebP e atualiza `data/video-manifest.json`.

```powershell
& "C:\Users\pedro\.cache\codex-runtimes\codex-primary-runtime\dependencies\python\python.exe" deploy/process-videos.py ars-vie
```

Para mídia institucional mantida fora da pasta de um projeto, o mesmo pipeline
aceita um diretório master explícito e publica no pacote `site`:

```powershell
& "C:\Users\pedro\.cache\codex-runtimes\codex-primary-runtime\dependencies\python\python.exe" deploy/process-videos.py site --source-dir "C:\improov-media-masters\novos" --output-slug site --manifest-key site
```

As variantes ficam em `assets/media/{slug}/v1/videos/{categoria}` e os posters em `assets/media/{slug}/v1/posters/{categoria}`. O script limita o maior eixo público a 1920/1280, preserva proporção e não faz upscale. Os perfis são definidos por categoria: animações priorizam qualidade, filmes preservam áudio AAC e pílulas usam uma compressão mais leve. Pílulas e animações de até 15 segundos recebem `loopCandidate` como indicação auxiliar para o frontend.

A curadoria por projeto fica em `data/video-curation.json`. Quando um projeto possui configuração, o inventário continua sendo coletado para todos os masters, mas somente as fontes explicitamente publicadas geram derivados. Cada fonte pode escolher `publish`, `poster` e `variants` (`1080`, `720`, `source` ou `all`). Fontes sem regra permanecem como `available: true` e `published: false`, sem serem copiadas para os assets públicos. `posterOnly` permite manter um frame WebP, como no `tracking-0040` do ARS_VIE, sem manter o MP4.
