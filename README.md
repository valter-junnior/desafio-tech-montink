<h1 align="center" style="font-weight: bold;">Desafio tech Montink💻</h1>

<p align="center">
  <a href="#technologies">Technologies</a> • 
  <a href="#getting-started">Getting Started</a> • 
   <a href="#api-endpoints">API Endpoints</a> •
  <a href="#collaborators">Collaborators</a> •
  <a href="#contribute">Contribute</a>
</p>

<p align="center">
     <b>Um projeto de e-commerce PHP simples, com gerenciamento de produtos, estoque (incluindo variações), carrinho de compras, cálculo de frete, aplicação de cupons, integração com ViaCEP, envio de e-mails de confirmação de pedido e webhook para atualização de status/cancelamento.</b>
</p>

---

<h2 id="technologies">💻 Technologies</h2>

- **PHP 8.2**: Linguagem de programação backend.
- **MySQL 8.0**: Sistema de gerenciamento de banco de dados relacional.
- **Nginx**: Servidor web para servir a aplicação PHP.
- **PHP-FPM**: Processador FastCGI para PHP, otimizado para Nginx.
- **Docker & Docker Compose**: Para orquestração e gerenciamento do ambiente de desenvolvimento.
- **Composer**: Gerenciador de dependências PHP.
- **PHPMailer**: Biblioteca para envio de e-mails via SMTP.
- **Bootstrap 5.3**: Framework CSS para desenvolvimento frontend responsivo e moderno.
- **jQuery**: Biblioteca JavaScript para manipulação de DOM e requisições AJAX (usada para ViaCEP).
- **ViaCEP API**: Serviço para consulta de endereços a partir do CEP.

---

<h2 id="getting-started">🚀 Getting started</h2>

Este guia irá ajudá-lo a configurar e rodar o projeto localmente usando Docker.

### Prerequisites

Certifique-se de ter as seguintes ferramentas instaladas em sua máquina:

- [**Docker Desktop**](https://www.docker.com/products/docker-desktop/) (que inclui Docker Engine e Docker Compose)
- [**Git**](https://git-scm.com/downloads)

### Cloning

Clone este repositório para o seu ambiente local:

```bash
git clone [https://github.com/valter-junnior/seu-projeto-ecommerce.git](https://github.com/valter-junnior/seu-projeto-ecommerce.git) 
```

### Project Structure

O projeto segue uma estrutura MVC básica:

```
.
├── .docker/             # Contém Dockerfiles e configurações para Nginx, PHP e MySQL
│   ├── nginx/
│   │   └── Dockerfile
│   │   └── default.conf
│   ├── php/
│   │   └── Dockerfile
│   │   └── php.ini
│   └── mysql/
│       └── init.sql     # Schema do banco de dados e dados iniciais
├── app/                 # Diretório principal da aplicação PHP
│   ├── config/          # Configurações do banco de dados
│   ├── controllers/     # Lógica da aplicação (controladores)
│   ├── models/          # Interação com o banco de dados (modelos)
│   ├── views/           # Interface do usuário (templates)
│   ├── public/          # Arquivos acessíveis via web (CSS, JS, index.php)
│   │   ├── index.php    # Front controller
│   │   ├── css/
│   │   │   └── custom.css
│   │   └── js/
│   │       └── script.js
│   ├── services/        # Serviços externos (ViaCEP, E-mail)
│   └── routes.php       # Definição de rotas simples
│   └── composer.json    # Dependências PHP (PHPMailer)
├── docker-compose.yml   # Definição dos serviços Docker
└── README.md
```

### Starting the Project

1.  **Navegue** até o diretório raiz do projeto no seu terminal:

    ```bash
    cd simple-ecommerce-php # Ou o nome da pasta que você clonou
    ```

2.  **Suba os containers Docker:**

    ```bash
    docker-compose up --build -d
    ```

    Este comando irá construir as imagens Docker (se necessário) e iniciar os serviços `nginx`, `php`, `mysql` e `phpmyadmin` em segundo plano.

3.  **Instale as dependências PHP via Composer:**
    Uma vez que os containers estejam rodando, execute o Composer dentro do container `php` para instalar as dependências (como o PHPMailer). O `composer.json` deve estar na raiz da sua pasta `app`.

    ```bash
    docker-compose exec php composer install
    ```

    _Isso é necessário porque o seu diretório `./src` (onde está o `composer.json`) é montado no container PHP em `/var/www/html`._

### Accessing the Application

Após os passos acima, sua aplicação estará acessível:

- **Aplicação Principal:** `http://localhost`
- **phpMyAdmin:** `http://localhost:8080`
  - **Usuário:** `root`
  - **Senha:** `root_password` (ou a senha definida para `MYSQL_ROOT_PASSWORD` no `.env`)

---

<h2 id="api-endpoints"\>📍 API Endpoints\</h2\>

A aplicação possui rotas amigáveis e um endpoint de API para consulta de CEP e um webhook para comunicação de pedidos.

| Route                                     | Description                                                                          |
| :---------------------------------------- | :----------------------------------------------------------------------------------- |
| \<kbd\>GET /\</kbd\>                      | Redireciona para a listagem de produtos.                                             |
| \<kbd\>GET /products\</kbd\>              | Exibe o formulário de cadastro/edição de produtos e a lista de produtos cadastrados. |
| \<kbd\>POST /products\</kbd\>             | Cadastra um novo produto com suas variações e estoque inicial.                       |
| \<kbd\>GET /products/edit?id={id}\</kbd\> | Carrega o formulário para edição de um produto existente.                            |