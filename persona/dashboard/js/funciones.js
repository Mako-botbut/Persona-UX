document.getElementById("login-form").addEventListener("submit", async (e) => {
    e.preventDefault();

    const email = document.getElementById("email").value;
    const password = document.getElementById("password").value;

    const response = await fetch("http://localhost/persona/api/routes/auth.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email, password }),
    });

    const result = await response.json();

    if (result.success) {
        alert("Inicio de sesión exitoso");
        window.location.href = "../html"; // Redirigir al contenido protegido
    } else {
        alert(result.error);
    }
});
