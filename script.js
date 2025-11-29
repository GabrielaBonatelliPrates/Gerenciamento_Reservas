let deleteId = null;
let searchTimeout = null;

const tbody = document.getElementById('reservationsTableBody');
const emptyState = document.getElementById('emptyState');
const modal = document.getElementById('deleteModal');
const alerts = document.getElementById('alertContainer');
const searchInput = document.getElementById('searchInput');

document.addEventListener('DOMContentLoaded', function () {
    loadReservations();
    setupListeners();
});

function setupListeners() {
    searchInput.addEventListener('input', handleSearch);

    modal.addEventListener('click', function (e) {
        if (e.target === modal) {
            closeDeleteModal();
        }
    });
}

async function loadReservations() {
    try {
        const response = await fetch('api.php?action=list');
        const result = await response.json();

        if (result.success) {
            displayReservations(result.data);
        } else {
            showAlert(result.message || 'Erro ao carregar reservas', 'error');
        }
    } catch (error) {
        console.error('Erro:', error);
        showAlert('Erro de conexão', 'error');
    }
}

function displayReservations(reservations) {
    if (!reservations || reservations.length === 0) {
        tbody.innerHTML = '';
        emptyState.style.display = 'block';
        return;
    }

    emptyState.style.display = 'none';

    tbody.innerHTML = reservations.map(reserva => `
        <tr>
            <td>${reserva.id}</td>
            <td>${reserva.NomeCliente}</td>
            <td>${reserva.Telefone || '-'}</td>
            <td>${reserva.Email || '-'}</td>
            <td>${reserva.Produto}</td>
            <td>R$ ${parseFloat(reserva.PrecoProduto).toFixed(2)}</td>
            <td>${reserva.Quantidade}</td>
            <td>${formatDate(reserva.DataReserva)}</td>
            <td>${reserva.DataRetirada ? formatDate(reserva.DataRetirada) : '-'}</td>
            <td>
                <span class="status-badge status-${reserva.Status}">
                    ${reserva.Status === 'reservado' ? '🕐 Reservado' : '✅ Retirado'}
                </span>
            </td>
            <td>
                <div class="action-buttons">
                    <a href="form.php?id=${reserva.id}" class="btn btn-icon btn-secondary" title="Editar">
                        &#9998;
                    </a>
                    <button class="btn btn-icon btn-danger" onclick="openDeleteModal(${reserva.id})" title="Excluir">
                        <svg width="14" height="14" viewBox="0 0 16 16" fill="currentColor"><path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/><path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/></svg>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

function handleSearch(e) {
    clearTimeout(searchTimeout);

    const query = e.target.value.trim();

    searchTimeout = setTimeout(async () => {
        const url = query
            ? `api.php?action=search&query=${encodeURIComponent(query)}`
            : 'api.php?action=list';

        const response = await fetch(url);
        const result = await response.json();

        if (result.success) {
            displayReservations(result.data);
        }
    }, 300);
}

function openDeleteModal(id) {
    deleteId = id;
    modal.classList.add('active');
}

function closeDeleteModal() {
    deleteId = null;
    modal.classList.remove('active');
}

async function confirmDelete() {
    if (!deleteId) return;

    try {
        const response = await fetch('api.php?action=delete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: deleteId })
        });

        const result = await response.json();

        if (result.success) {
            showAlert(result.message, 'success');
            loadReservations();
        } else {
            showAlert(result.message, 'error');
        }
    } catch (error) {
        console.error('Erro:', error);
        showAlert('Erro ao excluir reserva', 'error');
    } finally {
        closeDeleteModal();
    }
}

function showAlert(msg, type) {
    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
    alert.textContent = msg;

    alerts.appendChild(alert);

    setTimeout(() => {
        alert.remove();
    }, 5000);
}

function formatDate(dateString) {
    const date = new Date(dateString + 'T00:00:00');
    return date.toLocaleDateString('pt-BR');
}

