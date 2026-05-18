<?php

include 'includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Cripta password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Query SQL
    $sql = "INSERT INTO users (username, email, password)
            VALUES ('$username', '$email', '$hashedPassword')";

    if (mysqli_query($conn, $sql)) {
        echo "Registrazione completata!";
    } else {
        echo "Errore: " . mysqli_error($conn);
    }
}

?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Login - NeuroDesk</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-zinc-950 text-white flex items-center justify-center h-screen">

<div class="bg-zinc-900 p-10 rounded-2xl w-[400px] shadow-2xl">

    <h1 class="text-4xl font-bold mb-2">NeuroDesk</h1>

    <p class="text-zinc-400 mb-8">
        Accedi al tuo workspace
    </p>

    <form method="POST">
        
        <input
        type="text"
        name="username"
        placeholder="Username"
        required
        class="w-full p-3 rounded-xl bg-zinc-800 border border-zinc-700 mb-2"
        >
        <input
            type="email"
            name="email"
            placeholder="Email"
            required
            class="w-full p-3 rounded-xl bg-zinc-800 border border-zinc-700 mb-4"
        >

        <input
            type="password"
            name="password"
            placeholder="Password"
            required
            class="w-full p-3 rounded-xl bg-zinc-800 border border-zinc-700 mb-6"
        >

        <button
            type="submit"
            class="w-full bg-indigo-600 hover:bg-indigo-500 transition p-3 rounded-xl font-semibold"
        >
            Accedi
        </button>

    </form>

</div>

</body>
</html>