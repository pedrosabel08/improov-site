# Escopo proposto — Valence RDO

Data: 08/10/2026. Escopo aprovado e implementado localmente em `/projetos/rdo-val`.

Origem: `C:\Users\pedro\OneDrive\Documentos\Cases Site\Valence RDO`.

Inventário: **45 arquivos — 31 JPGs e 14 MP4s**, aproximadamente 5,93 GB. A conferência visual incluiu todas as imagens e uma miniatura extraída de cada vídeo; não corresponde à revisão integral de reprodução dos vídeos. Dimensões e durações dos vídeos foram lidas pelos metadados do Windows. Após a revisão solicitada, 44 materiais aparecem na página; a primeira versão da sacada 1704 fica somente no acervo. O vídeo do Hero também é reutilizado no card animado.

## Ordem e disposição

| Etapa | Seção | Disposição |
| --- | --- | --- |
| 01 | Hero | Vídeo de abertura em loop mudo, também usado no card animado; poster derivado desse vídeo. |
| 02 | Ficha técnica | Valence Residence; RDO Empreendimentos Imobiliários; Viva Park - Porto Belo - SC; Dória Arquitetos. |
| 03 | Corpo 1 | Vídeo Fachada Ampla em destaque; abaixo, fachada do observador 1 à esquerda e embasamento/Rua A à direita. |
| 04 | Carrossel Fachadas | 6 imagens: implantação geral → topo → relação com o parque → fachada do observador → embasamento → mall. |
| 05 | Corpo 2 | Vídeo Fachada parque em destaque; abaixo, imagem de entorno/ângulo 4 à esquerda e piscina/ângulo 2 à direita. |
| 06 | Carrossel Áreas comuns | 8 imagens: piscina → lazer geral → playground → fitness → funcional → coworking → salão 1 → salão 2. |
| 07 | Tour 360° | Tour incorporado em iframe responsivo 16:9, logo após Áreas comuns e antes do Corpo 3; link para abrir em nova aba como alternativa. |
| 08 | Corpo 3 | Vídeo Living Detalhe em destaque; abaixo, uma única imagem ampla da sacada 3003. |
| 09 | Carrossel Apartamentos/unidades | 2 imagens: living → sacada 1704 (arquivo versão 02, sem “vista 2” no rótulo). |
| 10 | Carrossel Animações | 9 vídeos: topo → entorno → fachada de baixo → observador → esquina → mall → piscina ângulo 2 → piscina geral → living geral. |
| 11 | Carrossel Plantas humanizadas | 9 plantas: 42 → 50 → 55 → 60 → 65 → 70 → 75 → 80 → 85. Trilho único, sem categorias Lazer/Unidades. |
| 12 | Filme conceito | Filme conceito V2 completo, com áudio e controles; duração aproximada de 2min56s. |
| 13 | Próximo projeto | Card no padrão existente; sequência editorial automática, levando a Vieiras na ordem atual. |

## Apresentação e critérios editoriais

- Corpos 1 e 2: vídeo largo seguido de duas imagens lado a lado no desktop. No mobile, vídeo e imagens empilhados na mesma ordem.
- Corpo 3: vídeo largo seguido de uma imagem larga, com altura automática e `contain` para preservar a proporção original. O `sizes` indica a largura completa para carregar a resolução adequada.
- Corpos sem títulos visíveis, seguindo os blocos editoriais existentes. Carrosséis com os títulos Fachadas, Áreas comuns, Apartamentos, Animações 3D e Plantas humanizadas.
- Carrosséis no padrão de trilho horizontal, arraste, setas e ampliação já usado no site. Plantas completas, com proporção preservada e sem recortes.
- O vídeo Fachada parque é uma cena de arquitetura/entorno. Como não há vídeo de piscina na pasta do corpo, ele faz a transição para as imagens de entorno e lazer no segundo bloco.
- As duas imagens 34 da sacada 1704 são próximas visualmente, mas os hashes e os pixels são diferentes. Conforme a revisão solicitada, apenas o arquivo versão 02 aparece no carrossel, com o rótulo “Sacada — unidade 1704”. O outro arquivo permanece preservado no acervo.
- A numeração 01–13 deste escopo representa a ordem completa da página. O menu de capítulos pode omitir Hero, blocos do corpo e Próximo projeto, seguindo os cases atuais.

## Organização e nomenclatura

Código editorial `RDO_VAL`; slug `rdo-val`; URL prevista `/projetos/rdo-val`, seguindo o padrão `ARS_VIE` → `ars-vie` e `AYA_KAR` → `aya-kar`.

Originais copiados para `C:\improov-media-masters\projetos\rdo-val`, nas subpastas `imagens`, `animacoes`, `plantas` e `filmes`, com hashes registrados em `master-index.json`. Todas as plantas ficam diretamente em `plantas`, sem Lazer/Unidades. Corpo e carrosséis são definidos pelo cadastro editorial da página, sem necessidade de subpastas master próprias.

Nomes em minúsculas, ASCII e hífens, com prefixos `imagem-`, `animacao-`, `planta-` e `filme-`. O mapa completo abaixo registra o nome proposto para cada arquivo. Os rótulos das plantas seguem a descrição dos arquivos recebidos, preservando tipo, torre, multiplicadores e indicação de variação, em um único carrossel. A versão 02 da sacada conserva um sufixo próprio.

Derivados públicos seguem o pipeline compartilhado em `assets/media/rdo-val/v1/`. Originais recebidos e masters permanecem intactos; não serão substituídos por versões otimizadas ou orientadas.

## Conversor de plantas

O script `deploy/rotate-floorplans.ps1` chama `deploy/rotate-floorplans.py`, com pasta de entrada e pasta de saída separadas, preservando os originais e recusando sobrescritas. As oito plantas das unidades foram convertidas; a planta 42 permanece como recebida. As cópias preparadas estão em `_prepared/plantas`, com sufixo `-horizontal`, sem divisão por categoria.

Exemplo para converter novas plantas:

```powershell
powershell -ExecutionPolicy Bypass -File .\deploy\rotate-floorplans.ps1 -InputDirectory 'C:\caminho\plantas-verticais' -OutputDirectory 'C:\caminho\plantas-horizontais'
```

Aplicar **270° no sentido horário**, equivalente a 90° no sentido anti-horário, sobre os pixels verticais da origem. Todas as oito fontes têm altura de 7000 px; as cópias horizontais terão largura de 7000 px.

A planta 50 já tem **EXIF Orientation=8**, que representa a mesma rotação. O conversor deve materializar a orientação apenas uma vez e normalizar/remover essa tag. Não aplicar auto-orientação seguida de outra rotação de 270°. As outras sete plantas têm EXIF Orientation=1 e precisam da rotação explícita. As cópias orientadas serão entradas controladas para os derivados públicos, com correspondência registrada e verificação visual antes de gerar o pacote final.

## Implementação

O componente compartilhado `editorialBlock` em `partials/case-detail.php` agora aceita uma ou duas imagens. No Corpo 3, a imagem ocupa a largura completa; os outros cases continuam com a composição existente de duas imagens.

Cadastro e composição seguem `data/projects.json` e `data/cases.json`. Imagens e vídeos foram gerados pelos conversores e manifestos compartilhados descritos em `docs/CASE_ONBOARDING.md`. Foram gerados 384 arquivos de imagens responsivas (31 imagens editoriais e uma capa extraída do Hero, cada uma com 12 variantes), 14 vídeos H.264 e 14 posters. O filme conserva seu áudio. O sitemap inclui PT, EN e ES. A publicação em produção não foi executada.

## Incorporação do tour 360°

O endereço enviado foi incluído como iframe seguro e responsivo em `https://tour.meupasseiovirtual.com/view/79vPI6jHjNd`. O título da seção é “Tour 360°”; a altura do quadro é limitada a 70% da viewport, seguindo os carrosséis. O link de fallback abre o mesmo passeio em uma nova aba. O domínio do provedor não respondeu a partir deste ambiente, então a política de incorporação do lado do provedor ainda precisa ser confirmada quando o site estiver acessível: se o iframe for bloqueado, o fallback continua disponível.

## Verificação da implementação

- Conferidos 412 arquivos públicos existentes: 384 variantes de imagens, 14 MP4s e 14 posters. O manifesto de vídeo terminou com zero erros.
- Os 45 arquivos recebidos e suas cópias master tiveram SHA-256 conferido novamente depois da geração de mídia. Todos permaneceram iguais aos hashes registrados na importação.
- Os registros dos cases existentes nos seis cadastros/manifestos foram comparados com a versão anterior: nenhum conteúdo anterior foi alterado.
- Navegador Chrome, em 1440 × 1000, 834 × 1112 e 390 × 844: ordem das seções, corpos com 1+2 / 1+2 / 1+1 materiais, carrosséis, ausência de overflow horizontal, reprodução e pausa do Hero, navegação por setas/teclado, ampliação e fechamento de plantas e reprodução do filme conferidos. Capturas foram inspecionadas visualmente. A revisão deixou os carrosséis com 6/8/2/9/9 itens.
- PT, EN e ES, link na listagem, seis cards da Home e composição editorial anterior de Vieiras conferidos. Nenhum erro de JavaScript ou carregamento de mídia foi encontrado no Valence.
- Sintaxe PHP, JavaScript e PowerShell e `git diff --check` passaram. O conversor passou pelos testes de rotação, EXIF=8, preservação de hashes, caminhos com espaços e recusa de sobrescrita.
- O PHP CS Fixer já aponta formatação no arquivo original de `partials/case-detail.php`; não foi aplicada uma reformatação geral fora do escopo.
- Pendência anterior observada ao abrir a listagem: falta `assets/media/hsa-mon/v1/posters/animacoes/hsa-mon-tracking-0038-lazer-ai-slow-down-poster.webp`. A referência já existia antes desta inclusão e não foi alterada. A asserção global de ausência de erros HTTP do teste da listagem acusa esse 404; os testes do Valence passaram.

## Mapa completo dos materiais

Origens relativas à pasta recebida; destinos relativos à pasta master proposta `rdo-val`. A ordem das tabelas determina a sequência dentro de cada seção. Dimensões das plantas abaixo correspondem aos pixels armazenados no original, antes de materializar a rotação.

### 01 — Hero

| Ordem | Arquivo recebido | Destino master proposto | Dimensões / duração |
| --- | --- | --- | --- |
| 01 | `Capa Thumbnail animado\RDO_VAL_tracking_0019_V2.mp4` | `animacoes/animacao-rdo-val-tracking-0019-v2.mp4` | 3840 × 2160; 52.17 s |

### 03 — Corpo 1

| Ordem | Arquivo recebido | Destino master proposto | Dimensões / duração |
| --- | --- | --- | --- |
| 01 | `Corpo da página\1.RDO_VAL Fachada Ampla_.mp4` | `animacoes/animacao-01-rdo-val-fachada-ampla.mp4` | 3840 × 2160; 15.00 s |
| 02 | `Corpo da página\5.RDO_VAL_Fachada_no_angulo_do_observador_da_rua_1_EF.jpg` | `imagens/imagem-05-rdo-val-fachada-no-angulo-do-observador-da-rua-1-ef.jpg` | 5000 × 4004 |
| 03 | `Corpo da página\7.RDO_VAL_Embasamento_mostrando_mall_e_calcadas_1_da_Rua_A_EF.jpg` | `imagens/imagem-07-rdo-val-embasamento-mostrando-mall-e-calcadas-1-da-rua-a-ef.jpg` | 5000 × 2812 |

### 04 — Carrossel Fachadas

| Ordem | Arquivo recebido | Destino master proposto | Dimensões / duração |
| --- | --- | --- | --- |
| 01 | `Carrossel Imagens 3D da fachada\1.RDO_VAL_Fotomontagem_aerea_com_insercao_do_empreendimento_em_terreno_real_angulo_1_EF.jpg` | `imagens/imagem-01-rdo-val-fotomontagem-aerea-com-insercao-do-empreendimento-em-terreno-real-angulo-1-ef.jpg` | 4456 × 5000 |
| 02 | `Carrossel Imagens 3D da fachada\2.RDO_VAL_Fotomontagem_aerea_com_insercao_do_empreendimento_em_terreno_real_angulo_2_EF.jpg` | `imagens/imagem-02-rdo-val-fotomontagem-aerea-com-insercao-do-empreendimento-em-terreno-real-angulo-2-ef.jpg` | 5000 × 2109 |
| 03 | `Carrossel Imagens 3D da fachada\3.RDO_VAL_Fotomontagem_aerea_com_insercao_do_empreendimento_em_terreno_real_angulo_3_-_Com_foco_na_area_de_lazer_1_EF_6_1.jpg` | `imagens/imagem-03-rdo-val-fotomontagem-aerea-com-insercao-do-empreendimento-em-terreno-real-angulo-3-com-foco-na-area-de-lazer-1-ef-6-1.jpg` | 5000 × 5000 |
| 04 | `Carrossel Imagens 3D da fachada\6.RDO_VAL_Fachada_no_angulo_do_observador_da_rua_2_EF.jpg` | `imagens/imagem-06-rdo-val-fachada-no-angulo-do-observador-da-rua-2-ef.jpg` | 5000 × 5000 |
| 05 | `Carrossel Imagens 3D da fachada\8.RDO_VAL_Embasamento_mostrando_mall_e_calcadas_2_mostrando_a_Avenida_I_com_a_Avenida_Ver.Pedro_Elias_EF.jpg` | `imagens/imagem-08-rdo-val-embasamento-mostrando-mall-e-calcadas-2-mostrando-a-avenida-i-com-a-avenida-ver-pedro-elias-ef.jpg` | 5000 × 2812 |
| 06 | `Carrossel Imagens 3D da fachada\9.RDO_VAL_Corredor_do_mall_EF.jpg` | `imagens/imagem-09-rdo-val-corredor-do-mall-ef.jpg` | 5000 × 3798 |

### 05 — Corpo 2

| Ordem | Arquivo recebido | Destino master proposto | Dimensões / duração |
| --- | --- | --- | --- |
| 01 | `Corpo da página\3.RDO_VAL Fachada parque.mp4` | `animacoes/animacao-03-rdo-val-fachada-parque.mp4` | 3840 × 2160; 10.00 s |
| 02 | `Corpo da página\4.RDO_VAL_Fotomontagem_aerea_com_insercao_do_empreendimento_em_terreno_real_angulo_4_EF.jpg` | `imagens/imagem-04-rdo-val-fotomontagem-aerea-com-insercao-do-empreendimento-em-terreno-real-angulo-4-ef.jpg` | 5000 × 2812 |
| 03 | `Corpo da página\26.RDO_VAL_Piscina_externa_angulo_2_-_com_vista_real_fotografica_EF.jpg` | `imagens/imagem-26-rdo-val-piscina-externa-angulo-2-com-vista-real-fotografica-ef.jpg` | 5000 × 3798 |

### 06 — Carrossel Áreas comuns

| Ordem | Arquivo recebido | Destino master proposto | Dimensões / duração |
| --- | --- | --- | --- |
| 01 | `Carrossel Imagens 3D das áreas comuns\25.RDO_VAL_Piscina_externa_angulo_1_-_com_vista_real_fotografica_EF.jpg` | `imagens/imagem-25-rdo-val-piscina-externa-angulo-1-com-vista-real-fotografica-ef.jpg` | 5000 × 2813 |
| 02 | `Carrossel Imagens 3D das áreas comuns\30.RDO_VAL_Ambiente_externo_do_pavimento_de_lazer_a_definir_2_-_com_vista_real_fotografica_EF.jpg` | `imagens/imagem-30-rdo-val-ambiente-externo-do-pavimento-de-lazer-a-definir-2-com-vista-real-fotografica-ef.jpg` | 5000 × 3799 |
| 03 | `Carrossel Imagens 3D das áreas comuns\24.RDO_VAL_Playground_externo_(Antigo_salao_de_festas_2)_EF.jpg` | `imagens/imagem-24-rdo-val-playground-externo-antigo-salao-de-festas-2-ef.jpg` | 5000 × 3798 |
| 04 | `Carrossel Imagens 3D das áreas comuns\14.RDO_VAL_Fitness_EF.jpg` | `imagens/imagem-14-rdo-val-fitness-ef.jpg` | 5000 × 2854 |
| 05 | `Carrossel Imagens 3D das áreas comuns\15.RDO_VAL_Funcional_EF.jpg` | `imagens/imagem-15-rdo-val-funcional-ef.jpg` | 5000 × 3030 |
| 06 | `Carrossel Imagens 3D das áreas comuns\16.RDO_VAL_Coworking_EF.jpg` | `imagens/imagem-16-rdo-val-coworking-ef.jpg` | 5000 × 2812 |
| 07 | `Carrossel Imagens 3D das áreas comuns\20.RDO_VAL_Salao_de_festas_1_EF.jpg` | `imagens/imagem-20-rdo-val-salao-de-festas-1-ef.jpg` | 5000 × 2172 |
| 08 | `Carrossel Imagens 3D das áreas comuns\21.RDO_VAL_Salao_de_festas_2_EF.jpg` | `imagens/imagem-21-rdo-val-salao-de-festas-2-ef.jpg` | 5000 × 2172 |

### 07 — Corpo 3

| Ordem | Arquivo recebido | Destino master proposto | Dimensões / duração |
| --- | --- | --- | --- |
| 01 | `Corpo da página\37_RDO_VAL Living com vista real fotografica_Detalhe.mp4` | `animacoes/animacao-37-rdo-val-living-com-vista-real-fotografica-detalhe.mp4` | 3840 × 2160; 7.00 s |
| 02 | `Corpo da página\36.RDO_VAL_Sacada_com_vista_real_fotografica_-_Torre_1_-_unid.3003_EF.jpg` | `imagens/imagem-36-rdo-val-sacada-com-vista-real-fotografica-torre-1-unid-3003-ef.jpg` | 5000 × 2778 |

### 08 — Carrossel Apartamentos/unidades

| Ordem | Arquivo recebido | Destino master proposto | Dimensões / duração |
| --- | --- | --- | --- |
| 01 | `Carrossel Imagens 3D das unidades\37.RDO_VAL_Living_com_vista_real_fotografica_-_Torre_2_-_Final_12_EF.jpg` | `imagens/imagem-37-rdo-val-living-com-vista-real-fotografica-torre-2-final-12-ef.jpg` | 5000 × 2777 |
| 02 | `Carrossel Imagens 3D das unidades\34.RDO_VAL Sacada com vista real fotografica - Torre 1 - unid.1704_EF - 02.jpg` | `imagens/imagem-34-rdo-val-sacada-com-vista-real-fotografica-torre-1-unid-1704-ef-02.jpg` | 5000 × 3000 |

Mantido apenas no acervo, por solicitação na revisão: `Carrossel Imagens 3D das unidades\34.RDO_VAL_Sacada_com_vista_real_fotografica_-_Torre_1_-_unid.1704_EF.jpg` → `imagens/imagem-34-rdo-val-sacada-com-vista-real-fotografica-torre-1-unid-1704-ef.jpg` (5000 × 3000).

### 09 — Carrossel Animações

| Ordem | Arquivo recebido | Destino master proposto | Dimensões / duração |
| --- | --- | --- | --- |
| 01 | `Carrossel Animações 3D\2.RDO_VAL_Fachada_Topo.mp4` | `animacoes/animacao-02-rdo-val-fachada-topo.mp4` | 3840 × 2160; 10.00 s |
| 02 | `Carrossel Animações 3D\4.RDO_VAL_Fotomontagem_aerea_com_insercao_do_empreendimento_em_terreno_real_angulo_4.mp4` | `animacoes/animacao-04-rdo-val-fotomontagem-aerea-com-insercao-do-empreendimento-em-terreno-real-angulo-4.mp4` | 3840 × 2160; 10.00 s |
| 03 | `Carrossel Animações 3D\5.RDO_VAL Fachada Baixo.mp4` | `animacoes/animacao-05-rdo-val-fachada-baixo.mp4` | 3840 × 2160; 10.00 s |
| 04 | `Carrossel Animações 3D\6.RDO_VAL Fachada observador da rua 2.mp4` | `animacoes/animacao-06-rdo-val-fachada-observador-da-rua-2.mp4` | 3840 × 2160; 9.57 s |
| 05 | `Carrossel Animações 3D\7.RDO_VAL Embasamento Esquina.mp4` | `animacoes/animacao-07-rdo-val-embasamento-esquina.mp4` | 3840 × 2160; 9.93 s |
| 06 | `Carrossel Animações 3D\9.RDO_VAL Corredor do mall Geral.mp4` | `animacoes/animacao-09-rdo-val-corredor-do-mall-geral.mp4` | 3840 × 2160; 8.00 s |
| 07 | `Carrossel Animações 3D\26.RDO_VAL Piscina externa angulo 2.mp4` | `animacoes/animacao-26-rdo-val-piscina-externa-angulo-2.mp4` | 3840 × 2160; 10.00 s |
| 08 | `Carrossel Animações 3D\30.RDO_VAL PISCINA GERAL.mp4` | `animacoes/animacao-30-rdo-val-piscina-geral.mp4` | 3840 × 2160; 10.00 s |
| 09 | `Carrossel Animações 3D\37_RDO_VAL Living com vista real fotografica_Geral.mp4` | `animacoes/animacao-37-rdo-val-living-com-vista-real-fotografica-geral.mp4` | 3840 × 2160; 7.97 s |

### 10 — Carrossel Plantas humanizadas

Rótulos na ordem dos arquivos: Lazer Pilotis; Tipo 3 (x11); Tipo 02 diferenciado — Variação; Tipo 02 x 7 — Variação; Tipo 03 diferenciado — Variação; Tipo 03 x 6 — Variação; Tipo 1 diferenciado — Torre 02; Tipo 01 (x11); Tipo 02 (x12).

| Ordem | Arquivo recebido | Destino master proposto | Dimensões / duração |
| --- | --- | --- | --- |
| 01 | `Carrossel Plantas Humanizadas\Lazer\42.RDO_VAL_Planta_humanizada_do_pavimento_Lazer_Pilotis_R02_1_1.jpg` | `plantas/planta-01-rdo-val.jpg` | 5000 × 3451 |
| 02 | `Carrossel Plantas Humanizadas\Unidades\50. RDO_VAL_PH_Tipo_3_(x11)_EF.jpg` | `plantas/planta-02-rdo-val.jpg` | 4661 × 7000 |
| 03 | `Carrossel Plantas Humanizadas\Unidades\55.RDO_VAL Planta humanizada do pavimento Tipo 02 diferenciado - Variacao_EF.jpg` | `plantas/planta-03-rdo-val.jpg` | 4964 × 7000 |
| 04 | `Carrossel Plantas Humanizadas\Unidades\60.RDO_VAL Planta humanizada do pavimento Tipo 02 x 7 - Variacao_EF.jpg` | `plantas/planta-04-rdo-val.jpg` | 5173 × 7000 |
| 05 | `Carrossel Plantas Humanizadas\Unidades\65.RDO_VAL Planta humanizada do pavimento Tipo 03 diferenciado - Variacao_EF.jpg` | `plantas/planta-05-rdo-val.jpg` | 5457 × 7000 |
| 06 | `Carrossel Plantas Humanizadas\Unidades\70.RDO_VAL Planta humanizada do pavimento Tipo 03 x 6 - Variacao_EF.jpg` | `plantas/planta-06-rdo-val.jpg` | 5457 × 7000 |
| 07 | `Carrossel Plantas Humanizadas\Unidades\75. RDO_VAL_PH_Tipo_1_Diferenciado_Torre_02_EF.jpg` | `plantas/planta-07-rdo-val.jpg` | 3774 × 7000 |
| 08 | `Carrossel Plantas Humanizadas\Unidades\80.RDO_VAL Planta Humanizada do pavimento TIPO 01 (X11)_EF.jpg` | `plantas/planta-08-rdo-val.jpg` | 4894 × 7000 |
| 09 | `Carrossel Plantas Humanizadas\Unidades\85.RDO_VAL Planta Humanizada do pavimento TIPO 02 (X12)_EF.jpg` | `plantas/planta-09-rdo-val.jpg` | 4476 × 7000 |

### 11 — Filme conceito

| Ordem | Arquivo recebido | Destino master proposto | Dimensões / duração |
| --- | --- | --- | --- |
| 01 | `Filme conceito\2026 RDO_VAL CONCEPT_V2.mp4` | `filmes/filme-rdo-val-conceito-v2.mp4` | 1920 × 1080; 175.96 s |

Cobertura atual: **45 arquivos preservados, 44 exibidos na página e uma imagem mantida somente no acervo**, sem colisões de nomes. A proporção do Corpo 3 e o carrossel com dois itens foram novamente conferidos em larguras de 390, 834, 1440 e 1920 px; os rótulos foram conferidos em PT, EN e ES.
