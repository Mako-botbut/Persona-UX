document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const userId = urlParams.get('id');

    if (!userId) {
        console.error('ID de usuario no especificado en la URL.');
        return;
    }

    console.log(`Solicitando datos del usuario con ID: ${userId}`);

    fetch(`http://localhost/persona/api/routes/usuario.php?id=${userId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }
            return response.json();
        })
        .then(user => {
            console.log('Usuario obtenido:', user);

            if (!user || Object.keys(user).length === 0) {
                console.error('No se encontraron datos para el usuario.');
                return;
            }

            document.getElementById('user-name').textContent = user.nombre ? user.nombre : 'Nombre no disponible';

            document.getElementById('user-age').textContent = user.edad || 'No disponible';
            document.getElementById('user-occupation').textContent = user.ocupacion || 'No disponible';
            document.getElementById('user-location').textContent = user.ubicacion || 'No disponible';
            document.getElementById('user-goals').textContent = user.metas || 'No disponible';
            document.getElementById('user-motivations').textContent = user.motivaciones || 'No disponible';
            document.getElementById('user-frustrations').textContent = user.frustraciones || 'No disponible';
            document.getElementById('user-contact').textContent = user.contacto || 'No disponible';
        })
        .catch(error => {
            console.error('Error al cargar los datos del usuario:', error);
        });
});
