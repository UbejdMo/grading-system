<?php
session_start();
include 'db.php';

// Ensure only admin can access this page
if ($_SESSION['role'] != 'admin') {
    header("Location: index.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
</head>
<body class="admindsh-body">
<div class="menu">
    <li>
        <a href="admin_dashboard.php"><span class="material-symbols-outlined icons">
<span>shield_person</span>
</span><span>Moduli Administratorit</span></a>
        </li>
        <li><a href="logout.php">
        <span class="material-symbols-outlined icons">
                        <span>logout</span>
                        </span>
                        <span>Çkyçu</span></a>
                    </li>
            </div>
    <h1 class="admin-h1">Menaxho Përdorues</h1>
    <div class="admin-button-container">
        <form action="add_users.php">
            <button class="admin-dashboard-button"><span class="material-symbols-outlined">person_add</span><p>Shto Përdorues</p></button>

        </form>
        <form action="edit_users.php">
        <button class="admin-dashboard-button"><span class="material-symbols-outlined">person_edit</span><p>Modifiko Përdorues</p></button>
        </form>
        <form action="delete_users.php">
        <button class="admin-dashboard-button"><span class="material-symbols-outlined">person_remove</span><p>Fshijë Përdorues</p></button>
        </form>
    </div>
</body>
</html>
