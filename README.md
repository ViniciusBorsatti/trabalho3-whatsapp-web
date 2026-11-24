# Clone do WhatsApp Web – Trabalho III

Trabalho da disciplina Desenvolvimento Web I do curso de Ciência da Computação do IFC – Campus Concórdia. É um clone do WhatsApp Web feito para fins educacionais, sem vínculo com o WhatsApp ou a Meta. Todos os dados são fictícios.

## Integrantes (Grupo 4)

- Jadson Butzk
- Vinicius Borsatti
- Nicolas Muller

## Site online

https://whatsclone-grupo4.infinityfree.me

## Tecnologias

- HTML5, CSS3 e JavaScript
- PHP 8 (sem framework)
- MySQL/MariaDB com PDO e prepared statements
- Bootstrap Icons

## Usuários de teste

| Área | Login | Senha |
| --- | --- | --- |
| Painel do administrador (`admin/login.php`) | admin@teste.com | admin123 |
| Usuário do site (`login.php`) | (49) 99999-0000 | usuario123 |

## Como executar (XAMPP)

1. Instale o [XAMPP](https://www.apachefriends.org/) e inicie o **Apache** e o **MySQL** no painel de controle.
2. Coloque a pasta do projeto em `C:\xampp\htdocs\trabalho3-whatsapp-web`.
3. Abra `http://localhost/phpmyadmin`, vá em **Importar**, escolha o arquivo `banco.sql` e clique em **Executar**. O script cria o banco `whatsapp_clone` com as tabelas e os dados de teste.
4. Acesse `http://localhost/trabalho3-whatsapp-web/`.

A conexão usa o usuário `root` sem senha, que é o padrão do XAMPP. Se o seu MySQL tiver outra senha, altere em `includes/conexao.php`.

Para importar o banco de novo, apague antes o banco `whatsapp_clone` no phpMyAdmin.

## Como testar o QR code

1. No computador, abra `index.php`. A página gera um QR code que vale 60 segundos.
2. No celular, entre na conta (`login.php`) e leia o QR code, ou, no mesmo computador, clique em **Abrir link do QR** para abrir a tela do celular em outra aba.
3. Na tela do celular, toque em **Conectar**. O computador entra sozinho nas Conversas.

Para ler o QR com a câmera de um celular de verdade:

- o celular precisa estar no **mesmo Wi-Fi** que o computador;
- o QR já usa o IP do computador na rede no lugar de `localhost` (o endereço aparece embaixo do QR). Se aparecer um IP errado, abra o site no computador pelo IP certo, por exemplo `http://192.168.0.10/trabalho3-whatsapp-web/` (veja o IP com o comando `ipconfig`, em "Endereço IPv4");
- na primeira vez, o Windows pode perguntar se libera o Apache no firewall: marque **Redes privadas** e permita.

## Páginas

| Página | Arquivo | Área | Responsável |
| --- | --- | --- | --- |
| Entrada (QR code) | index.php | Pública | Jadson / Vinicius |
| Cadastro | cadastro.php | Pública | Nicolas |
| Login | login.php | Pública | Nicolas |
| Conectar computador | conectar.php | Usuário logado | Nicolas / Vinicius |
| Conversas | conversas.php | Usuário logado | Vinicius |
| Perfil do contato | perfil.php | Pública | Jadson |
| Nova conversa (contatos) | nova-conversa.php | Pública | Jadson |
| Configurações | configuracoes.php | Usuário logado | Jadson |
| Login do administrador | admin/login.php | Restrita | Nicolas |
| Painel do administrador | admin/painel.php | Restrita | Vinicius |

## Funcionalidades

- Cadastro e login de usuário com senha criptografada (`password_hash` e `password_verify`) e sessão
- Login do administrador com sessão; o painel redireciona para o login quem não estiver logado
- Painel com CRUD de contatos (criar, listar com busca, editar e excluir) e lista das denúncias
- Lista pública de contatos com busca e cadastro de novo contato
- Perfil do contato com favoritar, bloquear e denúncia gravada no banco
- Conversas carregadas do banco com `fetch` (arquivos da pasta `api/` respondem em JSON)
- Envio de fotos (JPG, PNG, GIF, WEBP) e PDF de até 5 MB nas conversas; o PHP confere o tipo real do arquivo e salva na pasta `uploads/`
- Validação no PHP em todos os formulários, mantendo os dados preenchidos quando há erro
- Tema claro e escuro, layout responsivo

## Regras de negócio (validadas no PHP)

- **RN01:** no máximo 3 conversas fixadas
- **RN02:** "Apagar para todos" só para mensagens próprias com até 15 minutos
- **RN03:** contato bloqueado não recebe mensagens
- **RN04:** o QR code de conexão vale 60 segundos e só pode ser usado uma vez

## Estrutura de pastas

```
trabalho3-whatsapp-web/
├── index.php, cadastro.php, login.php, logout.php
├── conectar.php, conversas.php, perfil.php
├── nova-conversa.php, configuracoes.php
├── admin/
│   ├── login.php
│   ├── logout.php
│   └── painel.php
├── api/
│   ├── conversas.php     Lista, fixa, favorita e marca conversas como lidas
│   ├── mensagens.php     Lista, envia e apaga mensagens
│   └── qr.php            Gera e consulta o QR code
├── includes/
│   ├── conexao.php       Conexão PDO
│   ├── funcoes.php       Funções de banco e regras de negócio
│   ├── cabecalho.php
│   ├── rodape.php
│   ├── verifica-usuario.php
│   └── verifica-admin.php
├── css/
├── js/
├── img/
├── banco.sql             Criação do banco e dados de teste
├── PROMPTS.md
└── README.md
```

## Prompts utilizados

Os prompts usados no desenvolvimento estão em [PROMPTS.md](PROMPTS.md).
