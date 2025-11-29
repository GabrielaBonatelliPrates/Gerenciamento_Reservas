<?php
session_start();
require_once 'auth.php';
checkAuth();
$editMode = isset($_GET['id']) && is_numeric($_GET['id']);
$reservaId = $editMode ? $_GET['id'] : null;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $editMode ? 'Editar' : 'Nova'; ?> Reserva</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>Batlló Reservas</h1>
            <div class="header-actions">
                <a href="index.php" class="btn btn-secondary">← Voltar</a>
            </div>
        </header>

        <div id="alertContainer"></div>

        <div class="form-card">
            <h2><?php echo $editMode ? 'Editar Reserva' : 'Nova Reserva'; ?></h2>
            
            <form id="reservationForm">
                <input type="hidden" id="reservationId" value="<?php echo $reservaId; ?>">
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="NomeCliente">Nome do Cliente</label>
                        <input type="text" id="NomeCliente" name="NomeCliente" placeholder="Ex: Maria Silva" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="Telefone">Telefone</label>
                        <input type="tel" id="Telefone" name="Telefone" placeholder="(11) 98765-4321">
                    </div>
                    
                    <div class="form-group">
                        <label for="Email">Email</label>
                        <input type="email" id="Email" name="Email" placeholder="cliente@email.com">
                    </div>
                    
                    <div class="form-group">
                        <label for="Produto">Produto</label>
                        <input type="text" id="Produto" name="Produto" placeholder="Ex: Jogo de Panelas">
                    </div>
                    
                    <div class="form-group">
                        <label for="PrecoProduto">Preço do Produto (R$)</label>
                        <input type="number" id="PrecoProduto" name="PrecoProduto" step="0.01" min="0" placeholder="299.90" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="Quantidade">Quantidade</label>
                        <input type="number" id="Quantidade" name="Quantidade" min="1" placeholder="1" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="DataReserva">Data da Reserva</label>
                        <input type="date" id="DataReserva" name="DataReserva" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="DataRetirada">Data de Retirada</label>
                        <input type="date" id="DataRetirada" name="DataRetirada">
                    </div>
                    
                    <div class="form-group">
                        <label for="Status">Status</label>
                        <select id="Status" name="Status" required>
                            <option value="reservado">Reservado</option>
                            <option value="retirado">Retirado</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-actions">
                    <a href="index.php" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <span id="submitBtnText"><?php echo $editMode ? 'Atualizar' : 'Criar'; ?> Reserva</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="form.js?v=<?php echo time(); ?>"></script>
</body>
</html>
