<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include('db.php');

// Handle adding classes
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_class'])) {
        $class_name = $_POST['class_name'];

        $query = "INSERT INTO classes (class_name) VALUES (?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $class_name);
        $stmt->execute();
    }

    // Handle deleting classes
    if (isset($_POST['delete_class'])) {
        $class_id_to_delete = $_POST['delete_class_id'];
        $query = "DELETE FROM classes WHERE class_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $class_id_to_delete);
        $stmt->execute();
    }

    // Handle editing class info
    if (isset($_POST['edit_class'])) {
        $class_id_to_edit = $_POST['edit_class_id'];
        $new_class_name = $_POST['new_class_name'];

        $query = "UPDATE classes SET class_name = ? WHERE class_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $new_class_name, $class_id_to_edit);
        $stmt->execute();
    }
}

// Fetch all classes for the edit and delete sections
$class_query = "SELECT * FROM classes";
$class_result = $conn->query($class_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menaxho Klasat</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
</head>
<body class="admindsh-body">
<div class="menu">
        <li><a href="admin_dashboard.php"><span class="material-symbols-outlined icons">
<span>shield_person</span>
</span><span>Moduli Administratorit</span></a></li>
        <li><a href="logout.php">
        <span class="material-symbols-outlined icons">
                        <span>logout</span>
                        </span>
                        <span>Çkyçu</span></a>
                    </li>
            </div>
    <section class="adduser-wrapper">
        <div>
        <h1>Menaxho Klasat</h1>

        <!-- Add Class Form -->
        <h2>Shto klasa</h2>
        <form method="POST">
            <label>Emri Klasës (p.sh., Klasa 1/1, Klasa 1/2):</label>
            <input type="text" name="class_name" required><br>
            <button class="admin-button" type="submit" name="add_class">Shto klasën</button>
        </form>

        <!-- Edit Class Form -->
        <h2>Modifiko Klasën</h2>
        <form method="POST">
            <label>Zgjedh klasën për modifikim:</label>
            <select name="edit_class_id">
                <?php
                while ($class = $class_result->fetch_assoc()) {
                    echo "<option value='{$class['class_id']}'>{$class['class_name']}</option>";
                }
                ?>
            </select><br>
            <label>Emri i ri i klasës:</label><input type="text" name="new_class_name" required><br>
            <button class="admin-button" type="submit" name="edit_class">Modifiko klasën</button>
        </form>

        <!-- Delete Class Form -->
        <h2>Fshijë klasën</h2>
        <form method="POST">
            <label>Zgjedh klasën për fshirje:</label>
            <select name="delete_class_id">
                <?php
                $class_result->data_seek(0); // Reset the result set for reuse
                while ($class = $class_result->fetch_assoc()) {
                    echo "<option value='{$class['class_id']}'>{$class['class_name']}</option>";
                }
                ?>
            </select><br>
            <button class="admin-button" type="submit" name="delete_class">Fshijë klasën</button>
        </form>
        </div>
    </section>
</body>
</html>
