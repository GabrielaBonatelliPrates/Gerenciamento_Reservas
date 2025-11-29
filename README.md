# Sistema de Gerenciamento de Reservas

Sistema web simples para gerenciar reservas de produtos da empresa Batlló com autenticação, CRUD completo e integração MySQL.

## 🎨 Funções do sistema

- Login fixo com os dados da loja
- Criar, editar e excluir reservas
- Listagem de reservas ordenadas por data de retirada
- Busca por nome do cliente, produto ou data


## 📋 Requisitos

- XAMPP (Apache + MySQL + PHP)
- Navegador web moderno


## 🚀 Instalação

### 1. Configurar o Banco de Dados

1. Abra o **MySQL Workbench** ou **phpMyAdmin**
2. Execute o script `database.sql` para criar o banco e a tabela
3. O banco `reservas_bd` será criado com dados de exemplo

### 2. Configurar o Projeto

1. Certifique-se de que o XAMPP está instalado
2. Os arquivos já estão em: `c:\xampp\htdocs\Gerenciamento_Reservas\batllo\`
3. Inicie o Apache e MySQL no painel do XAMPP

### 3. Acessar o Sistema

1. Abra o navegador
2. Acesse: `http://localhost/Gerenciamento_Reservas/batllo/login.php`
3. Faça login com as credenciais:
   - **Usuário**: `batllo@gmail.com`
   - **Senha**: `12345`


## 🎯 Como Usar

### Login
1. Acesse `login.php`
2. Digite as credenciais
3. Clique em "Entrar"

### Listar Reservas
- A página principal mostra todas as reservas
- Ordenadas por data de retirada (mais próxima primeiro)

### Buscar Reservas
- Digite no campo de busca
- Busca por: nome do cliente, produto ou data
- Resultados aparecem em tempo real

### Criar Reserva
1. Clique em "Nova Reserva"
2. Preencha todos os campos 
3. Clique em "Criar Reserva"

### Editar Reserva
1. Clique no botão ✏️ na linha da reserva
2. Modifique os campos desejados
3. Clique em "Atualizar Reserva"

### Excluir Reserva
1. Clique no botão 🗑️ na linha da reserva
2. Confirme a exclusão
3. A reserva será removida

## 🗄️ Estrutura do Banco de Dados

**Banco**: `reservas_bd`  
**Tabela**: `Reservas`

| Campo | Tipo | Descrição |
|-------|------|-----------|
| id | INT | ID único (auto incremento) |
| NomeCliente | VARCHAR(100) | Nome do cliente |
| Telefone | VARCHAR(20) | Telefone do cliente |
| Email | VARCHAR(100) | Email do cliente |
| Produto | VARCHAR(100) | Nome do produto |
| PrecoProduto | DECIMAL(10,2) | Preço do produto |
| Quantidade | INT | Quantidade reservada |
| DataReserva | DATE | Data da reserva |
| DataRetirada | DATE | Data de retirada |
| Status | ENUM | 'reservado' ou 'retirado' |

## 🛠️ Tecnologias Utilizadas

- **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
- **Backend**: PHP 7+ (MySQLi)
- **Banco de Dados**: MySQL
- **Servidor**: Apache (XAMPP)
