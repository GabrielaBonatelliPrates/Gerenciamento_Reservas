<?php
session_start();
require_once 'auth.php';
checkAuth();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Reservas</title>
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
                <a href="form.php" class="btn btn-primary">+ Nova Reserva</a>
            </div>
        </header>

        <div id="alertContainer"></div>

        <section class="search-section">
            <div class="search-bar">
                <input type="text" id="searchInput" placeholder="Buscar por nome do cliente, produto ou data...">
            </div>
        </section>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Telefone</th>
                        <th>Email</th>
                        <th>Produto</th>
                        <th>Preço</th>
                        <th>Qtd</th>
                        <th>Data Reserva</th>
                        <th>Data Retirada</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="reservationsTableBody">
                </tbody>
            </table>
            <div id="emptyState" class="empty-state" style="display: none;">
                <div class="empty-state-icon">📦</div>
                <p>Nenhuma reserva encontrada</p>
            </div>
        </div>
    </div>

    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>⚠️ Confirmar Exclusão</h3>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir esta reserva?</p>
            </div>
            <div class="modal-actions">
                <button class="btn btn-secondary" onclick="closeDeleteModal()">Cancelar</button>
                <button class="btn btn-danger" onclick="confirmDelete()">Excluir</button>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>
