# Revisão comercial do site — 22/10/2026

A parte 1 já aprovada foi preservada. As partes seguintes foram implementadas no projeto local, incluindo as decisões posteriores do usuário.

## Home

- Cards identificam o cliente e os serviços produzidos, a partir dos dados de cada case.
- Convite para avaliação gratuita após os projetos, com WhatsApp direto.
- Quatro etapas de trabalho: briefing, direção criativa, prévia/aprovação e entrega.
- Prazos confirmados pelo usuário: imagens de 15 a 30 dias úteis; filmes de 2 a 4 semanas. Animações 3D: de 15 a 20 dias úteis. Documentários foram retirados dos prazos por solicitação posterior do usuário. Revisões são definidas na proposta, pois não foi informada uma quantidade padrão.
- Dois filmes existentes (AYA/Kariió e ARS Kammer/Vieiras) em destaque na Home, sem autoplay e sem carregar o vídeo antes da interação.
- Seis perguntas frequentes sobre prazo, orçamento, revisões, arquivos, atendimento fora de SC e contratação por serviço.
- Chamada final com WhatsApp como ação principal e formulário como alternativa.

## Contato e confirmação

- WhatsApp em botão sólido e primeira ação da página.
- Seis campos: nome, WhatsApp, empresa, cargo, cidade do empreendimento e previsão de lançamento. Todos são obrigatórios; consentimento e campos de atribuição continuam presentes.
- Sem campo de e-mail, mensagem longa ou upload. E-mail opcional continua aceito pela API, para compatibilidade com integrações.
- Telefone, prazo, limites de texto, idioma, consentimento e honeypot são conferidos no servidor.
- Cargo e previsão são registrados no campo de detalhes (`mensagem`) do esquema existente, com rótulos explícitos. Nenhuma migração de banco é necessária. A fila de e-mail continua recebendo todos os dados.
- O envio comercial sem e-mail não define Reply-To, evitando falha no PHPMailer. Candidaturas continuam usando o e-mail informado.
- Mensagem de retorno: até 24 horas, normalmente no mesmo dia; WhatsApp até 1 hora no horário comercial, conforme revisão fornecida.
- Após sucesso confirmado pela API, `contact_form_success` é emitido uma vez e o usuário segue para `/obrigado`, também em EN e ES. A página é `noindex` e oferece WhatsApp.
- Não existe agenda nem referência a atendimento por uma pessoa específica, por decisão do usuário.
- Mensagem do WhatsApp padronizada: “Olá! Vou lançar um empreendimento e quero avaliar o material de vendas com a Improov.” As versões EN e ES também são traduzidas.

## Cases e imagens

- Os resumos de desafio, entrega e uso foram retirados dos headers dos nove cases por solicitação posterior do usuário, restaurando a abertura anterior.
- Fichas técnicas mantidas. Os filmes existentes de depoimento de Kariió, Vieiras e AYA Casas aparecem também em destaque junto à ficha técnica, com o título e sem o nome da construtora e com o mesmo player de tela cheia dos demais filmes.
- Todos os cases têm convite para avaliação antes do próximo projeto.
- O campo opcional `result` em `data/cases.json` permite incluir um resultado comercial confirmado. Não foram inventados números de vendas ou falas de clientes.
- Pôsteres de vídeos fora da tela passam a carregar apenas quando próximos da área visível, incluindo cards e depoimentos. A prévia principal continua imediata.
- Revelação de conteúdo passa a acontecer antes da entrada na tela, sem depender de 10% da altura de blocos grandes. Conteúdo permanece visível quando JavaScript está desativado.
- 50 variantes AVIF pesadas do Kariió foram comprimidas mantendo dimensões: 16.609.226 para 7.578.597 bytes (54,4% de redução nesses arquivos). JPEGs e originais não foram alterados. Backups estão em `cache/media-backups/`, ignorado pelo Git.
- O gerador passa a usar AVIF com qualidade 65. `deploy/optimize-case-images.py --project aya-kar --apply` aplica a política a derivados existentes; sem `--apply`, mostra o plano.

## Validação e publicação

- Sintaxe PHP e JavaScript, JSON e diff conferidos.
- Home, contato, confirmação e Kariió conferidos em 320/390/1440 px e nos três idiomas; sem rolagem horizontal ou chaves de tradução visíveis.
- Fluxo de sucesso e evento único testados com resposta simulada da API. Nenhum contato de teste foi gravado e nenhum e-mail real foi enviado.
- API real rejeitou ausência de campos obrigatórios, telefone inválido, prazo inválido, ausência de consentimento e honeypot preenchido, antes de acessar o banco.
- Nove cases retornaram HTTP 200 com convite. Exibição sem JavaScript conferida.
- Na simulação local com rede de 1,6 Mbps, latência de 100 ms, DPR 2 e CPU 4x, o tempo até DOMContentLoaded caiu de 23.212 ms para 8.121 ms após adiar os pôsteres.
- A medição de celular é uma simulação local, com vídeos bloqueados para isolar imagens; não corresponde a um teste de produção ou a uma nota do PageSpeed.
- Publicação em produção não executada. Código e as 50 imagens AVIF atualizadas devem acompanhar a publicação; `assets/media/` pertence ao pacote de mídia e não é versionado no Git. Atualizar o processo do worker ao publicar o novo serviço de e-mail.
- As configurações administrativas pendentes de GA4 continuam documentadas em `docs/analytics-events.md`; não são modificadas pelo código do site.
