const form = document.getElementById('reservationForm');
const btn = document.getElementById('submitBtn');
const txt = document.getElementById('submitBtnText');
const alerts = document.getElementById('alertContainer');
let reservaId = document.getElementById('reservationId').value;

const isEdit = reservaId && reservaId !== '';

document.addEventListener('DOMContentLoaded', function () {
    setDefaultDate();
    setupListeners();

    if (isEdit) {
        loadReservation();
    }
});

function setupListeners() {
    form.addEventListener('submit', handleSubmit);
    document.getElementById('Telefone').addEventListener('input', formatPhone);
}

function setDefaultDate() {
    if (!isEdit) {
        const hoje = new Date();
        const ano = hoje.getFullYear();
        const mes = String(hoje.getMonth() + 1).padStart(2, '0');
        const dia = String(hoje.getDate()).padStart(2, '0');
        document.getElementById('DataReserva').value = `${ano}-${mes}-${dia}`;
    }
}

async function loadReservation() {
    try {
        const response = await fetch('api.php?action=list');
        const result = await response.json();

        if (result.success) {
            const reserva = result.data.find(r => r.id == reservaId);

            if (reserva) {
                document.getElementById('NomeCliente').value = reserva.NomeCliente;
                document.getElementById('Telefone').value = reserva.Telefone || '';
                document.getElementById('Email').value = reserva.Email || '';
                document.getElementById('Produto').value = reserva.Produto;
                document.getElementById('PrecoProduto').value = reserva.PrecoProduto;
                document.getElementById('Quantidade').value = reserva.Quantidade;
                document.getElementById('DataReserva').value = reserva.DataReserva;
                document.getElementById('DataRetirada').value = reserva.DataRetirada || '';
                document.getElementById('Status').value = reserva.Status;
            } else {
                showAlert('Reserva não encontrada', 'error');
                setTimeout(() => {
                    window.location.href = 'index.php';
                }, 2000);
            }
        }
    } catch (error) {
        console.error('Erro:', error);
        showAlert('Erro ao carregar dados da reserva', 'error');
    }
}

async function handleSubmit(e) {
    e.preventDefault();

    const formData = new FormData(form);
    const data = {};

    formData.forEach((value, key) => {
        data[key] = value;
    });

    const action = isEdit ? 'update' : 'create';
    if (isEdit) {
        data.id = reservaId;
    }

    txt.textContent = 'Salvando...';
    btn.disabled = true;

    try {
        const response = await fetch(`api.php?action=${action}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.success) {
            showAlert(result.message, 'success');
            setTimeout(() => {
                window.location.href = 'index.php';
            }, 1500);
        } else {
            showAlert(result.message, 'error');
            txt.textContent = isEdit ? 'Atualizar Reserva' : 'Criar Reserva';
            btn.disabled = false;
        }
    } catch (error) {
        console.error('Erro:', error);
        showAlert('Erro ao salvar reserva', 'error');
        txt.textContent = isEdit ? 'Atualizar Reserva' : 'Criar Reserva';
        btn.disabled = false;
    }
}

function formatPhone(e) {
    let val = e.target.value.replace(/\D/g, '');

    if (val.length <= 11) {
        if (val.length <= 2) {
            val = val.replace(/(\d{0,2})/, '($1');
        } else if (val.length <= 6) {
            val = val.replace(/(\d{2})(\d{0,4})/, '($1) $2');
        } else if (val.length <= 10) {
            val = val.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
        } else {
            val = val.replace(/(\d{2})(\d{5})(\d{0,4})/, '($1) $2-$3');
        }
    }

    e.target.value = val;
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
