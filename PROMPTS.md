# Prompts utilizados

---
## Parte 1 – Planejamento e protótipo visual

### Vinicius

**Prompt 1** – Gere um guia de estilo completo, numa prancha só com 6 blocos:
- Paleta do tema claro: 12 cores, cada uma com nome, hex e onde é usada.
- Paleta do tema escuro: 8 cores.
- Tabela de contraste (WCAG): razão de cada combinação de texto e fundo e se passa nos 4,5:1.
- Tipografia: a pilha de fontes do sistema e os 6 tamanhos usados (título, nomes, mensagem, prévia, horário).
- Componentes: botões primário, hover, secundário, desabilitado e perigo; campo de busca e campos de
  formulário (normal, em foco e com erro); chips de filtro, badge de não lidas e toggle de tema; item de
  conversa normal e selecionado; balões no tema claro e no escuro; mensagem de erro (RN01) e aviso de sucesso.
- Logotipo, ícones e imagens: os 16 ícones do Bootstrap Icons com o nome de cada um e a fonte (licença MIT),
  os avatares padrão e a origem do QR code e do fundo do chat.

**Prompt 2** – Gere a definição do site atualizada, já com o fluxo do QR, as telas do celular e a RN04.

**Prompt 3** – Junte os wireframes que eu mandei, o guia de estilo e a definição em um PDF.

---

## Parte 2 – Front-end (HTML, CSS e JavaScript)

### Vinicius

**Prompt 4** – Gere uma estrutura de arquivos, sem nenhum código dentro, ideal para essas telas.

**Prompt 5** – Crie o css/style.css usado em todas as páginas: variáveis de cor e fonte do guia de estilo
(tema claro em :root e tema escuro em [data-tema="escuro"]), reset, :focus-visible, classes reutilizáveis
(botões, campos com erro, busca, chips, badge, avatar, interruptor, modal, toast, cartão, tabela zebrada
e rodapé) e breakpoints em 767px e 1024px.

**Prompt 6** – Crie o js/tema.js: ler a chave "tema" do localStorage, aplicar data-tema="escuro" no <html>,
função alternarTema() e ligar com addEventListener todo elemento com data-alternar-tema.

**Prompt 7** – Crie dados/conversas.json com 12 conversas fictícias e o js/conversas.js: carregar com fetch,
busca, filtros, abrir conversa, enviar mensagem, RN01 (fixar até 3), RN02 (apagar para todos até 15 min),
RN03 (contato bloqueado), avisos em toast sem alert e dados salvos no localStorage.

**Prompt 8** – Preencha admin/painel.html, css/admin-painel.css e js/admin-painel.js: menu lateral que vira ☰
no mobile, cartões de resumo, busca e tabela de contatos com Editar e Excluir em modal, validação sem alert.

**Prompt 9** – Revisar e corrigir telas do grupo: conectar.html virar a tela do celular sem QR code, trocar
imagens quebradas pelo ícone do WhatsApp, criar dados/contatos.json e fazer o tema das Configurações usar a
mesma chave do tema.js.

**Prompt 10** – Crie o README.md com descrição, integrantes, link do site, tecnologias, tabela de páginas,
funcionalidades, regras de negócio, estrutura de pastas e como executar.

### Nicolas

**Prompt 11** – Gere um HTML base para essas telas, e um CSS base também.

**Prompt 12** – Converta a questão dos números para serem preenchidos automaticamente na formatação com parênteses.

**Prompt 13** – Melhore a página de login, para ficar no modelo da versão que já existe, igual está feito aí na foto.

### Adicionados por Vinicius

**Prompt 14** – Ajude-me a colocar comentários nos trechos desses códigos que te enviei (login, cadastro, HTML e JS).

**Prompt 15** – Me ajude a criar um README completo para esse projeto.

**Prompt 16** – Revise esse projeto, se ele está com boas práticas e se existe alguma possível falha de segurança.

---

## Parte 3 – Back-end com PHP

### Vinicius

Nesta parte os pedidos foram feitos direto à ferramenta, que gerou e testou o código.

**Pedido 17** – Separar as tarefas da Parte 3 entre os três integrantes a partir do enunciado, em PDF.

**Pedido 18** – Criar a estrutura de pastas e arquivos do PHP (includes, api, logout, banco.sql).

**Pedido 19** – Ligar as Conversas ao banco com fetch em arquivos PHP que devolvem JSON, com as regras
RN01, RN02 e RN03 validadas no PHP.

**Pedido 20** – Fazer o QR code real (RN04): token gerado no PHP, validade de 60 segundos, uso único e
confirmação pelo celular.

---

## Parte 4 – Vídeo

**Pedido 21** – Montar o roteiro do vídeo dividindo as partes entre os integrantes.
