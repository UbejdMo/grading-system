<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'parent') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="styles.css">
</head>
<body class="admindsh-body">
<div class="menu">
        <li><a href="logout.php">
        <span class="material-symbols-outlined icons">
                        <span>logout</span>
                        </span>
                        <span>Çkyçu</span></a>
                    </li>
        </div>
    <h1 class="admin-h1">Moduli Prindit</h1>
    <div class="admin-button-container">
        <form action="raporti_ditor.php">
            <button class="admin-dashboard-button"><span class="material-symbols-outlined">today</span><p>Raporti Ditorë</p></button>
        </form>
        <form action="final_gradesP.php">
            <button class="admin-dashboard-button"><span class="material-symbols-outlined">calendar_month</span><p>Notat Përfundimtare</p></button>
        </form>
    </div>
</body>
</html>
