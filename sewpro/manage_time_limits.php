<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include('db.php');  // Include the database connection file

// Function to get time limits from the database
function get_time_limits()
{
    global $conn;  // Use the global connection

    $sql = "SELECT * FROM user_time_limits";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $time_limits = [];
        while ($row = $result->fetch_assoc()) {
            $time_limits[] = $row;
        }
        return $time_limits;
    } else {
        return [];
    }
}

// Function to get a specific time limit by limit_id
function get_time_limit_by_id($limit_id)
{
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM user_time_limits WHERE limit_id = ?");
    $stmt->bind_param("i", $limit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Function to translate days from English to Albanian
function translate_day_to_shqip($day_in_english)
{
    $days_translation = array(
        'Monday' => 'e Hënë',
        'Tuesday' => 'e Martë',
        'Wednesday' => 'e Mërkurë',
        'Thursday' => 'e Enjte',
        'Friday' => 'e Premte',
        'Saturday' => 'e Shtunë',
        'Sunday' => 'e Diel'
    );
    return isset($days_translation[$day_in_english]) ? $days_translation[$day_in_english] : $day_in_english;
}
// Function to translate roles from English to Albanian
function translate_role_to_shqip($role)
{
    $roles_translation = array(
        'admin' => 'Administrator',
        'teacher' => 'Mësues',
        'student' => 'Nxënës',
        'parent' => 'Prind'
    );
    return isset($roles_translation[$role]) ? $roles_translation[$role] : $role;
}

// Table displaying current time limits

// Function to display checkboxes for all days of the week
function display_days_of_week($selected_days = [])
{
    // Days of the week in English
    $days_of_week = array(
        'Monday' => 'e Hënë',
        'Tuesday' => 'e Martë',
        'Wednesday' => 'e Mërkurë',
        'Thursday' => 'e Enjte',
        'Friday' => 'e Premte',
        'Saturday' => 'e Shtunë',
        'Sunday' => 'e Diel'
    );

    foreach ($days_of_week as $day_english => $day_shqip) {
        $checked = in_array($day_english, $selected_days) ? 'checked' : ''; // Check if day is selected
        echo "<div class='checkboxes'><input type='checkbox' name='days[]' value='$day_english' $checked><p> $day_shqip</p></div><br>";
    }
}

// Add new time limit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_time_limit'])) {
    $role = $_POST['role'];
    $days = isset($_POST['days']) ? implode(",", $_POST['days']) : '';  // Merge selected days into a string
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    if (empty($days)) {
        $error_message = "Duhet të zgjedhni së paku një ditë!";
    } else {
        $stmt = $conn->prepare("INSERT INTO user_time_limits (role, days, start_time, end_time) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $role, $days, $start_time, $end_time);
        $stmt->execute();
        $success_message = "Orari i kyçjës u shtua me sukses!";
    }
}

// Update time limit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_time_limit'])) {
    $limit_id = $_POST['limit_id'];
    $role = $_POST['role'];
    $days = isset($_POST['days']) ? implode(",", $_POST['days']) : '';  // Merge selected days into a string
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    if (empty($days)) {
        $error_message = "Duhet të zgjedhni së paku një ditë!";
    } else {
        $stmt = $conn->prepare("UPDATE user_time_limits SET role = ?, days = ?, start_time = ?, end_time = ? WHERE limit_id = ?");
        $stmt->bind_param("ssssi", $role, $days, $start_time, $end_time, $limit_id);
        $stmt->execute();
        $success_message = "Orari i kyçjës u modifikua me sukses!";
    }
}

// Delete time limit
if (isset($_GET['delete'])) {
    $limit_id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM user_time_limits WHERE limit_id = ?");
    $stmt->bind_param("i", $limit_id);
    $stmt->execute();
    $success_message = "Orari i kyçjës u fshi me sukses!";
}

// Prepare data for editing
$edit_data = null;
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $edit_data = get_time_limit_by_id($edit_id);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menaxho Orarin e Kyçjës</title>
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
            <h1>Menaxho Orarin e Kyçjës</h1>

            <!-- Success or Error Message -->
            <?php if (isset($success_message)): ?>
                <p style="color: green;"><?php echo $success_message; ?></p>
            <?php endif; ?>
            <?php if (isset($error_message)): ?>
                <p style="color: red;"><?php echo $error_message; ?></p>
            <?php endif; ?>

            <!-- Form for adding time limit -->
            <h2>Shto Orarin e Kyçjës</h2>
            <form method="POST" action="manage_time_limits.php">
                <label for="role">Roli:</label>
                <select class="select-role" name="role" required>
                    <option value="admin">Administrator</option>
                    <option value="teacher">Mësues</option>
                    <option value="student">Nxënës</option>
                    <option value="parent">Prind</option>
                </select>

                <label for="days">Ditët:</label><br>
                <?php display_days_of_week(); ?><br>

                <label for="start_time">Koha e fillimit:</label>
                <input type="time" name="start_time" required><br>

                <label for="end_time">Koha e përfundimit:</label>
                <input type="time" name="end_time" required><br>

                <input type="submit" class="admin-button" name="add_time_limit" value="Shto orarin e kyçjës">
            </form>

            <!-- Form for editing time limit -->
            <?php if ($edit_data): ?>
                <h2>Modifiko Orarin e Kyçjës</h2>
                <form method="POST" action="manage_time_limits.php">
                    <input type="hidden" name="limit_id" value="<?php echo $edit_data['limit_id']; ?>">
                    <label for="role">Roli:</label>
                    <select name="role" required>
                        <option value="admin" <?php if ($edit_data['role'] == 'admin')
                            echo 'selected'; ?>>Administrator
                        </option>
                        <option value="teacher" <?php if ($edit_data['role'] == 'teacher')
                            echo 'selected'; ?>>Mësues</option>
                        <option value="student" <?php if ($edit_data['role'] == 'student')
                            echo 'selected'; ?>>Nxënës</option>
                    </select><br>

                    <label for="days">Ditët:</label><br>
                    <?php display_days_of_week(explode(',', $edit_data['days'])); ?><br>

                    <label for="start_time">Koha e fillimit:</label>
                    <input type="time" name="start_time" value="<?php echo $edit_data['start_time']; ?>" required><br>

                    <label for="end_time">Koha e përfundimit:</label>
                    <input type="time" name="end_time" value="<?php echo $edit_data['end_time']; ?>" required><br>

                    <input type="submit" class="admin-button" name="update_time_limit" value="Modifiko orarin e kyçjës">
                </form>
            <?php endif; ?>

            <!-- Table displaying current time limits -->
            <h2>Orari aktual i Kyçjës</h2>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Roli</th>
                            <th>Ditët</th>
                            <th>Koha e fillimit</th>
                            <th>Koha e përfundimit</th>
                            <th>Veprimet</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $time_limits = get_time_limits();
                        foreach ($time_limits as $time_limit):
                            ?>
                            <tr>
                                <td><?php echo translate_role_to_shqip($time_limit['role']); ?></td>
                                <td><?php
                                $days = explode(',', $time_limit['days']); // Ndajmë ditët
                                $translated_days = array_map('translate_day_to_shqip', array_map('trim', $days)); // Përkthejmë secilën ditë dhe heqim hapësirat
                                echo implode(', ', $translated_days); // Bashkojmë përsëri ditët e përkthyera me presje
                                ?></td>
                                <td><?php echo $time_limit['start_time']; ?></td>
                                <td><?php echo $time_limit['end_time']; ?></td>
                                <td>
                                    <a href="?edit=<?php echo $time_limit['limit_id']; ?>">Modifiko</a> |
                                    <a href="?delete=<?php echo $time_limit['limit_id']; ?>"
                                        onclick="return confirm('A jeni të sigurtë se dëshironi ta fshini?')">Fshijë</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </section>
</body>

</html>