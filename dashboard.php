
<?php

session_start();

include 'includes/db.php';


$user_id = $_SESSION['user_id'];

$completedSql = "SELECT COUNT(*) AS total
                 FROM tasks
                 WHERE user_id='$user_id'
                 AND status='done'";

$completedResult = mysqli_query($conn, $completedSql);
$completedData = mysqli_fetch_assoc($completedResult);

$tasksCompleted = $completedData['total'];

$hoursSql = "SELECT SUM(duration) AS total
             FROM tasks
             WHERE user_id='$user_id'
             AND status='done'";

$hoursResult = mysqli_query($conn, $hoursSql);
$hoursData = mysqli_fetch_assoc($hoursResult);

$hoursStudied = $hoursData['total'] / 60;

if ($tasksCompleted == 0) {
    $focus = 0;
} else {
    $focus = min(100, ($tasksCompleted * 10));
}

if (isset($_POST['add_task'])) {

    $task = $_POST['task_title'];

    $sql = "INSERT INTO tasks (user_id, title)
            VALUES ('$user_id', '$task')";

    mysqli_query($conn, $sql);
}
if (isset($_POST['delete_task'])) {

    $task_id = $_POST['task_id'];

    $sql = "DELETE FROM tasks
            WHERE id='$task_id'
            AND user_id='$user_id'";

    mysqli_query($conn, $sql);
}
if (isset($_POST['update_status'])) {

    $task_id = $_POST['task_id'];
    $new_status = $_POST['new_status'];

    $sql = "UPDATE tasks
            SET status='$new_status'
            WHERE id='$task_id'
            AND user_id='$user_id'";

    mysqli_query($conn, $sql);

    // SE COMPLETATO → aggiungi XP
    if ($new_status == "done") {

        $xpSql = "UPDATE users
                  SET xp = xp + 10
                  WHERE id='$user_id'";

        mysqli_query($conn, $xpSql);
    }
}
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

?>
<?php

$userSql = "SELECT xp FROM users WHERE id='$user_id'";
$userResult = mysqli_query($conn, $userSql);
$userData = mysqli_fetch_assoc($userResult);

$xp = $userData['xp'];

?>
<?php

$level = 1;

if ($xp >= 50) $level = 2;
if ($xp >= 100) $level = 3;
if ($xp >= 200) $level = 4;
if ($xp >= 400) $level = 5;

?>

<?php

$todoCount = 0;
$doingCount = 0;
$doneCount = 0;

$sql = "SELECT status, COUNT(*) as total
        FROM tasks
        WHERE user_id='$user_id'
        GROUP BY status";

$result = mysqli_query($conn, $sql);

while ($row = mysqli_fetch_assoc($result)) {

    if ($row['status'] == 'todo') $todoCount = $row['total'];
    if ($row['status'] == 'doing') $doingCount = $row['total'];
    if ($row['status'] == 'done') $doneCount = $row['total'];

}

?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - NeuroDesk</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-zinc-950 text-white">

<div class="flex">

    <!-- Sidebar -->
    <div class="w-64 h-screen bg-zinc-900 p-6">

        <h1 class="text-3xl font-bold mb-10">
            NeuroDesk
        </h1>

        <nav class="space-y-4">

            <a href="#" class="block text-zinc-300 hover:text-white">
                Dashboard
            </a>

            <a href="#" class="block text-zinc-300 hover:text-white">
                Tasks
            </a>

            <a href="#" class="block text-zinc-300 hover:text-white">
                Analytics
            </a>

            <a href="logout.php" class="block text-red-400 hover:text-red-300">
                Logout
            </a>

        </nav>

    </div>
    

    <!-- Main -->
    <div class="flex-1 p-10">

        <h2 class="text-4xl font-bold mb-2">
            Benvenuto,
            <?php echo $_SESSION['username']; ?>
        </h2>

        <p class="text-zinc-400 mb-10">
            Ecco la tua produttività di oggi.
        </p>
        <!-- Add Task -->

<div class="bg-zinc-900 p-6 rounded-2xl mb-8">

    <h3 class="text-2xl font-bold mb-4">
        Nuovo Task
    </h3>

    <form method="POST">

        <div class="flex gap-4">

            <input
                type="text"
                name="task_title"
                placeholder="Scrivi un task..."
                required
                class="flex-1 p-3 rounded-xl bg-zinc-800 border border-zinc-700"
            >

            <button
                type="submit"
                name="add_task"
                class="bg-indigo-600 hover:bg-indigo-500 px-6 rounded-xl"
            >
                Aggiungi
            </button>

        </div>

    </form>

</div>
        <!-- Cards -->
        <div class="grid grid-cols-3 gap-6">

            <div class="bg-zinc-900 p-6 rounded-2xl">
                <h3 class="text-zinc-400 mb-2">Task completati</h3>
                <?php echo $tasksCompleted; ?>
            </div>

            <div class="bg-zinc-900 p-6 rounded-2xl">
                <h3 class="text-zinc-400 mb-2">Ore studiate</h3>
                <?php echo $hoursStudied; ?>h
            </div>

            <div class="bg-zinc-900 p-6 rounded-2xl">
                <h3 class="text-zinc-400 mb-2">Livello focus</h3>
                <?php echo $focus; ?>%
            </div>

        </div>
        <!-- USER STATS -->

<div class="grid grid-cols-3 gap-6 mt-8">

    <!-- XP -->
    <div class="bg-zinc-900 p-6 rounded-2xl">

        <h3 class="text-zinc-400 mb-2">XP Totali</h3>

        <p class="text-4xl font-bold text-indigo-400">
            <?php echo $xp; ?>
        </p>

    </div>

    <!-- LEVEL -->
    <div class="bg-zinc-900 p-6 rounded-2xl">

        <h3 class="text-zinc-400 mb-2">Livello</h3>

        <p class="text-4xl font-bold text-green-400">
            Lv. <?php echo $level; ?>
        </p>

    </div>

    <!-- PROGRESS -->
    <div class="bg-zinc-900 p-6 rounded-2xl">

        <h3 class="text-zinc-400 mb-2">Progressione</h3>

        <div class="w-full bg-zinc-800 rounded-full h-3 mt-4">

            <div class="bg-indigo-500 h-3 rounded-full"
                 style="width: <?php echo ($xp % 100); ?>%;">
            </div>

        </div>

    </div>

</div>
<!-- Task Board -->

<div class="mt-10">

    <h3 class="text-3xl font-bold mb-6">
        Task Board
    </h3>

    <div class="grid grid-cols-3 gap-6">

        <!-- TODO -->

        <div class="bg-zinc-900 p-5 rounded-2xl">

            <h4 class="text-xl font-bold mb-4 text-indigo-400">
                TO DO
            </h4>

            <div class="space-y-4">

                <?php

                $sql = "SELECT * FROM tasks
                        WHERE user_id='$user_id'
                        AND status='todo'
                        ORDER BY created_at DESC";

                $result = mysqli_query($conn, $sql);

                while ($task = mysqli_fetch_assoc($result)) {

                ?>

                <div class="bg-zinc-800 p-4 rounded-xl">

                    <p class="font-semibold mb-4">
                        <?php echo $task['title']; ?>
                    </p>

                    <div class="flex gap-2">

                        <form method="POST">

                            <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                            <input type="hidden" name="new_status" value="doing">

                            <button
                                type="submit"
                                name="update_status"
                                class="bg-yellow-500 px-3 py-1 rounded-lg text-sm"
                            >
                                Doing
                            </button>

                        </form>

                        <form method="POST">

                            <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">

                            <button
                                type="submit"
                                name="delete_task"
                                class="bg-red-500 px-3 py-1 rounded-lg text-sm"
                            >
                                Delete
                            </button>

                        </form>

                    </div>

                </div>

                <?php } ?>

            </div>

        </div>

        <!-- DOING -->

        <div class="bg-zinc-900 p-5 rounded-2xl">

            <h4 class="text-xl font-bold mb-4 text-yellow-400">
                DOING
            </h4>

            <div class="space-y-4">

                <?php

                $sql = "SELECT * FROM tasks
                        WHERE user_id='$user_id'
                        AND status='doing'
                        ORDER BY created_at DESC";

                $result = mysqli_query($conn, $sql);

                while ($task = mysqli_fetch_assoc($result)) {

                ?>

                <div class="bg-zinc-800 p-4 rounded-xl">

                    <p class="font-semibold mb-4">
                        <?php echo $task['title']; ?>
                    </p>

                    <div class="flex gap-2">

                        <form method="POST">

                            <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                            <input type="hidden" name="new_status" value="done">

                            <button
                                type="submit"
                                name="update_status"
                                class="bg-green-500 px-3 py-1 rounded-lg text-sm"
                            >
                                Done
                            </button>

                        </form>

                    </div>

                </div>

                <?php } ?>

            </div>

        </div>

        <!-- DONE -->

        <div class="bg-zinc-900 p-5 rounded-2xl">

            <h4 class="text-xl font-bold mb-4 text-green-400">
                DONE
            </h4>

            <div class="space-y-4">

                <?php

                $sql = "SELECT * FROM tasks
                        WHERE user_id='$user_id'
                        AND status='done'
                        ORDER BY created_at DESC";

                $result = mysqli_query($conn, $sql);

                while ($task = mysqli_fetch_assoc($result)) {

                ?>

                <div class="bg-zinc-800 p-4 rounded-xl opacity-70">

                    <p class="font-semibold">
                        <?php echo $task['title']; ?>
                    </p>

                </div>

                <?php } ?>

            </div>

        </div>

    </div>

</div>
<!-- Analytics -->

<div class="mt-12">

    <h3 class="text-3xl font-bold mb-6">
        Analytics
    </h3>

    <div class="bg-zinc-900 p-6 rounded-2xl">

        <canvas id="tasksChart"></canvas>

    </div>

</div>
    </div>

</div>
<script>
const ctx = document.getElementById('tasksChart');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['To Do', 'Doing', 'Done'],
        datasets: [{
            label: 'Tasks',
            data: [
                <?php echo $todoCount; ?>,
                <?php echo $doingCount; ?>,
                <?php echo $doneCount; ?>
            ],
            backgroundColor: [
                '#6366f1',
                '#facc15',
                '#22c55e'
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        }
    }
});
</script>

</body>
</html>