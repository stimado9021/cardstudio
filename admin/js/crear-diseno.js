const canvas = document.getElementById('tarjetaCanvas');
const ctx = canvas.getContext('2d');
let fondoImg = new Image();
let textos = []; 
let seleccionadoIdx = null;
let isDragging = false;
let offset = { x: 0, y: 0 }; // Para un arrastre suave

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
    
    añadirTexto();
};

function añadirTexto() {
    const nuevo = {
        contenido: "Nuevo Texto",
        x: 100, y: 100 + (textos.length * 40),
        size: 50,
        family: "Arial",
        color: "#ff4d29",
        angle: 0,
        bColor: "#ffffff",
        bWidth: 2,
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
    document.getElementById('borderColor').value = t.bColor;
    document.getElementById('borderWidth').value = t.bWidth;
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
    t.bColor = document.getElementById('borderColor').value;
    t.bWidth = parseFloat(document.getElementById('borderWidth').value);
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

        ctx.shadowColor = t.sColor;
        ctx.shadowBlur = t.sBlur;
        ctx.shadowOffsetX = t.sOffsetX;
        ctx.shadowOffsetY = t.sOffsetY;

        ctx.textAlign = "left"; ctx.textBaseline = "top";

        if (t.bWidth > 0) {
            ctx.strokeStyle = t.bColor;
            ctx.lineWidth = t.bWidth;
            ctx.strokeText(t.contenido, t.x, t.y);
        }

        ctx.fillStyle = t.color;
        ctx.fillText(t.contenido, t.x, t.y);
        ctx.restore();
    });
}

// Eventos de Mouse con mejora de precisión
canvas.onmousedown = (e) => {
    const rect = canvas.getBoundingClientRect();
    const mx = e.clientX - rect.left;
    const my = e.clientY - rect.top;

    for (let i = textos.length - 1; i >= 0; i--) {
        const t = textos[i];
        if (mx >= t.x && mx <= t.x + t.width && my >= t.y && my <= t.y + t.height) {
            seleccionadoIdx = i;
            isDragging = true;
            offset.x = mx - t.x;
            offset.y = my - t.y;
            actualizarPanelControl();
            draw();
            return;
        }
    }
    seleccionadoIdx = null;
    draw();
};

canvas.onmousemove = (e) => {
    if (!isDragging || seleccionadoIdx === null) return;
    const rect = canvas.getBoundingClientRect();
    const mx = e.clientX - rect.left;
    const my = e.clientY - rect.top;
    
    const t = textos[seleccionadoIdx];
    t.x = mx - offset.x;
    t.y = my - offset.y;
    draw();
};

canvas.onmouseup = () => isDragging = false;

document.querySelectorAll('.master-control').forEach(el => {
    el.addEventListener('input', capturarCambios);
});

document.getElementById('bgInput').onchange = (e) => {
    const reader = new FileReader();
    reader.onload = (ev) => { fondoImg.src = ev.target.result; fondoImg.onload = draw; };
    reader.readAsDataURL(e.target.files[0]);
};

// --- GUARDADO CON GENERACIÓN DE JPG ---
document.getElementById('btnGuardar').onclick = async () => {
    const nombre = document.getElementById('nombreDiseno').value;
    if(!nombre) { alert("Por favor, asigna un nombre al diseño."); return; }

    // 1. Limpiamos selección para que la miniatura salga limpia
    seleccionadoIdx = null;
    draw();

    // 2. Generamos el JPG del Canvas
    const miniaturaBase64 = canvas.toDataURL('image/jpeg', 0.8);

    const formData = new FormData();
    formData.append('nombre_diseno', nombre);
    formData.append('id_categoria', document.getElementById('categoriaSelect').value);
    formData.append('imagen_fondo', document.getElementById('bgInput').files[0]);
    formData.append('config_json', JSON.stringify(textos));
    formData.append('miniatura_base64', miniaturaBase64); // Enviamos el JPG

    const res = await fetch('api.php?action=save_design', 
    { 
        method: 'POST',
        body: formData 
    });
    const data = await res.json();
    
    if (data.success) {
        alert("¡Diseño y JPG de vista previa guardados con éxito!");
        limpiarEditor();
    } else {
        alert("Error al guardar: " + data.message);
    }
};

function limpiarEditor() {
    textos = [];
    seleccionadoIdx = null;
    fondoImg = new Image();
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    document.getElementById('nombreDiseno').value = "";
    document.getElementById('bgInput').value = "";
    draw();
}

function logout() {
    localStorage.removeItem('user_session');
    window.location.href = 'login.html';
}