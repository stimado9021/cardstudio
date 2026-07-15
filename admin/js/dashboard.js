let todosLosDisenos = [];

document.addEventListener('DOMContentLoaded', async () => {
    const grid = document.getElementById('designsGrid');

    try {
        const [disenosRes, catsRes] = await Promise.all([
            fetch('api.php?action=get_disenos'),
            fetch('api.php?action=get_categorias')
        ]);

        if (disenosRes.status === 401 || catsRes.status === 401) {
            window.location.href = 'login.php';
            return;
        }

        const disenos = await disenosRes.json();
        const categorias = await catsRes.json();

        if (!Array.isArray(disenos) || !Array.isArray(categorias)) {
            grid.innerHTML = `<div class="no-designs">${i18n.t('dashboard_error_loading')}</div>`;
            return;
        }

        todosLosDisenos = disenos;

        const select = document.getElementById('categoryFilter');
        categorias.forEach(cat => {
            const opt = document.createElement('option');
            opt.value = cat.id;
            opt.textContent = cat.nombre;
            select.appendChild(opt);
        });

        renderDisenos(disenos);
    } catch (error) {
        console.error("Error loading designs:", error);
        grid.innerHTML = `<div class="no-designs">${i18n.t('dashboard_error_loading')}</div>`;
    }
});

function renderDisenos(lista) {
    const grid = document.getElementById('designsGrid');
    grid.innerHTML = "";

    if (lista.length === 0) {
        grid.innerHTML = `<div class="no-designs">${i18n.t('dashboard_empty')}</div>`;
        return;
    }

    lista.forEach(diseno => {
        const imgSrc = diseno.miniatura_url || 'https://via.placeholder.com/250x350?text=Sin+Vista+Previa';

        const card = document.createElement('div');
        card.className = 'card';
        card.innerHTML = `
            <div class="card-img-wrapper">
                <img src="${imgSrc}" class="card-img" alt="${diseno.nombre_diseno}" loading="lazy">
            </div>
            <div class="card-body">
                <h3 class="card-title">${diseno.nombre_diseno}</h3>
                <p class="card-category">${i18n.t('dashboard_category')} ${diseno.categoria_nombre}</p>
                <a href="crear-diseno.html?id=${diseno.id_diseno}" class="btn-edit">${i18n.t('dashboard_edit')}</a>
            </div>
        `;
        grid.appendChild(card);
    });
}

function filtrarPorCategoria() {
    const catId = document.getElementById('categoryFilter').value;
    if (catId === 'all') {
        renderDisenos(todosLosDisenos);
    } else {
        renderDisenos(todosLosDisenos.filter(d => d.id_categoria == catId));
    }
}
