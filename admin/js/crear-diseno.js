const canvas = document.getElementById('tarjetaCanvas');
const ctx = canvas.getContext('2d');
let fondoImg = new Image();
let textos = []; 
let seleccionadoIdx = null;
let isDragging = false;
let offset = { x: 0, y: 0 }; // Para un arrastre suave
let idDisenoActual = 0;

window.onload = async () => {
    // Cargar categorías dinámicas
    try {
        const resp = await fetch('api.php?action=get_categorias');
        const categorias = await resp.json();
        const select = document.getElementById('categoriaSelect');
        categorias.forEach(cat => {
            let opt = document.createElement('option');
            opt.value = cat.id; opt.innerHTML = cat.nombre;
            select.appendChild(opt);
        });
    } catch (e) { console.error("Error al cargar categorías"); }
    
    // Verificar si estamos editando
    const urlParams = new URLSearchParams(window.location.search);
    const id = urlParams.get('id');
   
    if (id) {
        try {
            const resp = await fetch(`api.php?action=get_diseno&id=${id}`);
            const data = await resp.json();
            if (data.success) {
                const diseno = data.diseno;
                idDisenoActual = diseno.id_diseno;
                document.getElementById('nombreDiseno').value = diseno.nombre_diseno;
                document.getElementById('categoriaSelect').value = diseno.id_categoria;
                textos = JSON.parse(diseno.configuracion_textos_json);
                fondoImg.src = diseno.imagen_fondo_url; 
                fondoImg.onload = draw;
            } else {
                alert(i18n.t("design_not_found"));
                añadirTexto();
            }
        } catch (e) { console.error(e); añadirTexto(); }
    } else {
        añadirTexto();
    }
};

function añadirTexto() {
    const nuevo = {
        contenido: i18n.t("label_text") + " " + (textos.length + 1),
        x: 100, y: 100 + (textos.length * 40),
        size: 50,
        family: "Arial",
        color: "#ff4d29",
        angle: 0,
        hasBorder: false,
        bColor: "#ffffff",
        bWidth: 2,
        hasShadow: false,
        sBlur: 10,
        sColor: "#000000",
        sOffsetX: 5,
        sOffsetY: 5,
        width: 0, height: 0
    };
    textos.push(nuevo);
    seleccionadoIdx = textos.length - 1;
    actualizarPanelControl();
    draw();
}

function actualizarPanelControl() {
    if (seleccionadoIdx === null) return;
    const t = textos[seleccionadoIdx];
    
    document.getElementById('textoInput').value = t.contenido;
    document.getElementById('fontFamily').value = t.family;
    document.getElementById('fontSize').value = t.size;
    document.getElementById('fontColor').value = t.color;
    document.getElementById('anguloInput').value = t.angle;
    document.getElementById('checkBorder').checked = t.hasBorder || false;
    document.getElementById('borderColor').value = t.bColor;
    document.getElementById('borderWidth').value = t.bWidth;
    document.getElementById('checkShadow').checked = t.hasShadow || false;
    document.getElementById('shadowColor').value = t.sColor;
    document.getElementById('shadowBlur').value = t.sBlur;
    document.getElementById('shadowX').value = t.sOffsetX;
    document.getElementById('shadowY').value = t.sOffsetY;
}

function capturarCambios() {
    if (seleccionadoIdx === null) return;
    const t = textos[seleccionadoIdx];

    t.contenido = document.getElementById('textoInput').value;
    t.family = document.getElementById('fontFamily').value;
    t.size = parseInt(document.getElementById('fontSize').value);
    t.color = document.getElementById('fontColor').value;
    t.angle = parseFloat(document.getElementById('anguloInput').value);
    t.hasBorder = document.getElementById('checkBorder').checked;
    t.bColor = document.getElementById('borderColor').value;
    t.bWidth = parseFloat(document.getElementById('borderWidth').value);
    t.hasShadow = document.getElementById('checkShadow').checked;
    t.sColor = document.getElementById('shadowColor').value;
    t.sBlur = parseInt(document.getElementById('shadowBlur').value);
    t.sOffsetX = parseInt(document.getElementById('shadowX').value);
    t.sOffsetY = parseInt(document.getElementById('shadowY').value);
    
    draw();
}

function draw() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    if (fondoImg.src) ctx.drawImage(fondoImg, 0, 0, canvas.width, canvas.height);

    textos.forEach((t, index) => {
        ctx.save();
        ctx.font = `bold ${t.size}px ${t.family}`;
        const m = ctx.measureText(t.contenido);
        t.width = m.width; t.height = t.size;

        const cx = t.x + t.width / 2;
        const cy = t.y + t.height / 2;

        ctx.translate(cx, cy);
        ctx.rotate(t.angle);
        ctx.translate(-cx, -cy);

        // Solo dibujamos el cuadro de selección si NO estamos exportando la imagen
        if (index === seleccionadoIdx) {
            ctx.strokeStyle = "#ff4d29";
            ctx.setLineDash([5, 5]);
            ctx.strokeRect(t.x - 5, t.y - 5, t.width + 10, t.height + 10);
            ctx.setLineDash([]);
        }

        if (t.hasShadow) {
            ctx.shadowColor = t.sColor;
            ctx.shadowBlur = t.sBlur;
            ctx.shadowOffsetX = t.sOffsetX;
            ctx.shadowOffsetY = t.sOffsetY;
        } else {
            ctx.shadowColor = 'transparent';
            ctx.shadowBlur = 0;
            ctx.shadowOffsetX = 0;
            ctx.shadowOffsetY = 0;
        }

        ctx.textAlign = "left"; ctx.textBaseline = "top";

        if (t.hasBorder && t.bWidth > 0) {
            ctx.strokeStyle = t.bColor;
            ctx.lineWidth = t.bWidth;
            ctx.strokeText(t.contenido, t.x, t.y);
        }

        ctx.fillStyle = t.color;
        ctx.fillText(t.contenido, t.x, t.y);
        ctx.restore();
    });
}

// =====================================================================
//  INTERACCIÓN CON EL CANVAS — Mouse + Touch con escala correcta
// =====================================================================

/**
 * Convierte coordenadas del cliente (pantalla) a coordenadas del canvas,
 * teniendo en cuenta que el canvas puede estar escalado por CSS.
 */
function getCanvasPos(e) {
    const rect = canvas.getBoundingClientRect();
    // Relación entre tamaño real del canvas y tamaño visual en pantalla
    const scaleX = canvas.width  / rect.width;
    const scaleY = canvas.height / rect.height;

    // Soporte tanto para mouse como para touch
    const clientX = e.touches ? e.touches[0].clientX : e.clientX;
    const clientY = e.touches ? e.touches[0].clientY : e.clientY;

    return {
        x: (clientX - rect.left) * scaleX,
        y: (clientY - rect.top)  * scaleY
    };
}

/**
 * Detecta si el punto (px, py) está dentro del bounding box de un texto,
 * incluyendo rotación y un área de padding para facilitar el clic.
 */
function hitTest(t, px, py) {
    const PAD = 10; // píxeles extra de margen de clic
    const w = (t.width  || 10) + PAD * 2;
    const h = (t.height || t.size || 20) + PAD * 2;

    // Centro del elemento
    const cx = t.x + (t.width  || 0) / 2;
    const cy = t.y + (t.height || t.size || 0) / 2;

    // Rotar el punto del mouse al espacio local del texto
    const angle = -(t.angle || 0);
    const dx = px - cx;
    const dy = py - cy;
    const rx = dx * Math.cos(angle) - dy * Math.sin(angle);
    const ry = dx * Math.sin(angle) + dy * Math.cos(angle);

    return Math.abs(rx) <= w / 2 && Math.abs(ry) <= h / 2;
}

// ─── Mouse Events ────────────────────────────────────────────────────
canvas.addEventListener('mousedown', (e) => {
    e.preventDefault();
    const { x: mx, y: my } = getCanvasPos(e);

    // Buscar de arriba a abajo (último elemento = más al frente)
    let encontrado = false;
    for (let i = textos.length - 1; i >= 0; i--) {
        if (hitTest(textos[i], mx, my)) {
            seleccionadoIdx = i;
            isDragging = true;
            offset.x = mx - textos[i].x;
            offset.y = my - textos[i].y;
            canvas.style.cursor = 'grabbing';
            actualizarPanelControl();
            draw();
            encontrado = true;
            break;
        }
    }
    if (!encontrado) {
        seleccionadoIdx = null;
        draw();
    }
});

canvas.addEventListener('mousemove', (e) => {
    e.preventDefault();
    if (!isDragging || seleccionadoIdx === null) {
        // Cambiar cursor para indicar elementos arrastrables
        const { x: mx, y: my } = getCanvasPos(e);
        const sobreAlgo = textos.some(t => hitTest(t, mx, my));
        canvas.style.cursor = sobreAlgo ? 'grab' : 'default';
        return;
    }
    const { x: mx, y: my } = getCanvasPos(e);
    const t = textos[seleccionadoIdx];
    t.x = mx - offset.x;
    t.y = my - offset.y;
    draw();
});

canvas.addEventListener('mouseup', () => {
    isDragging = false;
    canvas.style.cursor = 'grab';
});

canvas.addEventListener('mouseleave', () => {
    isDragging = false;
    canvas.style.cursor = 'default';
});

// ─── Touch Events (móvil / tablet) ───────────────────────────────────
canvas.addEventListener('touchstart', (e) => {
    e.preventDefault();
    const { x: mx, y: my } = getCanvasPos(e);

    let encontrado = false;
    for (let i = textos.length - 1; i >= 0; i--) {
        if (hitTest(textos[i], mx, my)) {
            seleccionadoIdx = i;
            isDragging = true;
            offset.x = mx - textos[i].x;
            offset.y = my - textos[i].y;
            actualizarPanelControl();
            draw();
            encontrado = true;
            break;
        }
    }
    if (!encontrado) {
        seleccionadoIdx = null;
        draw();
    }
}, { passive: false });

canvas.addEventListener('touchmove', (e) => {
    e.preventDefault();
    if (!isDragging || seleccionadoIdx === null) return;
    const { x: mx, y: my } = getCanvasPos(e);
    const t = textos[seleccionadoIdx];
    t.x = mx - offset.x;
    t.y = my - offset.y;
    draw();
}, { passive: false });

canvas.addEventListener('touchend', () => {
    isDragging = false;
});


document.querySelectorAll('.master-control').forEach(el => {
    el.addEventListener('input', capturarCambios);
    el.addEventListener('change', capturarCambios);
});

document.getElementById('bgInput').onchange = (e) => {
    const reader = new FileReader();
    reader.onload = (ev) => { fondoImg.src = ev.target.result; fondoImg.onload = draw; };
    reader.readAsDataURL(e.target.files[0]);
};

// --- GUARDADO CON GENERACIÓN DE JPG ---
document.getElementById('btnGuardar').onclick = async () => {
    const nombre = document.getElementById('nombreDiseno').value;
    if(!nombre) { alert(i18n.t("admin_design_name")); return; }

    // 1. Limpiamos selección para que la miniatura salga limpia
    seleccionadoIdx = null;
    draw();

    // 2. Generamos el JPG del Canvas
    const miniaturaBase64 = canvas.toDataURL('image/jpeg', 0.8);

    const formData = new FormData();
    if (idDisenoActual > 0) {
        formData.append('id_diseno', idDisenoActual);
    }
    formData.append('nombre_diseno', nombre);
    formData.append('id_categoria', document.getElementById('categoriaSelect').value);
    
    const fileInput = document.getElementById('bgInput');
    if (fileInput.files.length > 0) {
        formData.append('imagen_fondo', fileInput.files[0]);
    }
    
    formData.append('config_json', JSON.stringify(textos));
    formData.append('miniatura_base64', miniaturaBase64); // Enviamos el JPG

    const res = await fetch('api.php?action=save_design', 
    { 
        method: 'POST',
        body: formData 
    });
    const data = await res.json();
    
    if (data.success) {
        alert(i18n.t("success_saving"));
        limpiarEditor();
    } else {
        alert(i18n.t("error_saving") + " " + data.message);
    }
};

function limpiarEditor() {
    textos = [];
    seleccionadoIdx = null;
    idDisenoActual = 0;
    fondoImg = new Image();
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    document.getElementById('nombreDiseno').value = "";
    document.getElementById('bgInput').value = "";
    draw();
    // Limpiar URL si venimos de editar
    window.history.replaceState({}, document.title, window.location.pathname);
}

function logout() {
    localStorage.removeItem('user_session');
    window.location.href = '../index.html';
}