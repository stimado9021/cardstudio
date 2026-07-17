const canvas = document.getElementById('tarjetaCanvas');
const ctx = canvas.getContext('2d');
let fondoImg = new Image();
let textos = []; 
let seleccionadoIdx = null;
let isDragging = false;
let offset = { x: 0, y: 0 };
let idDisenoActual = 0;
let isBold = false;
let isItalic = false;

function adaptCanvasToImage(img) {
    const MAX_DIM = 700, MIN_DIM = 400;
    const w = img.naturalWidth || img.width;
    const h = img.naturalHeight || img.height;
    if (!w || !h) return;
    const ratio = w / h;
    if (ratio >= 1) {
        canvas.width = MAX_DIM;
        canvas.height = Math.round(MAX_DIM / ratio);
        if (canvas.height < MIN_DIM) { canvas.height = MIN_DIM; canvas.width = Math.round(MIN_DIM * ratio); }
    } else {
        canvas.height = MAX_DIM;
        canvas.width = Math.round(MAX_DIM * ratio);
        if (canvas.width < MIN_DIM) { canvas.width = MIN_DIM; canvas.height = Math.round(MIN_DIM / ratio); }
    }
}

function draw() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    if (fondoImg.src) ctx.drawImage(fondoImg, 0, 0, canvas.width, canvas.height);

    textos.forEach((t, index) => {
        ctx.save();
        const weight = t.bold ? 'bold ' : '';
        const style = t.italic ? 'italic ' : '';
        ctx.font = `${style}${weight}${t.size}px ${t.family}`;
        
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

        const cx = t.x + t.width / 2;
        const cy = t.y + t.height / 2;

        ctx.translate(cx, cy);
        ctx.rotate(t.angle);
        ctx.translate(-cx, -cy);

        if (index === seleccionadoIdx) {
            ctx.strokeStyle = "#2E90E5";
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

        ctx.textAlign = t.align || "left"; 
        ctx.textBaseline = "top";

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
}

function getCanvasPos(e) {
    const rect = canvas.getBoundingClientRect();
    const scaleX = canvas.width  / rect.width;
    const scaleY = canvas.height / rect.height;
    const clientX = e.touches ? e.touches[0].clientX : e.clientX;
    const clientY = e.touches ? e.touches[0].clientY : e.clientY;
    return {
        x: (clientX - rect.left) * scaleX,
        y: (clientY - rect.top)  * scaleY
    };
}

function hitTest(t, px, py) {
    const PAD = 10;
    const w = (t.width  || 10) + PAD * 2;
    const h = (t.height || t.size || 20) + PAD * 2;
    const cx = t.x + (t.width  || 0) / 2;
    const cy = t.y + (t.height || t.size || 0) / 2;
    const angle = -(t.angle || 0);
    const dx = px - cx;
    const dy = py - cy;
    const rx = dx * Math.cos(angle) - dy * Math.sin(angle);
    const ry = dx * Math.sin(angle) + dy * Math.cos(angle);
    return Math.abs(rx) <= w / 2 && Math.abs(ry) <= h / 2;
}

// ─── PANEL CONTROL ────────────────────────────────────────────────────
function actualizarPanelControl() {
    console.log('[EDITOR] actualizarPanelControl called, seleccionadoIdx:', seleccionadoIdx);
    if (seleccionadoIdx === null) return;
    const t = textos[seleccionadoIdx];
    if (!t) { console.log('[EDITOR] text object not found at index', seleccionadoIdx); return; }

    console.log('[EDITOR] Setting textarea value to:', t.contenido);
    const textarea = document.getElementById('textEditInput');
    console.log('[EDITOR] textarea element:', textarea);
    if (textarea) {
        textarea.value = t.contenido || '';
        console.log('[EDITOR] textarea.value set to:', textarea.value);
    } else {
        console.error('[EDITOR] textEditInput NOT FOUND in DOM');
    }

    const ff = document.getElementById('fontFamily');
    if (ff) ff.value = t.family;

    const fs = document.getElementById('fontSize');
    if (fs) fs.value = t.size;

    const fc = document.getElementById('fontColor');
    if (fc) fc.value = t.color;

    const ai = document.getElementById('anguloInput');
    if (ai) ai.value = t.angle;

    const cb = document.getElementById('checkBorder');
    if (cb) cb.checked = t.hasBorder || false;

    const bc = document.getElementById('borderColor');
    if (bc) bc.value = t.bColor;

    const bw = document.getElementById('borderWidth');
    if (bw) bw.value = t.bWidth;

    const cs = document.getElementById('checkShadow');
    if (cs) cs.checked = t.hasShadow || false;

    const sc = document.getElementById('shadowColor');
    if (sc) sc.value = t.sColor;

    const sb = document.getElementById('shadowBlur');
    if (sb) sb.value = t.sBlur;

    const sx = document.getElementById('shadowX');
    if (sx) sx.value = t.sOffsetX;

    const sy = document.getElementById('shadowY');
    if (sy) sy.value = t.sOffsetY;

    const bcDiv = document.getElementById('borderControls');
    if (bcDiv) bcDiv.style.display = t.hasBorder ? 'block' : 'none';

    const scDiv = document.getElementById('shadowControls');
    if (scDiv) scDiv.style.display = t.hasShadow ? 'block' : 'none';

    isBold = t.bold || false;
    isItalic = t.italic || false;

    const btnB = document.getElementById('btnBold');
    if (btnB) btnB.classList.toggle('active', isBold);

    const btnI = document.getElementById('btnItalic');
    if (btnI) btnI.classList.toggle('active', isItalic);

    const swatch = document.getElementById('colorSwatch');
    if (swatch) swatch.style.background = t.color;
    
    document.querySelectorAll('.toolbar-btn-align').forEach(b => b.classList.remove('active'));
    const alignKey = (t.align || 'left');
    const alignBtn = document.getElementById('btnAlign' + alignKey.charAt(0).toUpperCase() + alignKey.slice(1));
    if (alignBtn) alignBtn.classList.add('active');
    
    const groupMaxWidth = document.getElementById('groupMaxWidth');
    if (groupMaxWidth) {
        if (t.type === 'paragraph') {
            groupMaxWidth.style.display = 'block';
            const mw = document.getElementById('maxWidthInput');
            if (mw) mw.value = t.maxWidth;
            const ta = document.getElementById('textAlign');
            if (ta) ta.value = t.align || 'left';
        } else {
            groupMaxWidth.style.display = 'none';
        }
    }
}

// ─── CAPTURAR CAMBIOS ─────────────────────────────────────────────────
function capturarCambios() {
    if (seleccionadoIdx === null) return;
    const t = textos[seleccionadoIdx];
    if (!t) return;

    const textarea = document.getElementById('textEditInput');
    if (textarea) t.contenido = textarea.value;

    const ff = document.getElementById('fontFamily');
    if (ff) t.family = ff.value;

    const fs = document.getElementById('fontSize');
    if (fs) t.size = parseInt(fs.value) || 50;

    const fc = document.getElementById('fontColor');
    if (fc) t.color = fc.value;

    const ai = document.getElementById('anguloInput');
    if (ai) t.angle = parseFloat(ai.value);

    const cb = document.getElementById('checkBorder');
    if (cb) t.hasBorder = cb.checked;

    const bc = document.getElementById('borderColor');
    if (bc) t.bColor = bc.value;

    const bw = document.getElementById('borderWidth');
    if (bw) t.bWidth = parseFloat(bw.value);

    const cs = document.getElementById('checkShadow');
    if (cs) t.hasShadow = cs.checked;

    const sc = document.getElementById('shadowColor');
    if (sc) t.sColor = sc.value;

    const sb = document.getElementById('shadowBlur');
    if (sb) t.sBlur = parseInt(sb.value);

    const sx = document.getElementById('shadowX');
    if (sx) t.sOffsetX = parseInt(sx.value);

    const sy = document.getElementById('shadowY');
    if (sy) t.sOffsetY = parseInt(sy.value);
    
    if (t.type === 'paragraph') {
        const mw = document.getElementById('maxWidthInput');
        if (mw) t.maxWidth = parseInt(mw.value);
        const ta = document.getElementById('textAlign');
        if (ta) t.align = ta.value;
    }
    
    draw();
    if (typeof refreshLayerPanel === 'function') refreshLayerPanel();
}

// ─── AÑADIR / ELIMINAR ────────────────────────────────────────────────
function añadirTexto() {
    console.log('[EDITOR] añadirTexto called');
    const contenido = i18n.t("label_text") + " " + (textos.length + 1);
    console.log('[EDITOR] contenido:', contenido);
    const nuevo = {
        contenido: contenido,
        x: 100, y: 100 + (textos.length * 40),
        size: 50,
        family: "Arial",
        color: "#2E90E5",
        bold: true,
        italic: false,
        angle: 0,
        hasBorder: false,
        bColor: "#ffffff",
        bWidth: 2,
        hasShadow: false,
        sBlur: 10,
        sColor: "#000000",
        sOffsetX: 5,
        sOffsetY: 5,
        width: 0, height: 0,
        type: 'normal'
    };
    textos.push(nuevo);
    seleccionadoIdx = textos.length - 1;
    actualizarPanelControl();
    draw();
    if (typeof refreshLayerPanel === 'function') refreshLayerPanel();
}

function añadirParrafo() {
    const nuevo = {
        contenido: "Escribe tu dedicatoria o\npárrafo aquí...",
        x: 100, y: 100 + (textos.length * 40),
        size: 30,
        family: "Arial",
        color: "#2E90E5",
        bold: false,
        italic: false,
        angle: 0,
        hasBorder: false,
        bColor: "#ffffff",
        bWidth: 2,
        hasShadow: false,
        sBlur: 10,
        sColor: "#000000",
        sOffsetX: 5,
        sOffsetY: 5,
        width: 0, height: 0,
        type: 'paragraph',
        maxWidth: 300,
        align: 'center'
    };
    textos.push(nuevo);
    seleccionadoIdx = textos.length - 1;
    actualizarPanelControl();
    draw();
    if (typeof refreshLayerPanel === 'function') refreshLayerPanel();
}

function eliminarCapa() {
    if (seleccionadoIdx === null) return;
    textos.splice(seleccionadoIdx, 1);
    seleccionadoIdx = null;
    draw();
    const textarea = document.getElementById('textEditInput');
    if (textarea) textarea.value = '';
    const groupMaxWidth = document.getElementById('groupMaxWidth');
    if (groupMaxWidth) groupMaxWidth.style.display = 'none';
    if (typeof refreshLayerPanel === 'function') refreshLayerPanel();
}

// ─── MOUSE EVENTS ─────────────────────────────────────────────────────
canvas.addEventListener('mousedown', (e) => {
    e.preventDefault();
    const { x: mx, y: my } = getCanvasPos(e);
    console.log('[EDITOR] mousedown at canvas coords:', mx, my, 'texts:', textos.length);
    let encontrado = false;
    for (let i = textos.length - 1; i >= 0; i--) {
        const ht = hitTest(textos[i], mx, my);
        console.log('[EDITOR] hitTest[' + i + ']:', ht, 'w:' + textos[i].width, 'h:' + textos[i].height, 'x:' + textos[i].x, 'y:' + textos[i].y);
        if (ht) {
            seleccionadoIdx = i;
            isDragging = true;
            offset.x = mx - textos[i].x;
            offset.y = my - textos[i].y;
            canvas.style.cursor = 'grabbing';
            actualizarPanelControl();
            draw();
            if (typeof refreshLayerPanel === 'function') refreshLayerPanel();
            encontrado = true;
            break;
        }
    }
    if (!encontrado) {
        console.log('[EDITOR] No text hit — deselecting');
        seleccionadoIdx = null;
        const textarea = document.getElementById('textEditInput');
        if (textarea) textarea.value = '';
        draw();
        if (typeof refreshLayerPanel === 'function') refreshLayerPanel();
    }
});

canvas.addEventListener('mousemove', (e) => {
    e.preventDefault();
    if (!isDragging || seleccionadoIdx === null) {
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

// ─── TOUCH EVENTS ─────────────────────────────────────────────────────
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
            if (typeof refreshLayerPanel === 'function') refreshLayerPanel();
            encontrado = true;
            break;
        }
    }
    if (!encontrado) {
        seleccionadoIdx = null;
        draw();
        if (typeof refreshLayerPanel === 'function') refreshLayerPanel();
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

// ─── TOOLBAR EVENT LISTENERS ──────────────────────────────────────────
function initToolbarListeners() {
    console.log('[EDITOR] initToolbarListeners called');
    // Hidden textAlign select for paragraph alignment
    if (!document.getElementById('textAlign')) {
        const sel = document.createElement('select');
        sel.id = 'textAlign';
        sel.style.display = 'none';
        sel.innerHTML = '<option value="left">Izq</option><option value="center">Centro</option><option value="right">Der</option>';
        document.body.appendChild(sel);
    }

    // Textarea
    const textarea = document.getElementById('textEditInput');
    console.log('[EDITOR] textarea found in initToolbar:', !!textarea);
    if (textarea) {
        textarea.addEventListener('input', function() {
            if (seleccionadoIdx !== null && textos[seleccionadoIdx]) {
                textos[seleccionadoIdx].contenido = this.value;
                draw();
                if (typeof refreshLayerPanel === 'function') refreshLayerPanel();
            }
        });
    }

    // Font select
    const ff = document.getElementById('fontFamily');
    if (ff) ff.addEventListener('change', capturarCambios);

    // Font size
    const fs = document.getElementById('fontSize');
    if (fs) fs.addEventListener('input', capturarCambios);

    // Font color
    const fc = document.getElementById('fontColor');
    if (fc) {
        fc.addEventListener('input', function() {
            const swatch = document.getElementById('colorSwatch');
            if (swatch) swatch.style.background = this.value;
            capturarCambios();
        });
    }

    // Angle
    const ai = document.getElementById('anguloInput');
    if (ai) ai.addEventListener('input', capturarCambios);

    // Border toggle
    const cb = document.getElementById('checkBorder');
    if (cb) cb.addEventListener('change', function() {
        const bc = document.getElementById('borderControls');
        if (bc) bc.style.display = this.checked ? 'block' : 'none';
        capturarCambios();
    });

    // Border color + width
    const bcol = document.getElementById('borderColor');
    if (bcol) bcol.addEventListener('input', capturarCambios);
    const bwi = document.getElementById('borderWidth');
    if (bwi) bwi.addEventListener('input', capturarCambios);

    // Shadow toggle
    const cs = document.getElementById('checkShadow');
    if (cs) cs.addEventListener('change', function() {
        const sc = document.getElementById('shadowControls');
        if (sc) sc.style.display = this.checked ? 'block' : 'none';
        capturarCambios();
    });

    // Shadow color, blur, x, y
    const sCol = document.getElementById('shadowColor');
    if (sCol) sCol.addEventListener('input', capturarCambios);
    const sBlur = document.getElementById('shadowBlur');
    if (sBlur) sBlur.addEventListener('input', capturarCambios);
    const sX = document.getElementById('shadowX');
    if (sX) sX.addEventListener('input', capturarCambios);
    const sY = document.getElementById('shadowY');
    if (sY) sY.addEventListener('input', capturarCambios);

    // Max width
    const mw = document.getElementById('maxWidthInput');
    if (mw) mw.addEventListener('input', capturarCambios);
}

// ─── BOLD / ITALIC ────────────────────────────────────────────────────
function toggleBold() {
    if (seleccionadoIdx === null) return;
    textos[seleccionadoIdx].bold = !textos[seleccionadoIdx].bold;
    isBold = textos[seleccionadoIdx].bold;
    document.getElementById('btnBold').classList.toggle('active', isBold);
    draw();
}

function toggleItalic() {
    if (seleccionadoIdx === null) return;
    textos[seleccionadoIdx].italic = !textos[seleccionadoIdx].italic;
    isItalic = textos[seleccionadoIdx].italic;
    document.getElementById('btnItalic').classList.toggle('active', isItalic);
    draw();
}

// ─── ALIGNMENT ────────────────────────────────────────────────────────
function setAlign(align) {
    if (seleccionadoIdx !== null && textos[seleccionadoIdx]) {
        textos[seleccionadoIdx].align = align;
        draw();
    }
    document.querySelectorAll('.toolbar-btn-align').forEach(b => b.classList.remove('active'));
    const btn = document.getElementById('btnAlign' + align.charAt(0).toUpperCase() + align.slice(1));
    if (btn) btn.classList.add('active');
}

// ─── FONT SIZE +/- ────────────────────────────────────────────────────
function changeFontSize(delta) {
    const input = document.getElementById('fontSize');
    let val = parseInt(input.value) || 50;
    val = Math.max(10, Math.min(250, val + delta));
    input.value = val;
    capturarCambios();
}

// ─── BACKGROUND IMAGE ─────────────────────────────────────────────────
document.getElementById('bgInput').onchange = (e) => {
    const reader = new FileReader();
    reader.onload = (ev) => {
        fondoImg.src = ev.target.result;
        fondoImg.onload = () => { adaptCanvasToImage(fondoImg); draw(); };
    };
    reader.readAsDataURL(e.target.files[0]);
};

// ─── SAVE ─────────────────────────────────────────────────────────────
document.getElementById('btnGuardar').onclick = async () => {
    const nombre = document.getElementById('nombreDiseno').value;
    if(!nombre) { alert(i18n.t("admin_design_name")); return; }

    seleccionadoIdx = null;
    draw();

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
    formData.append('miniatura_base64', miniaturaBase64);

    const res = await fetch('api.php?action=save_design', 
    { 
        method: 'POST',
        body: formData 
    });
    const data = await res.json();
    
    if (data.success) {
        window.location.href = 'dashboard.php';
    } else {
        alert(i18n.t("error_saving") + " " + data.message);
    }
};

// ─── RESET ────────────────────────────────────────────────────────────
function limpiarEditor() {
    textos = [];
    seleccionadoIdx = null;
    idDisenoActual = 0;
    fondoImg = new Image();
    canvas.width = 500;
    canvas.height = 700;
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    document.getElementById('nombreDiseno').value = "";
    document.getElementById('bgInput').value = "";
    const textarea = document.getElementById('textEditInput');
    if (textarea) textarea.value = '';
    if (typeof setOrientation === 'function') setOrientation('vertical');
    draw();
    window.history.replaceState({}, document.title, window.location.pathname);
}

function logout() {
    fetch('api_admin_auth.php?action=logout').then(() => {
        window.location.href = 'login.php';
    });
}

// ─── INIT ─────────────────────────────────────────────────────────────
window.onload = async () => {
    console.log('[EDITOR] window.onload fired');
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

    initToolbarListeners();

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
                fondoImg.onload = async function() {
                    adaptCanvasToImage(fondoImg);
                    await document.fonts.ready;
                    draw();
                    if (typeof refreshLayerPanel === 'function') refreshLayerPanel();
                };
            } else {
                alert(i18n.t("design_not_found"));
                añadirTexto();
            }
        } catch (e) { console.error(e); añadirTexto(); }
    } else {
        añadirTexto();
    }
};
