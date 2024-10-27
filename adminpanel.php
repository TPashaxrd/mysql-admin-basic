<?php
session_start();
require 'config.php';

$message = '';
$timeout_duration = 300;

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $timeout_duration)) {
        session_unset();
        session_destroy();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
    $_SESSION['LAST_ACTIVITY'] = time();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($username === 'your_password' && $password === 'your_password') {
        $_SESSION['loggedin'] = true;
    } else {
        $message = "Name & Username are Incorrect!";
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

if (isset($_POST['clear'])) {
    try {
        $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

        foreach ($tables as $table) {
            $pdo->exec("DELETE FROM $table");
        }
        $message = "All Datas have been Deleted..";
    } catch (PDOException $e) {
        $message = "Error occurred while deleting data: " . $e->getMessage();
    }
} elseif (isset($_POST['delete_id'])) {
    $id = intval($_POST['id']);
    $table = $_POST['table'];

    try {
        $stmt = $pdo->prepare("DELETE FROM $table WHERE id = ?");
        $stmt->execute([$id]);
        $message = "Record with ID: $id has been deleted successfully.";
    } catch (PDOException $e) {
        $message = "Error occurred while deleting the record: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <img
    <title>NiveSOFT | Databases</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #1f1f1f;
            color: #fff;
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #5cb85c;
        }
        h2 {
            color: #5cb85c;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background-color: #101114;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ccc;
        }
        th {
            background-color: #5cb85c;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #15161d;
        }
        tr:hover {
            background-color: #c9302c;
        }
        .no-data {
            text-align: center;
            color: black;
        }
        button {
            padding: 10px 20px;
            background-color: #d9534f;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            display: block;
            margin: 20px auto;
            font-size: 16px;
        }
        button:hover {
            background-color: #c9302c;
        }
        form {
            margin: 20px auto;
            text-align: center;
        }
        .delete-form {
            display: inline-flex;
            align-items: center;
            margin-left: 10px;
        }
        .id-input {
            width: 60px;
            margin-right: 5px;
        }
        .login-form {
            text-align: center;
            margin-bottom: 20px;
        }
        .login-input {
            margin: 5px;
            padding: 10px;
            width: 150px;
        }
        .settings {
            position: absolute;
            top: 10px;
            right: 10px;
            color: white;
            cursor: pointer;
        }
        .settings-panel {
            display: none;
            position: absolute;
            right: 10px;
            top: 50px;
            background-color: #1f1f1f;
            border: 1px solid #5cb85c;
            padding: 10px;
            border-radius: 5px;
            width: 200px;
        }
        .settings-panel button {
            background-color: #5cb85c;
            color: white;
            border: none;
            padding: 10px;
            width: 100%;
            margin-top: 10px;
            cursor: pointer;
        }
        .settings-panel button:hover {
            color: white;
            background-color: green;
        }
    </style>
    <script>
        setTimeout(function() {
            window.location.href = "<?php echo $_SERVER['PHP_SELF']; ?>?logout=true";
        }, 300000);

        function toggleSettingsPanel() {
            var panel = document.getElementById("settings-panel");
            panel.style.display = panel.style.display === "block" ? "none" : "block";
        }
    </script>
</head>
<body>
    <div class="settings" onclick="toggleSettingsPanel()">⚙️ Settings</div>
    <div id="settings-panel" class="settings-panel">
        <form method="get" action="">
            <button type="submit" name="logout">Log Out</button>
            <button type="button">Testing 1</button>
            <button type="button">Testing 2</button>
            <button type="button">Testing 3</button>
        </form>
    </div>

    <?php if (!isset($_SESSION['loggedin'])): ?>
        <div class="login-form">
            <h1>Login To DB</h1>
            <?php if ($message): ?>
                <div style="color: #d9534f;"><?php echo $message; ?></div>
            <?php endif; ?>
            <form method="post">
                <input type="text" name="username" class="login-input" placeholder="Username" required>
                <input type="password" name="password" class="login-input" placeholder="Password" required>
                <button type="submit" name="login">Login</button>
            </form>
        </div>
    <?php else: ?>
        <h1>Data Tables</h1>
        
        <?php if ($message): ?>
            <div style="text-align: center; color: #5cb85c;"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="post" style="text-align: center;">
            <button type="submit" name="clear">Delete All Datas</button>
        </form>

        <?php
        try {
            $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

            foreach ($tables as $table) {
                echo "<h2>Table: " . htmlspecialchars($table) . "
                    <form method='post' class='delete-form'>
                        <input type='hidden' name='table' value='" . htmlspecialchars($table) . "'>
                        <label for='id'>ID:</label>
                        <input type='number' name='id' class='id-input' required>
                        <button type='submit' name='delete_id'>Delete</button>
                    </form>
                </h2>";
                
                $stmt = $pdo->query("SELECT * FROM $table");
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($rows) > 0) {
                    echo "<table>";
                    echo "<tr>";
                    foreach ($rows[0] as $column => $value) {
                        echo "<th>" . htmlspecialchars($column) . "</th>";
                    }
                    echo "<th>Delete</th>";
                    echo "</tr>";

                    foreach ($rows as $row) {
                        echo "<tr>";
                        foreach ($row as $value) {
                            echo "<td>" . htmlspecialchars($value) . "</td>";
                        }
                        echo "<td>
                            <form method='post' style='margin: 0;'>
                                <input type='hidden' name='table' value='" . htmlspecialchars($table) . "'>
                                <input type='hidden' name='id' value='" . htmlspecialchars($row['id']) . "'>
                                <button type='submit' name='delete_id' style='background-color: #d9534f;'>Delete</button>
                            </form>
                        </td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<div class='no-data'>No Data Available.</div>";
                }
            }
        } catch (PDOException $e) {
            echo "Query error: " . $e->getMessage();

        }
    endif; 
    ?>
</body>
</html>
