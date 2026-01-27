# Cases

Cada case deve ter um diretório com um `slug` estável.

Sugestão:

```
content/cases/
  nome-do-projeto/
    meta.pt-BR.json
    meta.en.json
    gallery.json
    blocks.pt-BR.json
    blocks.en.json
```

Ideias de campos para `meta.*.json`:
- `title`
- `summary`
- `client`
- `location`
- `date`
- `tags`

`gallery.json` pode listar paths de mídia (que ficam em `public/media/...`).
