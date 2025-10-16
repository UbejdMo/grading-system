<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include('db.php');

// Get the list of classes
$classes_query = "SELECT * FROM classes";
$classes_result = $conn->query($classes_query);

// Filter users based on selected class
$class_id_filter = isset($_GET['class_id']) ? $_GET['class_id'] : null;

if ($class_id_filter) {
    // Query to fetch users belonging to the selected class
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
    // Fetch all users if no class is selected
    $users_query = "SELECT * FROM users";
    $users_result = $conn->query($users_query);
}

// Handle form submission for updating user or removing student-parent connection
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['edit_user'])) {
        $user_id = $_POST['user_id'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $role = $_POST['role'];

        $query = "UPDATE users SET username = ?, password = ?, role = ? WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssi", $username, $password, $role, $user_id);

        if ($stmt->execute()) {
            echo "Përdoruesi u modifikua me sukses!";
        } else {
            echo "Error: " . $stmt->error;
        }
    }

    if (isset($_POST['remove_child'])) {
        $parent_id = $_POST['parent_id'];
        $student_id = $_POST['student_id'];

        $delete_query = "DELETE FROM parent_student WHERE parent_id = ? AND student_id = ?";
        $stmt = $conn->prepare($delete_query);
        $stmt->bind_param("ii", $parent_id, $student_id);

        if ($stmt->execute()) {
            echo "Lidhja e Nxënësit me Prind u fshi me sukses!";
        } else {
            echo "Error: " . $stmt->error;
        }
    }
}

// Fetch the child (student) of a selected parent
$selected_parent_id = isset($_GET['parent_id']) ? $_GET['parent_id'] : null;
$children_result = null;
if ($selected_parent_id) {
    $children_query = "
        SELECT s.user_id, s.username
        FROM users s
        INNER JOIN parent_student ps ON s.user_id = ps.student_id
        WHERE ps.parent_id = ?
    ";
    $stmt = $conn->prepare($children_query);
    $stmt->bind_param("i", $selected_parent_id);
    $stmt->execute();
    $children_result = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
</head>

<body class="admindsh-body">
    <div class="menu">
        <li><a href="admin_dashboard.php"><span class="material-symbols-outlined icons">shield_person</span>Moduli Administratorit</a></li>
        <li><a href="manage_users.php"><span class="material-symbols-outlined icons">group</span>Menaxho Përdorues</a></li>
        <li><a href="logout.php"><span class="material-symbols-outlined icons">logout</span>Çkyçu</a></li>
    </div>
    <section class="adduser-wrapper">
        <div>
            <h1>Modifiko Përdoruesin</h1>

            <!-- Dropdown to filter users by class -->
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

            <!-- Form to edit user details -->
            <form method="POST">
                <select name="user_id" id="user_id" required onchange="window.location.href='?parent_id=' + this.value">
                    <option value="">Zgjedh Përdoruesin</option>
                    <?php
                    while ($user = $users_result->fetch_assoc()) {
                        $selected = ($user['user_id'] == $selected_parent_id) ? 'selected' : '';
                        echo "<option value='{$user['user_id']}' $selected>{$user['username']} ({$user['role']})</option>";
                    }
                    ?>
                </select>

                <input placeholder="Përdoruesi" type="text" name="username" required />
                <input placeholder="Fjalëkalimi" type="password" name="password" required />

                <label>Roli:</label>
                <select name="role" required>
                    <option value="teacher">Mësues</option>
                    <option value="student">Nxënës</option>
                    <option value="parent">Prind</option>
                </select><br>

                <button class="admin-button" type="submit" name="edit_user">Modifiko Përdoruesin</button>
            </form>

            <!-- Display and remove connected children if parent is selected -->
            <?php if ($children_result && $children_result->num_rows > 0): ?>
                <h2>Nxënësit e ndërlidhur</h2>
                <ul>
                    <?php while ($child = $children_result->fetch_assoc()): ?>
                        <li class="no-style">
                            <?php echo $child['username']; ?>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="parent_id" value="<?php echo $selected_parent_id; ?>">
                                <input type="hidden" name="student_id" value="<?php echo $child['user_id']; ?>">
                                <button type="submit" name="remove_child" class="remove-btn">x</button>
                            </form>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php endif; ?>

        </div>
    </section>
</body>

</html>
