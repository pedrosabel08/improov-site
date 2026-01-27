# improov-site

Site institucional da Improov, desenvolvido com foco na apresentação de projetos (cases) e posicionamento da marca.

Este repositório contém o código-fonte do site, incluindo páginas institucionais e páginas dedicadas a projetos, com forte apelo visual e suporte a conteúdo multimídia.

---

## 🎯 Objetivo do Projeto

Criar um site institucional que:
- Apresente os principais projetos da Improov (cases)
- Reforce o posicionamento da empresa
- Sirva como vitrine visual e comercial
- Seja simples de manter e evoluir

---

## 🧩 Escopo Inicial

O site contempla inicialmente:

- Página inicial (Home)
- Página de listagem de projetos
- Página individual de projeto (Case)
- Footer com informações institucionais e contato
- Suporte a múltiplos idiomas
- Conteúdo gerenciado diretamente no código (sem CMS)

Funcionalidades fora deste escopo poderão ser avaliadas futuramente.

---

## 🗂 Estrutura de Conteúdo

Cada projeto (case) poderá conter:
- Imagem ou animação principal
- Nome do projeto
- Descrição resumida
- Informações adicionais (cliente, localização, data, etc.)
- Imagens avulsas
- Carrosséis de imagens ou animações
- Vídeos (filmes)

---

## 🔄 Atualização de Conteúdo

- O site não utiliza CMS
- Todo o conteúdo é versionado via código
- Atualizações exigem alteração no repositório e novo deploy

---

## 🛠 Tecnologias

As tecnologias utilizadas serão definidas conforme a evolução do projeto.

> Observação: a escolha da stack priorizará performance, qualidade visual e facilidade de manutenção.

---

## 📌 Organização do Projeto

Este repositório serve como:
- Fonte única da verdade do site
- Histórico de decisões técnicas
- Base para futuras evoluções

---

## 📁 Estrutura de Pastas (sugestão)

Estrutura inicial criada para manter **conteúdo** separado do **código** e facilitar evolução de stack (sem travar agora em Next/Astro/Vite/etc):

```
improov-site/
	content/
		cases/               # dados dos projetos (sem CMS)
		i18n/                # textos globais por idioma
	public/
		media/               # imagens/vídeos servidos como estáticos
	src/
		pages/               # páginas (home, lista de cases, detalhe)
		layouts/             # layouts/base de páginas
		components/          # componentes reutilizáveis
		features/            # módulos por domínio (ex: cases)
		styles/              # estilos globais/tokens
		i18n/                # infra de i18n (carregar/selecionar idioma)
		lib/                 # helpers e utilitários
		types/               # contratos/tipos
	docs/
		adr/                 # decisões técnicas (ADRs)
	scripts/               # scripts auxiliares (validação/otimização/etc)
	config/                # configs (dependendo da stack)
```

### Onde colocar os cases

- Conteúdo (metadados, blocos, galerias): `content/cases/<slug>/...`
- Mídia (imagens/vídeos): `public/media/cases/<slug>/...`

Os detalhes e exemplos de estrutura estão nos READMEs dentro das próprias pastas.

---

## 🚧 Status

Projeto em desenvolvimento.
