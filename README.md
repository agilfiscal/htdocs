# Meu Site

Um site moderno, responsivo e totalmente personalizável desenvolvido com PHP.

## 🚀 Características

- Design responsivo e moderno
- Sistema de rotas amigável
- Estrutura MVC organizada
- Suporte a temas personalizáveis
- Sistema de autenticação
- API RESTful
- Cache e otimizações de performance

## 📋 Pré-requisitos

- PHP 7.4 ou superior
- Apache com mod_rewrite habilitado
- MySQL 5.7 ou superior
- Composer (gerenciador de dependências PHP)

## 🔧 Instalação

1. Clone o repositório:
   ```bash
git clone https://github.com/seu-usuario/meu-site.git
   ```

2. Instale as dependências:
   ```bash
composer install
   ```

3. Configure o banco de dados:
- Crie um banco de dados MySQL
- Copie o arquivo `.env.example` para `.env`
- Configure as credenciais do banco de dados no arquivo `.env`

4. Configure o servidor web:
- Aponte o document root para a pasta `public/`
- Certifique-se que o mod_rewrite está habilitado

5. Inicie o servidor:
     ```bash
php -S localhost:8000 -t public
```

## 📁 Estrutura do Projeto

```
meu-site/
│
├── public/                # Raiz pública acessível pela web
│   ├── index.php         # Arquivo principal
│   ├── assets/           # Arquivos públicos (CSS, JS, imagens)
│   └── .htaccess         # Configurações do Apache
│
├── app/                   # Código da aplicação
│   ├── Controllers/      # Controladores
│   ├── Models/           # Modelos
│   ├── Views/            # Views
│   ├── Core/             # Núcleo do sistema
│   └── Helpers/          # Funções auxiliares
│
├── routes/               # Arquivos de rotas
├── config/              # Configurações
├── storage/             # Arquivos gerados
├── tests/               # Testes
└── vendor/              # Dependências
```

## 🛠️ Tecnologias Utilizadas

- PHP 7.4+
- MySQL
- HTML5
- CSS3 (com variáveis CSS)
- JavaScript (ES6+)
- Composer
- Apache

## 📝 Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

## 👥 Contribuição

1. Faça um Fork do projeto
2. Crie uma Branch para sua Feature (`git checkout -b feature/AmazingFeature`)
3. Faça o Commit das suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Faça o Push para a Branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 📧 Contato

Seu Nome - [@seu_twitter](https://twitter.com/seu_twitter) - email@exemplo.com

Link do Projeto: [https://github.com/seu-usuario/meu-site](https://github.com/seu-usuario/meu-site)
