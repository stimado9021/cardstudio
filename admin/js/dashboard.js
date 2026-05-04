document.addEventListener('DOMContentLoaded', async () => {
    const grid = document.getElementById('designsGrid');

    try {
        const response = await fetch('api.php?action=get_disenos');
        const data = await response.json();

        if (data.length === 0) {
            grid.innerHTML = '<div class="no-designs">Aún no tienes diseños creados. Ve al Editor Maestro para empezar.</div>';
            return;
        }

        data.forEach(diseno => {
            const card = document.createElement('div');
            card.className = 'card';
            
            // Si la miniatura no existe, mostramos un color por defecto
            const imgSrc = diseno.miniatura_url ? diseno.miniatura_url : 'https://via.placeholder.com/250x350?text=Sin+Vista+Previa';

            card.innerHTML = `
                <div class="card-img-wrapper">
                    <img src="${imgSrc}" class="card-img" alt="${diseno.nombre_diseno}">
                </div>
                <div class="card-body">
                    <h3 class="card-title">${diseno.nombre_diseno}</h3>
                    <p class="card-category">Categoría: ${diseno.categoria_nombre}</p>
                    <a href="crear-diseno.html?id=${diseno.id_diseno}" class="btn-edit">Editar Diseño</a>
                </div>
            `;
            grid.appendChild(card);
        });

    } catch (error) {
        console.error("Error al cargar los diseños:", error);
        grid.innerHTML = '<div class="no-designs">Hubo un error al cargar los diseños.</div>';
    }
});
