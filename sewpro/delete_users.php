<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include('db.php');

// Merr listën e klasave
$classes_query = "SELECT * FROM classes";
$classes_result = $conn->query($classes_query);

// Filtrimi i përdoruesve bazuar në klasën e përzgjedhur
$class_id_filter = isset($_GET['class_id']) ? $_GET['class_id'] : null;

if ($class_id_filter) {
    // Query për të marrë të dhënat e përdoruesve që i përkasin klasës së përzgjedhur
    $users_query = "
        SELECT u.user_id, u.username, u.role
        FROM users u
        LEFT JOIN teacher_class tc ON u.user_id = tc.teacher_id
        LEFT JOIN student_class sc ON u.user_id = sc.student_id
        LEFT JOIN parent_student ps ON u.user_id = ps.parent_id
        LEFT JOIN student_class sc2 ON ps.student_id = sc2.student_id
        WHERE (tc.class_id = ? OR sc.class_id = ? OR sc2.class_id = ?)
        GROUP BY u.user_id
    ";
    $stmt = $conn->prepare($users_query);
    $stmt->bind_param("iii", $class_id_filter, $class_id_filter, $class_id_filter);
    $stmt->execute();
    $users_result = $stmt->get_result();
} else {
    // Merr të gjithë përdoruesit nëse nuk është përzgjedhur asnjë klasë
    $users_query = "SELECT * FROM users";
    $users_result = $conn->query($users_query);
}

// Handle deletion of user
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete_user'])) {
        $user_id = $_POST['user_id'];

        // Delete user
        $query = "DELETE FROM users WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $user_id);

        if ($stmt->execute()) {
            echo "Përdoruesi u fshi me sukses!";
        } else {
            echo "Error: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fshijë Përdoruesin</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
</head>

<body class="admindsh-body">
    <div class="menu">
        <li><a href="admin_dashboard.php"><span class="material-symbols-outlined icons">
                    <span>shield_person</span>
                </span><span>Moduli Administratorit</span></a></li>
        <li><a href="manage_users.php"><span class="material-symbols-outlined icons">
                    <span>group</span>
                </span><span>Menaxho Përdorues</span></a></li>
        <li><a href="logout.php">
                <span class="material-symbols-outlined icons">
                    <span>logout</span>
                </span>
                <span>Çkyçu</span></a>
        </li>
    </div>
    <section class="adduser-wrapper">
        <div>
            <h1>Fshijë Përdoruesin</h1>

            <!-- Dropdown për klasat -->
            <form method="GET">
                <label for="class_id">Filtro sipas klasës: <i class="fa-solid fa-filter"></i></label>
                <select name="class_id" id="class_id" onchange="this.form.submit()">
                    <option value="">Të gjitha klasat</option>
                    <?php
                while ($class = $classes_result->fetch_assoc()) {
                    $selected = ($class['class_id'] == $class_id_filter) ? 'selected' : '';
                    echo "<option value='{$class['class_id']}' $selected>{$class['class_name']}</option>";
                }
                ?>
                </select>
            </form>
            <br>

            <form method="POST">
                <select name="user_id" id="user_id" required>
                    <option value="">Zgjedh Përdoruesin</option>
                    <?php
                while ($user = $users_result->fetch_assoc()) {
                    echo "<option value='{$user['user_id']}'>{$user['username']} ({$user['role']})</option>";
                }
                ?>
                </select><br>

                <button class="admin-button" type="submit" name="delete_user">Fshijë Përdoruesin</button>
            </form>
        </div>
    </section>
</body>

</html>