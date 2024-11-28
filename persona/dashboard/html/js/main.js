const API_URL = "http://localhost/persona/api/routes/usuario.php";

// Función para obtener y mostrar los usuarios
async function fetchUsers() {
    const response = await fetch(API_URL);
    const users = await response.json();

    const userContainer = document.getElementById("users");
    userContainer.innerHTML = ""; // Limpiar contenido previo

    if (users.length === 0) {
        userContainer.innerHTML = "<p>No hay usuarios disponibles.</p>";
        return;
    }

    users.forEach(user => {
        const userCard = document.createElement("div");
        userCard.className = "user-card";
        userCard.innerHTML = `
            <h3>${user.nombre}</h3>
            <p><strong>ID:</strong> ${user.id}</p>
            <p><strong>Edad:</strong> ${user.edad}</p>
            <p><strong>Ocupación:</strong> ${user.ocupacion}</p>
            <p><strong>Ubicación:</strong> ${user.ubicacion}</p>
            <p><strong>Metas:</strong> ${user.metas}</p>
            <p><strong>Motivaciones:</strong> ${user.motivaciones}</p>
            <p><strong>Frustraciones:</strong> ${user.frustraciones}</p>
            <p><strong>Contacto:</strong> ${user.contacto}</p>
            <p>
                <button class="delete-btn" data-id="${user.id}">Eliminar</button>
                <button class="custom-view-btn" data-id="${user.id}">Ver Diseño</button>
            </p>
        `;
        
        // Agregar evento para redirigir al diseño personalizado
        userCard.querySelector('.custom-view-btn').addEventListener('click', () => {
            window.location.href = `views/custom-design.html?id=${user.id}`;
        });

        userCard.addEventListener('click', () => setupUpdateForm(user)); // Llenar el formulario al hacer clic
        userContainer.appendChild(userCard);
    });
}

// Función para agregar un nuevo usuario
async function addUser(event) {
    event.preventDefault();

    const formData = new FormData(document.getElementById("user-form"));
    const data = Object.fromEntries(formData.entries());

    const response = await fetch(API_URL, {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify(data)
    });

    if (response.ok) {
        alert("Usuario agregado exitosamente.");
        document.getElementById("user-form").reset();
        fetchUsers(); // Actualizar la lista de usuarios
    } else {
        alert("Hubo un error al agregar el usuario.");
    }
}

document.getElementById('updateForm').addEventListener('submit', async (event) => {
    event.preventDefault();

    const id = document.getElementById('updateId').value;
    const nombre = document.getElementById('updateNombre').value;
    const edad = document.getElementById('updateEdad').value;
    const ocupacion = document.getElementById('updateOcupacion').value;
    const ubicacion = document.getElementById('updateUbicacion').value;
    const metas = document.getElementById('updateMetas').value;
    const motivaciones = document.getElementById('updateMotivaciones').value;
    const frustraciones = document.getElementById('updateFrustraciones').value;
    const contacto = document.getElementById('updateContacto').value;

    const updatedData = {
        id: id,
        nombre: nombre,
        edad: edad,
        ocupacion: ocupacion,
        ubicacion: ubicacion,
        metas: metas,
        motivaciones: motivaciones,
        frustraciones: frustraciones,
        contacto: contacto
    };

    try {
        const response = await fetch(`${API_URL}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(updatedData)
        });

        const result = await response.json();
        if (result.success) {
            alert('Usuario actualizado correctamente.');
            fetchUsers();
        } else {
            alert('Error al actualizar el usuario: ' + result.message);
        }
    } catch (error) {
        console.error('Error al actualizar:', error);
        alert('Ocurrió un error al intentar actualizar el usuario.');
    }
});

function setupUpdateForm(user) {
    document.getElementById('updateId').value = user.id;
    document.getElementById('updateNombre').value = user.nombre;
    document.getElementById('updateEdad').value = user.edad;
    document.getElementById('updateOcupacion').value = user.ocupacion;
    document.getElementById('updateUbicacion').value = user.ubicacion;
    document.getElementById('updateMetas').value = user.metas;
    document.getElementById('updateMotivaciones').value = user.motivaciones;
    document.getElementById('updateFrustraciones').value = user.frustraciones;
    document.getElementById('updateContacto').value = user.contacto;
}

// Función para eliminar usuario
const deleteUser = async (id) => {
    try {
        const response = await fetch(`${API_URL}`, {
            method: "DELETE",
            headers: { "Content-Type": "application/json" },  // Cambiar a application/json
            body: JSON.stringify({ id: id }),  // Pasar el ID como JSON
        });

        if (!response.ok) {
            // Manejar errores HTTP
            const errorText = await response.text();
            console.error("Error del servidor:", errorText);
            alert("Error al eliminar el usuario: " + errorText);
            return;
        }

        // Intentar convertir la respuesta a JSON
        const result = await response.json();

        if (result.message) {
            alert(result.message); // Mostrar mensaje de éxito
            fetchUsers(); // Actualiza la lista de usuarios
        } else if (result.error) {
            alert("Error al eliminar el usuario: " + result.error);
        } else {
            alert("Respuesta desconocida del servidor.");
        }
    } catch (error) {
        console.error("Error en la petición:", error);
        alert("Ocurrió un error al intentar eliminar el usuario.");
    }
};

document.addEventListener("click", (event) => {
    if (event.target.classList.contains("delete-btn")) {
        const userId = event.target.getAttribute("data-id");
        if (confirm("¿Estás seguro de que quieres eliminar este usuario?")) {
            deleteUser(userId);
        }
    }
});
// Event Listener para el formulario
document.getElementById("user-form").addEventListener("submit", addUser);

// Obtener usuarios al cargar la página
fetchUsers();