# Prompts utilizados

Registro dos prompts usados com ferramentas de IA durante o Trabalho III. Para cada prompt estão a ferramenta, a parte do trabalho, o texto completo e como o resultado foi usado. Todo o resultado gerado foi revisado, testado e adaptado pelo grupo antes de entrar no projeto.

## Parte 1 – Planejamento

### Prompt 1
- **Ferramenta:** Claude
- **Parte:** Parte 1 – Guia de estilo
- **Prompt:**

  > Gere um guia de estilo completo, numa prancha só com 6 blocos:
  > 1. Paleta do tema claro: 12 cores, cada uma com nome, hex e onde é usada.
  > 2. Paleta do tema escuro: 8 cores.
  > 3. Tabela de contraste (WCAG): razão de cada combinação de texto e fundo e se passa nos 4,5:1. Mostra que branco no verde passa.
  > 4. Tipografia: a pilha de fontes do sistema e os 6 tamanhos usados (título, nomes, mensagem, prévia, horário).
  > 5. Componentes: botões primário, hover, secundário, desabilitado e perigo; campo de busca e campos de formulário (normal, em foco e com erro); chips de filtro, badge de não lidas e toggle de tema; item de conversa normal e selecionado; balões no tema claro e no escuro; mensagem de erro (RN01) e aviso de sucesso.
  > 6. Logotipo, ícones e imagens: os 16 ícones do Bootstrap Icons com o nome de cada um e a fonte (licença MIT), os avatares padrão e a origem do QR code e do fundo do chat.

- **Como foi usado:** a prancha gerada virou o guia de estilo entregue na Parte 1 (`guia-de-estilo.png`). As cores e tamanhos definidos nela foram usados depois nas variáveis do `css/style.css`.

### Prompt 2
- **Ferramenta:** Claude
- **Parte:** Parte 1 – Definição do site
- **Prompt:**

  > Gere a definição do site atualizada, já com o fluxo do QR, as telas do celular e a RN04.

- **Como foi usado:** o texto serviu de base para a seção de definição do site e para a descrição das regras de negócio no documento da Parte 1. Revisamos os nomes das páginas e ajustamos o mapa do site antes de entregar.

### Prompt 3
- **Ferramenta:** Claude
- **Parte:** Parte 1 – Documento final
- **Prompt:**

  > Junte os wireframes que eu mandei, o guia de estilo e a definição em um PDF no modelo da SBC.

- **Como foi usado:** gerou o PDF da Parte 1 com os wireframes feitos pelo grupo, o guia de estilo e a definição. Conferimos a formatação e os nomes dos integrantes antes de enviar.

## Parte 2 – HTML, CSS e JavaScript

### Prompt 4
- **Ferramenta:** Claude
- **Parte:** Parte 2 – Estrutura do projeto
- **Prompt:**

  > Gere uma estrutura de arquivos, sem nenhum código dentro, ideal para essas telas.

- **Como foi usado:** usamos a estrutura de pastas sugerida (`css/`, `js/`, `img/`, `admin/`) para criar o repositório. Os arquivos foram criados vazios e cada integrante preencheu os seus.

### Prompt 5
- **Ferramenta:** Claude
- **Parte:** Parte 2 – Base das páginas
- **Prompt:**

  > Gere um HTML base para essas telas, e um CSS base também.

- **Como foi usado:** o HTML base serviu de ponto de partida para o cabeçalho, o rodapé educacional e o layout de duas colunas da tela de conversas. O CSS base foi reescrito com as variáveis do guia de estilo; mantivemos apenas a organização em seções.

### Prompt 6
- **Ferramenta:** ChatGPT
- **Parte:** Parte 2 – Cadastro e login (máscara de telefone)
- **Prompt:**

  > Converta a questão dos números para serem preenchidos automáticos na formatação com parênteses, tipo (49) 99999-0000.

- **Como foi usado:** a função de máscara em JavaScript foi usada nos campos de telefone do cadastro, do login e do painel do administrador, com o padrão ajustado para o formato brasileiro.

### Prompt 7
- **Ferramenta:** ChatGPT
- **Parte:** Parte 2 – Login
- **Prompt:**

  > Melhore a página de login, para ficar no modelo da versão que já existe, igual tá feito aí na foto.

- **Como foi usado:** aproveitamos as sugestões de espaçamento e o botão de mostrar/ocultar senha. A estrutura do formulário continuou a mesma.

### Prompt 8
- **Ferramenta:** ChatGPT
- **Parte:** Parte 2 – Login, cadastro, HTML e JS
- **Prompt:**

  > Ajude-me a colocar comentários nos trechos desses códigos que te enviei (login, cadastro, HTML e JS).

- **Como foi usado:** os comentários foram usados para entender e documentar os arquivos durante a Parte 2. Na Parte 3 a maior parte foi removida para deixar o código mais limpo.

### Prompt 9
- **Ferramenta:** Claude
- **Parte:** Parte 2 – README
- **Prompt:**

  > Me ajuda a criar um README completo para esse projeto.

- **Como foi usado:** o README gerado foi a base do `README.md`. Depois foi atualizado pelo grupo com as instruções do XAMPP, os usuários de teste, a tabela de responsáveis e o link do site publicado.

### Prompt 10
- **Ferramenta:** Claude
- **Parte:** Parte 2 – Revisão
- **Prompt:**

  > Revise esse projeto, se ele está em boas práticas e se existe alguma possível falha de segurança.

- **Como foi usado:** a revisão apontou o uso de `onclick` inline, variáveis de CSS não definidas e a falta de escape na saída. Corrigimos esses pontos: trocamos os `onclick` por `addEventListener`, criamos as variáveis que faltavam e passamos a usar a função `esc()` em todo texto vindo do banco.

## Parte 3 – PHP e MySQL

### Prompt 11
- **Ferramenta:** Claude
- **Parte:** Parte 3 – Divisão das tarefas
- **Prompt:**

  > Com base no enunciado da Parte 3, divide as tarefas entre os três integrantes e monta um PDF com a divisão, sem falar o que é fácil ou difícil, só com as partes de cada um.

- **Como foi usado:** o PDF de divisão foi enviado ao grupo e seguido: Nicolas ficou com cadastro, login e login do administrador; Jadson com contatos, perfil e configurações; Vinicius com banco de dados, conversas, QR code e painel do administrador.

### Prompt 12
- **Ferramenta:** Claude
- **Parte:** Parte 3 – Banco de dados
- **Prompt:**

  > Cria o banco.sql com as tabelas usuarios, admins, contatos, denuncias, conversas, mensagens e qr_codes, com chaves estrangeiras, e os dados de teste (contatos, conversas e mensagens fictícias, um usuário e um administrador com senha gerada pelo password_hash). Tem que funcionar no MariaDB do XAMPP.

- **Como foi usado:** o script gerado virou o `banco.sql`. Ajustamos depois as colunas `lida`, `arquivo` e `arquivo_tipo` na tabela `mensagens` e a coluna `navegador` em `qr_codes` conforme as funções foram sendo feitas.

### Prompt 13
- **Ferramenta:** Claude
- **Parte:** Parte 3 – Conversas e regras de negócio
- **Prompt:**

  > Faz as funções em PHP com PDO e prepared statements para listar conversas, listar e enviar mensagens, fixar, favoritar e apagar para todos, validando as regras de negócio no servidor: no máximo 3 fixadas (RN01), apagar para todos só até 15 minutos (RN02) e contato bloqueado não recebe mensagem (RN03). As páginas da pasta api devem responder em JSON e o conversas.js deve carregar tudo com fetch.

- **Como foi usado:** as funções entraram no `includes/funcoes.php` e as rotas em `api/conversas.php` e `api/mensagens.php`. O `js/conversas.js` da Parte 2 foi adaptado para usar `fetch` em vez dos dados fixos. Testamos cada regra no XAMPP e corrigimos o cálculo dos 15 minutos para usar `TIMESTAMPDIFF` no MySQL.

### Prompt 14
- **Ferramenta:** Claude
- **Parte:** Parte 3 – QR code (RN04)
- **Prompt:**

  > Implementa o fluxo do QR code: o index.php gera um token aleatório salvo na tabela qr_codes, o QR mostra o link conectar.php?token=..., o celular logado confirma e o computador consulta a cada 2 segundos e entra nas conversas. O token vale 60 segundos e só pode ser usado uma vez (RN04). O QR precisa funcionar quando lido com a câmera de um celular de verdade.

- **Como foi usado:** gerou `api/qr.php`, `conectar.php`, `js/entrada.js` e `js/conectar.js`. A imagem do QR usa a API pública do qrserver.com. Para o celular conseguir abrir o link, publicamos o site no InfinityFree e o link do QR passou a usar o endereço público.

### Prompt 15
- **Ferramenta:** Claude
- **Parte:** Parte 3 – Painel do administrador
- **Prompt:**

  > Faz o painel do administrador com CRUD de contatos (criar, listar com busca, editar e excluir) e a lista das denúncias enviadas na página de perfil, tudo em PHP com validação no servidor e mantendo os dados preenchidos quando dá erro.

- **Como foi usado:** virou o `admin/painel.php` com as funções `listarContatosPainel`, `inserirContatoPainel`, `atualizarContatoPainel`, `excluirContatoPainel` e `listarDenuncias`. O JavaScript do painel só abre e fecha os modais.

### Prompt 16
- **Ferramenta:** Claude
- **Parte:** Parte 3 – Revisão e correções
- **Prompt:**

  > Passa um pente fino em tudo que não funciona: contato novo não aparece nas conversas, botões sem ação, o botão de anexar arquivo não funciona. Corrige e adiciona envio de foto ou PDF de até 5 MB conferindo o tipo real do arquivo no PHP.

- **Como foi usado:** a revisão gerou as correções em `conversas.php`, `nova-conversa.php` e `perfil.php` (abrir conversa com contato novo, menus e busca de mensagens) e o envio de arquivos com `finfo`, pasta `uploads/` e `.htaccess` bloqueando scripts. Testamos no XAMPP e no site publicado.

### Prompt 17
- **Ferramenta:** Claude
- **Parte:** Parte 3 – Fotos de perfil
- **Prompt:**

  > Coloca fotos de perfil nos contatos para melhorar o visual, usando avatares com licença livre.

- **Como foi usado:** os avatares foram gerados com o DiceBear (estilos Notionists e Shapes, licença CC0) e salvos em `img/avatares/`. A função `fotoContato()` escolhe a imagem pelo id do contato.

### Prompt 18
- **Ferramenta:** Claude
- **Parte:** Parte 3 – Publicação do site
- **Prompt:**

  > Como eu publico o site na internet de graça, com PHP e MySQL, para o QR funcionar em qualquer celular? Depois configura para cada push no GitHub atualizar o site sozinho.

- **Como foi usado:** o site foi publicado no InfinityFree (`whatsclone-grupo4.infinityfree.me`) com o banco importado pelo phpMyAdmin. A conexão passou a ler as credenciais de um `includes/config.php` fora do Git. O workflow `.github/workflows/deploy.yml` envia os arquivos por FTP a cada push na `main`.

## Parte 4 – Vídeo

### Prompt 19
- **Ferramenta:** Claude
- **Parte:** Parte 4 – Roteiro
- **Prompt:**

  > Monta o roteiro do vídeo de 8 a 10 minutos dividido entre os três integrantes, cada um apresentando a parte que fez, com uma folha de tempos dizendo o que aparece na tela e o que falar.

- **Como foi usado:** o roteiro e as folhas de tempos foram usados para gravar. Cada integrante gravou a própria narração.

### Prompt 20
- **Ferramenta:** Claude
- **Parte:** Parte 4 – Gravação das telas
- **Prompt:**

  > Grava as telas das minhas partes (abertura, QR code com celular, conversas e painel do administrador) no site publicado, em 1920x1080, sem som, para eu gravar o áudio por cima. Depois encaixa o meu áudio ajustando o vídeo à fala.

- **Como foi usado:** as telas das partes do Vinicius foram gravadas de forma automatizada e depois sincronizadas com a narração gravada por ele. As partes do Jadson e do Nicolas foram gravadas por eles mesmos.
