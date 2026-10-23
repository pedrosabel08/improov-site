# Instrumentação de Analytics da Improov

## Propriedades e integrações

- GA4 próprio da Improov: Measurement ID `G-B5NQ8F79YD`.
- Google Tag associado informado no painel: `GT-NGP39M9P` (não é declarado diretamente no código do site).
- GTM externo da Publya: `GTM-55WF2HTD`. O projeto não deve alterar, remover, substituir ou migrar esse container.
- A camada própria chama `gtag('event', ...)` através de `assets/js/analytics.js` e não depende do GTM da Publya para enviar os novos eventos.
- Os eventos `generate_lead` e `RD Popup e WhatsApp` já aparecem no GA4, mas sua origem não foi identificada nesta implementação. O evento próprio de sucesso do formulário comercial é `contact_form_success` e não reutiliza `generate_lead`.

## Eventos próprios implementados

| Evento                       | Quando dispara                                                                                                                          | Parâmetros                                                                        | Prioridade  | Evento principal?                                            |
| ---------------------------- | --------------------------------------------------------------------------------------------------------------------------------------- | --------------------------------------------------------------------------------- | ----------- | ------------------------------------------------------------ |
| `contact_form_success`       | Após `improov:form-success` do formulário `contact`, emitido somente após resposta HTTP bem-sucedida e `payload.success`                | `page_path`, `form_type=contact`, `form_location=contact_section`                 | Alta        | Sim — configurar no GA4 (pendente)                                        |
| `whatsapp_click`             | Clique em link `wa.me` ou elemento explicitamente identificado como ação de WhatsApp                                                    | `page_path`, `button_location`, `cta_name`                                        | Alta        | Sim — configurar no GA4 (pendente); mede intenção de contato |
| `email_click`                | Clique em link `mailto:`                                                                                                                | `page_path`, `button_location`                                                    | Média       | Não inicialmente                                             |
| `cta_click`                  | Clique nos CTAs nomeados de projetos, contato e candidatura                                                                             | `page_path`, `cta_name`, `button_location`, `destination`                         | Média       | Não                                                          |
| `project_select`             | Clique em card da Home, card do catálogo ou projeto seguinte                                                                            | `page_path`, `project_slug`, `project_name`, `button_location`                    | Média       | Não                                                          |
| `case_content_interaction`   | Abertura/ampliação de imagem ou seleção/ampliação de planta em um case                                                                  | `page_path`, `project_slug`, `content_type`, `content_name`, `interaction_action` | Média       | Não                                                          |
| `video_start`                | Início de vídeo controlado pelo usuário nos players de filme de projeto/case; também ao ativar explicitamente uma animação still-motion | `page_path`, `project_slug`, `media_name`, `media_location`                       | Média       | Não                                                          |
| `careers_application_submit` | Após `improov:form-success` do formulário `careers`, somente após sucesso confirmado pelo backend                                       | `page_path`, `form_type=careers`                                                  | Média       | Não para conversão comercial                                 |
| `social_click`               | Clique em Instagram, LinkedIn ou YouTube                                                                                                | `page_path`, `social_network`, `button_location`                                  | Baixa/Média | Não                                                          |

### Localizações e identificadores

As localizações são identificadores estáveis: `floating`, `hero`, `contact_section`, `footer`, `portfolio`, `content`, `home_projects`, `project_catalog` e `case_next`. Os nomes de CTA são identificadores semânticos (`view_projects`, `view_all_projects`, `contact_us`, `apply_for_careers`) e não dependem do texto traduzido.

`project_name` e `content_name` usam rótulos semânticos visíveis em vez de nomes técnicos de arquivos. O slug identifica o projeto de forma estável. `destination` em CTAs internos contém somente caminho e fragmento, sem query string. Os eventos não leem valores de campos de formulário, URLs completas de WhatsApp, endereços de e-mail, texto de mensagem ou nomes de anexos.

## Compatibilidade com o dataLayer existente

Os eventos legados `improov_whatsapp_click`, `improov_contact_click`, `improov_case_click` e `improov_form_submit` continuam sendo enviados ao `dataLayer` nas mesmas classes de interação que já eram cobertas. O repositório não revela se o GTM externo da Publya utiliza esses eventos, portanto foram preservados para evitar interromper uma dependência externa não verificável.

Os eventos novos são enviados uma vez diretamente ao GA4 pela função central `trackEvent`. O helper não envia a nova taxonomia ao `dataLayer`. Ainda assim, a configuração externa do GTM precisa ser revisada pela Publya para confirmar que os eventos legados não são encaminhados à mesma propriedade e para evitar que alguma tag externa gere hits semânticos adicionais. O código deste projeto não altera o container.

## Eventos automáticos do GA4: não duplicar

Conforme confirmado no painel da propriedade, a Medição otimizada está ativa. Evitar criar eventos próprios para:

- `page_view`, `scroll`, `click` externo, `form_start`, downloads genéricos e eventos básicos de sessão/engajamento.
- Pesquisa no site, enquanto não houver uma busca implementada.
- Engajamento de vídeo automático: verificar compatibilidade por tipo de player. Os eventos `video_start` próprios aqui são apenas para os elementos MP4 locais indicados acima e ações deliberadas; não representam autoplay.

O evento automático `click` pode ocorrer junto de `whatsapp_click` em links externos. O primeiro descreve a interação externa genérica; o segundo, a intenção via WhatsApp. Não somar ambos como duas conversões. `form_start` permanece a cargo da configuração existente. Eventos automáticos de envio não substituem os dois eventos próprios de sucesso, que só ocorrem após resposta positiva do backend.

Não foi criado `project_view`: o `page_view` da URL individual mede visualizações do projeto; `project_select` mede a decisão de abrir o projeto a partir de um card/link.

## Validação administrativa pendente

Configuração solicitada em 22/10/2026, ainda pendente de aplicação no painel do GA4. Alterar os arquivos do site não modifica regras de criação nem marcações de eventos-chave da propriedade.

1. Excluir a regra que cria `generate_lead` a partir de acessos à página `/contato`.
2. Desmarcar `page_view`, `user_engagement` e `form_start` como eventos-chave, preservando sua coleta normal.
3. Marcar `contact_form_success` e `whatsapp_click` como eventos-chave. O segundo mede intenção de contato e não comprova conversa iniciada.
4. Confirmar com a Publya se o GTM `GTM-55WF2HTD` consome os eventos `improov_*` preservados ou envia eventos para `G-B5NQ8F79YD`.
5. Usar DebugView e Tempo real do GA4 para conferir nomes e parâmetros após publicar/testar no ambiente conectado à propriedade.

## Revisão comercial de outubro de 2026

As novas chamadas para WhatsApp usam o mesmo evento `whatsapp_click`, com localizações `after_projects`, `closing`, `case_closing` e `thank_you`. Os links de depoimentos usam `project_select` com localização `testimonials`. A reprodução dos depoimentos da Home envia `video_start` com `media_location=home_testimonials`. O formulário comercial mantém o evento de sucesso após a confirmação da API e redireciona para `/obrigado`; a página não emite outro evento de conversão nem cria `generate_lead`.
