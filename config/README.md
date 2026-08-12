# Configuração do site

## Formulário Trabalhe Conosco

1. Crie uma conta Formspree com `contato@improov.com.br` e verifique o e-mail.
2. Crie um formulário para receber as candidaturas.
3. Copie o endpoint gerado (no formato `https://formspree.io/f/xxxxxxxx`).
4. Cole-o em `config/site-config.js`, no valor de `formspreeEndpoint`.

Enquanto esse endpoint estiver vazio, o site mantém a validação do formulário e orienta a pessoa a enviar o material por e-mail. Nenhuma candidatura é descartada silenciosamente.

## Idiomas

Os textos fixos ficam centralizados em `script.js`. Os textos específicos de cada projeto aceitam traduções por slug no mesmo arquivo, com o conteúdo em português preservado como padrão.
