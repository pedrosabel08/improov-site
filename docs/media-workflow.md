# Fluxo de mídia

## Organização

- Masters: fora do Git e fora do webroot, em `improov-media-masters/{slug}/`.
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

Ele lê os masters em `C:\improov-media-masters`, gera o pacote em `assets/media`, atualiza `data/media-map.json` e atualiza o manifesto. Os arquivos gerados em `assets/media` são pacote de deploy e permanecem fora do Git conforme o `.gitignore`.
