# Sistema de Gestão de Clínicas

Sistema de gestão para múltiplas clínicas, desenvolvido em Laravel 11.

## Funcionalidades

### Módulo Master
- **Gestão de Clínicas**
  - CRUD completo de clínicas
  - Ativação/desativação de clínicas
  - Soft delete com restauração
  - Busca por nome

- **Gestão de Administradores**
  - CRUD completo de administradores
  - Atribuição de clínicas aos administradores
  - Ativação/desativação de administradores
  - Soft delete
  - Busca por nome, email ou CPF

### Módulo de Clínicas
- **Pacientes**
  - Cadastro e gestão de pacientes
  - Histórico de atendimentos
  - Ativação/desativação de pacientes

- **Atendimentos**
  - Agendamento de consultas
  - Registro de procedimentos
  - Histórico de atendimentos

- **Procedimentos**
  - Cadastro de procedimentos
  - Associação com convênios
  - Valores e códigos

- **Convênios**
  - Gestão de planos de saúde
  - Configuração de coberturas
  - Valores e descontos

- **Estoque**
  - Controle de estoque
  - Entradas e saídas
  - Gestão de produtos

## Estrutura do Projeto

```
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Master/
│   │   │   │   ├── ClinicaController.php
│   │   │   │   └── AdminUserController.php
│   │   │   └── ...
│   │   └── Middleware/
│   │       └── MasterMiddleware.php
│   └── Models/
│       ├── User.php
│       └── Clinica.php
├── resources/
│   └── views/
│       ├── layouts/
│       │   ├── app.blade.php
│       │   └── menu.blade.php
│       └── master/
│           ├── clinicas.blade.php
│           └── admin-users.blade.php
└── routes/
    ├── web.php
    └── master.php
```

## Requisitos

- PHP 8.2+
- MySQL 5.7+
- Composer
- Node.js e NPM

## Instalação

1. Clone o repositório
2. Instale as dependências:
   ```bash
   composer install
   npm install
   ```
3. Configure o arquivo `.env`
4. Execute as migrações:
   ```bash
   php artisan migrate
   ```
5. Inicie o servidor:
   ```bash
   php artisan serve
   ```

## Perfis de Usuário

- **Master**: Acesso total ao sistema, pode gerenciar clínicas e administradores
- **Admin**: Acesso à clínica específica, pode gerenciar equipe e configurações
- **Médico**: Acesso a pacientes e atendimentos
- **Equipe Multidisciplinar**: Acesso a pacientes e procedimentos

## Segurança

- Autenticação com Laravel Sanctum
- Middleware de proteção por perfil
- Soft delete para registros importantes
- Validação de dados em todas as operações

## Contribuição

1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.
