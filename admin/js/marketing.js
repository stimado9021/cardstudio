// admin/js/marketing.js
// Lógica del panel de marketing masivo

async function loadStats() {
    try {
        const res = await fetch('api_emails.php?action=get_stats');
        const data = await res.json();
        if (data.success) {
            document.getElementById('subscriberCount').textContent = data.total_subscribers;
        }
    } catch (e) {
        console.error("Error loading stats", e);
    }
}

async function sendEmails() {
    const subject = document.getElementById('subject').value.trim();
    const content = document.getElementById('content').value.trim();
    const btn = document.getElementById('btnSend');
    const status = document.getElementById('statusMsg');

    if (!subject || !content) {
        showStatus('Por favor, completa el asunto y el contenido.', 'error');
        return;
    }

    if (!confirm('¿Estás seguro de enviar este correo a TODOS tus usuarios? Esta acción no se puede deshacer.')) {
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<span class="loader"></span> Enviando...';
    status.style.display = 'none';

    try {
        const res = await fetch('api_emails.php?action=send_mass', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ subject, content })
        });
        const data = await res.json();

        if (data.success) {
            showStatus(`¡Éxito! Se enviaron ${data.sent_count} de ${data.total_count} correos.`, 'success');
        } else {
            showStatus('Error: ' + (data.error || 'Ocurrió un problema inesperado.'), 'error');
        }
    } catch (e) {
        showStatus('Error de conexión con el servidor.', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '🚀 Enviar a todos';
    }
}

function showStatus(msg, type) {
    const status = document.getElementById('statusMsg');
    status.textContent = msg;
    status.className = type;
    status.style.display = 'block';
}

async function logout() {
    await fetch('api_admin_auth.php?action=logout');
    window.location.href = 'login.php';
}

document.addEventListener('DOMContentLoaded', loadStats);
