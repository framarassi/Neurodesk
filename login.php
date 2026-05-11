<?php

session_start();

include 'includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email'";

    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {

        $user = mysqli_fetch_assoc($result);

        // Verifica password
        if (password_verify($password, $user['password'])) {

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            header("Location: dashboard.php");
            exit();

        } else {
            echo "Password errata";
        }

    } else {
        echo "Utente non trovato";
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