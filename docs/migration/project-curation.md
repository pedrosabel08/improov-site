# Curadoria dos projetos

Fonte de runtime: `data/projects.json`. Composição editorial: `data/cases.json`.

O catálogo contém somente ARS Vieiras (`ars-vie`), AYA Karioó (`aya-kar`) e Alpes Catarina (`alp-sc`), nesta ordem. Os três usam os mesmos componentes PHP, CSS e JavaScript.

- ARS mantém a sequência editorial e as mídias existentes. A descrição temporária foi retirada; `needsEditorialReview` continua indicando a pendência de texto aprovado.
- AYA contém as imagens, plantas, animações e dois filmes aprovados, derivados dos masters externos.
- Alpes contém três imagens, Hero animado e um filme em inglês, compartilhado entre PT, EN e ES. Cliente, arquitetura, ano e localização aguardam informação; não preencher por inferência.
- O nome AYA Karioó e os dados técnicos de AYA foram preservados do cadastro existente.

`assets/projetos/projetos.json` é um espelho de compatibilidade do leitor legado, ignorado pelo Git. Não alimenta o site PHP. Seus registros reais usam imagens derivadas e precisam acompanhar o pacote legado se ele ainda for distribuído.

Os registros fictícios, demonstrações e mídias exclusivas aprovados para remoção foram retirados. Imagens compartilhadas com páginas institucionais e metadados permanecem disponíveis; o prefixo de um arquivo não comprova que ele seja descartável.

Para novos cases, seguir [CASE_ONBOARDING.md](../CASE_ONBOARDING.md).
