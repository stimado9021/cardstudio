// js/editor_cliente.js
// Motor del editor de cliente - Canvas, autenticación, pagos y descarga

const canvas = document.getElementById('clienteCanvas');
const ctx = canvas.getContext('2d');
const params = new URLSearchParams(window.location.search);
const disenoId = params.get('id');
let fondoImg = new Image();
let textos = [];

let isLoggedIn = false;
let hasPaid = false;

async function init() {
    if (!disenoId) return alert(i18n.t("alert_no_design"));

    await checkAuthStatus();

    try {
        const response = await fetch(`api_publica.php?action=get_design_details&id=${disenoId}`);
        const data = await response.json();

        fondoImg.src = `admin/${data.imagen_fondo_url}`;
        fondoImg.onload = () => draw();

        const jsonRaw = data.configuracion_textos_json || data.config_json;
        if (jsonRaw) {
            textos = JSON.parse(jsonRaw);
        }

        const contenedor = document.getElementById('controles-dinamicos');
        contenedor.innerHTML = "";
        textos.forEach((t, index) => {
            const div = document.createElement('div');
            div.className = 'input-group';
            if (t.type === 'paragraph') {
                div.innerHTML = `
                    <label>${i18n.t('label_text')} ${index + 1}:</label>
                    <textarea oninput="actualizarTexto(${index}, this.value)" rows="3" style="width: 100%; border-radius: 5px; padding: 10px; background: rgba(0,0,0,0.3); color: white; border: 1px solid rgba(255,255,255,0.1); resize: vertical; font-family: inherit;">${t.contenido}</textarea>
                `;
            } else {
                div.innerHTML = `
                    <label>${i18n.t('label_text')} ${index + 1}:</label>
                    <input type="text" value="${t.contenido}" oninput="actualizarTexto(${index}, this.value)">
                `;
            }
            contenedor.appendChild(div);
        });

        document.getElementById('titulo-diseno').innerText = data.nombre_diseno;

    } catch (error) {
        console.error("Error al cargar:", error);
    }
}

async function checkAuthStatus() {
    try {
        let res = await fetch('api_auth.php?action=check');
        let data = await res.json();
        isLoggedIn = data.logged_in;
        if (isLoggedIn) {
            let resPago = await fetch(`api_pagos.php?action=check&diseno_id=${disenoId}`);
            let dataPago = await resPago.json();
            hasPaid = dataPago.has_paid;
        }
    } catch(e) { console.error(e); }
}

function actualizarTexto(index, nuevoValor) {
    textos[index].contenido = nuevoValor;
    draw();
}

function draw() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    if (fondoImg.complete) {
        ctx.drawImage(fondoImg, 0, 0, canvas.width, canvas.height);
    }

    textos.forEach(t => {
        ctx.save();
        ctx.font = `bold ${t.size}px ${t.family}`;

        let lines = [];
        let lineHeight = t.size * 1.2;

        if (t.type === 'paragraph') {
            let paragraphs = t.contenido.split('\n');
            for (let p of paragraphs) {
                let words = p.split(' ');
                let currentLine = '';
                for (let i = 0; i < words.length; i++) {
                    let testLine = currentLine + words[i] + ' ';
                    let metrics = ctx.measureText(testLine);
                    if (metrics.width > t.maxWidth && i > 0) {
                        lines.push(currentLine.trim());
                        currentLine = words[i] + ' ';
                    } else {
                        currentLine = testLine;
                    }
                }
                lines.push(currentLine.trim());
            }
            t.width = t.maxWidth;
            t.height = lines.length * lineHeight;
        } else {
            lines = [t.contenido];
            const m = ctx.measureText(t.contenido);
            t.width = m.width;
            t.height = t.size;
        }

        ctx.textAlign = t.align || "left";
        ctx.textBaseline = "top";

        const cx = t.x + t.width / 2;
        const cy = t.y + t.height / 2;

        if (t.angle) {
            ctx.translate(cx, cy);
            ctx.rotate(t.angle);
            ctx.translate(-cx, -cy);
        }

        if (t.hasShadow) {
            ctx.shadowColor = t.sColor || 'transparent';
            ctx.shadowBlur = t.sBlur || 0;
            ctx.shadowOffsetX = t.sOffsetX || 0;
            ctx.shadowOffsetY = t.sOffsetY || 0;
        } else {
            ctx.shadowColor = 'transparent';
            ctx.shadowBlur = 0;
            ctx.shadowOffsetX = 0;
            ctx.shadowOffsetY = 0;
        }

        for (let i = 0; i < lines.length; i++) {
            let ly = t.y + (i * lineHeight);
            let lx = t.x;
            if (t.type === 'paragraph') {
                if (t.align === 'center') {
                    lx = t.x + t.width / 2;
                } else if (t.align === 'right') {
                    lx = t.x + t.width;
                }
            }

            if (t.hasBorder && t.bWidth > 0) {
                ctx.strokeStyle = t.bColor;
                ctx.lineWidth = t.bWidth;
                ctx.strokeText(lines[i], lx, ly);
            }
            ctx.fillStyle = t.color;
            ctx.fillText(lines[i], lx, ly);
        }

        ctx.restore();
    });

    if (!hasPaid) {
        ctx.save();
        ctx.font = "bold 48px Poppins";
        ctx.fillStyle = "rgba(255, 255, 255, 0.35)";
        ctx.textAlign = "center";
        ctx.textBaseline = "middle";
        ctx.translate(canvas.width / 2, canvas.height / 2);
        ctx.rotate(-Math.PI / 4);
        for(let i = -2; i <= 2; i++) {
            for(let j = -2; j <= 2; j++) {
                ctx.fillText("PREVIEW", i * 200, j * 150);
            }
        }
        ctx.restore();
    }
}

function descargarTarjeta() {
    if (!isLoggedIn) { mostrarModalLogin(); return; }
    if (!hasPaid)    { mostrarModalPago();  return; }

    const link = document.createElement('a');
    link.download = `mi-tarjeta-${disenoId}.png`;
    link.href = canvas.toDataURL("image/png");
    link.click();
}

function mostrarModalLogin() {
    document.getElementById('modalLogin').style.display = 'flex';
}

function mostrarModalPago() {
    document.getElementById('modalPago').style.display = 'flex';
    renderizarBotonesPayPal();
}

function renderizarBotonesPayPal() {
    const container = document.getElementById('paypal-button-container');
    container.innerHTML = '';

    if (typeof paypal === 'undefined') {
        container.innerHTML = `<p style="color:#f44;font-size:0.85rem;">${i18n.t('error_paypal_sdk')}</p>`;
        return;
    }

    paypal.Buttons({
        style: {
            layout: 'vertical',
            color:  'gold',
            shape:  'pill',
            label:  'pay'
        },

        createOrder: function(data, actions) {
            return actions.order.create({
                purchase_units: [{
                    description: `CardStudio - Invitación #${disenoId}`,
                    amount: {
                        currency_code: 'USD',
                        value: '1.00'
                    }
                }]
            });
        },

        onApprove: async function(data, actions) {
            container.innerHTML = `<p style="color:#a1a1aa; font-size:0.9rem;">${i18n.t('verifying_payment')}</p>`;
            try {
                const res = await fetch('api_pagos.php?action=capture', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        orderID:   data.orderID,
                        diseno_id: disenoId
                    })
                });
                const result = await res.json();

                if (result.success) {
                    hasPaid = true;
                    document.getElementById('modalPago').style.display = 'none';
                    draw();
                    setTimeout(descargarTarjeta, 300);
                } else {
                    container.innerHTML = `<p style="color:#f44;">${i18n.t('error_prefix')} ${result.error}</p>`;
                }
            } catch(e) {
                container.innerHTML = `<p style="color:#f44;">${i18n.t('error_connection')}</p>`;
            }
        },

        onError: function(err) {
            console.error('PayPal Error:', err);
            container.innerHTML = `<p style="color:#f44;">${i18n.t('error_paypal_generic')}</p>`;
        },

        onCancel: function() {}
    }).render('#paypal-button-container');
}

async function login() {
    let email = document.getElementById('loginEmail').value;
    let pwd   = document.getElementById('loginPassword').value;
    if(!email || !pwd) return alert(i18n.t("alert_login_required"));

    let res = await fetch('api_auth.php?action=login', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({email: email, password: pwd})
    });
    let data = await res.json();

    if(data.success) {
        document.getElementById('modalLogin').style.display = 'none';
        await checkAuthStatus();
        if(hasPaid) { draw(); descargarTarjeta(); } else { mostrarModalPago(); }
    } else {
        if(data.error === 'Usuario no encontrado') {
            let regRes = await fetch('api_auth.php?action=register', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({email: email, password: pwd})
            });
            let regData = await regRes.json();
            if(regData.success) {
                document.getElementById('modalLogin').style.display = 'none';
                await checkAuthStatus();
                mostrarModalPago();
            } else {
                alert(regData.error);
            }
        } else {
            alert(data.error);
        }
    }
}

window.onload = init;
