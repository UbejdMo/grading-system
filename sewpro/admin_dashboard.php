<?php
session_start();
include 'db.php';

// Ensure only admin can access this page
if ($_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
    <h1 class="admin-h1">Moduli Administratorit</h1>
    <div class="admin-button-container">
        <form action="manage_users.php">
            <button class="admin-dashboard-button"><span class="material-symbols-outlined">group</span><p>Menaxho Përdorues</p></button>
        </form>
        <form action="manage_classes.php">
            <button class="admin-dashboard-button"><span class="material-symbols-outlined">school</span><p>Menaxho Klasat</p></button>
        </form>
        <form action="manage_time_limits.php">
            <button class="admin-dashboard-button"><span class="material-symbols-outlined">schedule</span><p>Menaxho Orarin e Kyçjës</p></button>
        </form>
    </div>
</body>
</html>
