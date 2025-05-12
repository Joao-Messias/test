# Sistema de Gerenciamento de Tarefas (To-Do List)

## Sobre o projeto

Este é um sistema de gerenciamento de tarefas (To-Do List) desenvolvido com Laravel 10, Bootstrap 5 e jQuery 3. O sistema permite que usuários criem, visualizem, editem e excluam tarefas, além de gerenciar o acesso de outros usuários às tarefas.

## Requisitos Funcionais

### 1. Autenticação de usuários
- O usuário deve ser capaz de usar o login e senha informados durante o cadastro para se autenticar na aplicação
- A aplicação deverá verificar se o usuário está ativo durante a etapa de login
- O usuário deve ser capaz de alterar ou recuperar a senha, em caso de esquecimento

### 2. Cadastro de usuários
- O sistema deve possuir um usuário administrador que será responsável por criar usuários através de um cadastro exclusivo para esse perfil
- O administrador deve ser capaz de cadastrar novos usuários informando:
  - Nome (obrigatório, mínimo de 3 caracteres, máximo de 200)
  - E-mail (obrigatório, único por usuário, e-mail válido, máximo de 200 caracteres)
  - Senha (obrigatório, mínimo de 8 caracteres, com letras, números e símbolos)
  - Status (obrigatório, booleano)
- A senha deve ser criptografada antes de inserir o registro no banco de dados

### 3. Listagem de usuários
- Mostrar uma lista com os usuários cadastradas, incluindo:
  - Nome, e-mail e status
  - Ações para editar e excluir a tarefa

### 4. Atualização de usuários
- O administrador poderá atualizar o nome, e-mail, status e senha do usuário seguindo os mesmos critérios do cadastro

### 5. Exclusão de usuários
- O administrador poderá excluir um usuário
- Antes da exclusão deve aparecer um modal de confirmação
- A exclusão somente poderá ser efetivada se o usuário não estiver relacionado a nenhuma tarefa

### 6. Cadastro de Tarefas
- Todos os usuários do sistema devem ser capaz de cadastrar uma nova tarefa informando:
  - Título (obrigatório, mínimo de 3 caracteres, máximo de 255)
  - Descrição (opcional, máximo de 500 caracteres)
  - Status (pendente ou concluída)
- Ao realizar o cadastro, a tarefa deve ser automaticamente vinculada ao usuário que criou a tarefa

### 7. Listagem de Tarefas
- Mostrar uma lista com as tarefas cadastradas, incluindo:
  - Título, descrição e status
  - Ações para editar e excluir a tarefa
- A listagem deve trazer, por padrão, apenas as tarefas vinculadas ao usuário logado. Entretanto, o filtro pode ser alterado para mostrar tarefas vinculadas a outras pessoas

### 8. Gerenciar usuários das tarefas
- Todos os usuários do sistema devem ser capazes de gerenciar as pessoas envolvidas na tarefa, adicionando ou removendo pessoas, inclusive a si mesmo

### 9. Marcar tarefa como concluída
- Todos os usuários do sistema devem ser capazes de marcar a tarefa como concluída através de um clique

### 10. Atualização de Tarefas
- O usuário deve poder atualizar o título, descrição ou status de uma tarefa seguindo os mesmos critérios do cadastro

### 11. Exclusão de Tarefas
- O usuário deve poder excluir uma tarefa
- Antes da exclusão, deve aparecer um modal de confirmação

### 12. Pesquisa e filtro de tarefas
- Permitir a pesquisa de tarefas pelo título
- Filtrar as tarefas pelo status (pendente ou concluída)
- Filtrar as tarefas por pessoas

## Requisitos Técnicos

### 1. Backend
- Utilizar Laravel 10 para gerenciar o backend
- As rotas devem ser organizadas em um Resource Controller
- As validações devem ser feitas usando Form Requests
- A aplicação deve utilizar Eloquent ORM para acessar o banco de dados
- Utilizar middlewares, policies ou gates para verificação de permissões de acesso quando aplicável
- Criar Migrations para as tabelas do banco de dados
- Utilizar transações de banco de dados durante, cadastros, edições e exclusão dos registros das tabelas
- Criar mensagens de feedback para ações do CRUD (ex.: "Tarefa criada com sucesso!")

### 2. Frontend
- Utilizar Bootstrap 5 para construção das interfaces
- Usar jQuery 3 para adicionar comportamento as páginas

### 3. Extras (Diferenciais)
- Criar interfaces responsivas para utilização em dispositivos móveis
- Criar componentes blade para reutilização de código
- Escrever testes unitários e/ou funcionais para o backend

## Observações

1. Os requisitos técnicos visam garantir a utilização das tecnologias e técnicas relevantes para o cargo aplicado.
2. O participante é livre para fazer uso de outras tecnologias que julgar necessárias para resolução do problema.
3. Quaisquer pontos que não tenham sido abordados na descrição do teste, podem ser implementados com base na livre interpretação do participante.
4. Embora não seja obrigatório, considere a utilização da biblioteca jQuery Datatable para listagem dos registros em tabela. O link para a documentação é: https://datatables.net/
5. Existe ainda uma abstração no back-end para a biblioteca datatables, que permite processar os dados do lado do servidor e somente exibi-los no frontend, com a devida paginação, ordenação e filtros aplicados. O link para a documentação é: https://yajrabox.com/docs/laravel-datatables/11.0


## Critérios de avaliação

1. Organização e clareza do código. 
2. Qualidade da interface e experiência do usuário. 
3. Uso correto das tecnologias solicitadas (Laravel, Bootstrap, jQuery). 
4. Cumprimento dos requisitos funcionais 

## Requisitos

- Docker

## Instalação e Execução

1. Clone o repositório:

2. Configure o ambiente:
```bash
cp .env.example .env
```

3. Inicie os containers Docker:
```bash
docker-compose up -d
```

4. Instale as dependências do projeto:
```bash
docker-compose exec app composer install
```

5. Gere a chave da aplicação:
```bash
docker-compose exec app php artisan key:generate
```

6. Execute as migrações e seeders:
```bash
docker-compose exec app php artisan migrate --seed
```

A aplicação estará disponível em: http://localhost:8000

## Executando os Testes

Para executar os testes:

```bash
docker-compose exec app php artisan test
```

## Estrutura do Projeto

- `app/Actions/Task/`: Contém as classes de ação para operações com tarefas
- `app/Http/Controllers/`: Contém os controllers da aplicação
- `app/Models/`: Contém os modelos da aplicação
- `app/Policies/`: Contém as políticas de autorização
- `database/migrations/`: Contém as migrações do banco de dados
- `tests/`: Contém os testes da aplicação

## Funcionalidades

- CRUD completo de tarefas
- Atribuição de múltiplos usuários a uma tarefa
- Filtros por status, título e usuário
- Autenticação de usuários
- Políticas de autorização para edição/exclusão de tarefas

## Tecnologias Utilizadas

- PHP 8.2
- Laravel 10.x
- MySQL 8.0
- Docker
- PHPUnit para testes

## Observações

- O banco de dados MySQL está configurado com as seguintes credenciais padrão:
  - Database: laravel
  - Username: laravel
  - Password: root
  - Root Password: root

- As credenciais podem ser alteradas no arquivo `.env` e no `docker-compose.yml`

- Os dados do banco são persistidos em um volume Docker para manter os dados entre reinicializações dos containers 