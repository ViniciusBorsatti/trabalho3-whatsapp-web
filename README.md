# Clone do WhatsApp Web – Trabalho III

Trabalho da disciplina Desenvolvimento Web I do curso de Ciência da Computação do IFC – Campus Concórdia. É um clone do WhatsApp Web feito para fins educacionais, sem vínculo com o WhatsApp ou a Meta. Todos os dados são fictícios.

## Integrantes (Grupo 4)

- Jadson Butzk
- Vinicius Borsatti
- Nicolas Muller

## Site publicado

https://viniciusborsatti.github.io/trabalho3-whatsapp-web/

## Tecnologias

- HTML5
- CSS3
- JavaScript
- Bootstrap Icons

## Páginas

| Página | Arquivo | Área | Responsável |
| --- | --- | --- | --- |
| Entrada | index.html | Pública | Jadson |
| Cadastro | cadastro.html | Pública | Nicolas |
| Login | login.html | Pública | Nicolas |
| Conectar aparelho | conectar.html | Pública | Nicolas |
| Conversas | conversas.html | Pública | Vinicius |
| Perfil do contato | perfil.html | Pública | Jadson |
| Nova conversa | nova-conversa.html | Pública | Jadson |
| Configurações | configuracoes.html | Pública | Jadson |
| Login do administrador | admin/login.html | Restrita | Nicolas |
| Painel do administrador | admin/painel.html | Restrita | Vinicius |

## Funcionalidades

- Tema claro e escuro salvo no navegador
- Busca e filtros de conversas
- Envio de mensagens
- Layout responsivo
- RN01: no máximo 3 conversas fixadas
- RN02: apagar para todos só até 15 minutos depois do envio
- RN03: contato bloqueado não recebe mensagens
- RN04: QR code válido por 60 segundos e de uso único

## Estrutura de pastas

```
trabalho3-whatsapp-web/
├── index.html
├── cadastro.html
├── login.html
├── conectar.html
├── conversas.html
├── perfil.html
├── nova-conversa.html
├── configuracoes.html
├── admin/
│   ├── login.html
│   └── painel.html
├── css/
│   ├── style.css
│   ├── entrada.css
│   ├── cadastro.css
│   ├── login.css
│   ├── conectar.css
│   ├── conversas.css
│   ├── perfil.css
│   ├── nova-conversa.css
│   ├── configuracoes.css
│   ├── admin-login.css
│   └── admin-painel.css
├── js/
│   ├── tema.js
│   ├── entrada.js
│   ├── cadastro.js
│   ├── login.js
│   ├── conectar.js
│   ├── conversas.js
│   ├── perfil.js
│   ├── nova-conversa.js
│   ├── configuracoes.js
│   ├── admin-login.js
│   └── admin-painel.js
├── dados/
│   └── conversas.json
├── img/
│   └── prints/
├── PROMPTS.md
└── README.md
```

## Como executar localmente

1. Abra a pasta do projeto no VS Code.
2. Instale a extensão Live Server.
3. Clique com o botão direito em `index.html` e escolha "Open with Live Server".

O projeto precisa ser aberto por um servidor local porque as páginas carregam os arquivos JSON com `fetch`, e o navegador bloqueia essa leitura quando o HTML é aberto direto pelo endereço `file://`.

## Capturas de tela

### Conversas

![Tela de conversas](img/prints/conversas.png)

### Conversas no tema escuro

![Tela de conversas no tema escuro](img/prints/conversas-escuro.png)

### Painel do administrador

![Painel do administrador](img/prints/painel.png)

## Próximos passos

A Parte 3 do trabalho vai adicionar back-end com PHP e MySQL.

## Prompts utilizados

Os prompts usados no desenvolvimento estão em [PROMPTS.md](PROMPTS.md).
