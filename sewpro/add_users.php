<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include('db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_user'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $role = $_POST['role'];

        // Add user to "users" table
        $query = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sss", $username, $password, $role);

        if (!$stmt->execute()) {
            echo "Error: " . $stmt->error;
            exit();
        }

        $user_id = $stmt->insert_id; // Get ID of newly created user

        // If the user is a parent, link them to students
        if ($role == 'parent') {
            if (isset($_POST['student_ids']) && !empty($_POST['student_ids'])) {
                $student_ids = explode(',', $_POST['student_ids']); // List of student IDs

                foreach ($student_ids as $student_id) {
                    $query = "INSERT INTO parent_student (parent_id, student_id) VALUES (?, ?)";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("ii", $user_id, $student_id);

                    if (!$stmt->execute()) {
                        echo "Error: " . $stmt->error;
                        exit();
                    }
                }
            } else {
                echo "Asnjë nxënës nuk ndërlidhet me ndonjë prind.";
            }
        }

        // If the user is a teacher or student, link them to a class
        if ($role == 'teacher' || $role == 'student') {
            $class_id = $_POST['class_id'];
            $table = ($role == 'teacher') ? "teacher_class" : "student_class";
            $column = ($role == 'teacher') ? "teacher_id" : "student_id";

            $query = "INSERT INTO $table ($column, class_id) VALUES (?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ii", $user_id, $class_id);

            if (!$stmt->execute()) {
                echo "Error: " . $stmt->error;
                exit();
            }
        }
    }
}

$classes_query = "SELECT * FROM classes";
$classes_result = $conn->query($classes_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shto përdorues</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />

    <script>
        function filterParentStudents() {
            const selectedClass = document.getElementById('filter_class_parent').value;
            const studentItems = document.querySelectorAll('.student-item');

            studentItems.forEach(item => {
                if (selectedClass === "" || item.dataset.class === selectedClass) {
                    item.style.display = "block";
                } else {
                    item.style.display = "none";
                }
            });
        }

        function addStudentToParent(studentId, studentName) {
            const selectedStudentIds = document.getElementById('selected_student_ids');
            const studentIds = selectedStudentIds.value ? selectedStudentIds.value.split(',') : [];

            if (!studentIds.includes(studentId.toString())) {
                studentIds.push(studentId);
                selectedStudentIds.value = studentIds.join(',');

                const studentDisplay = document.createElement('div');
                studentDisplay.textContent = studentName;
                document.getElementById('students_list').appendChild(studentDisplay);
            }
        }

        function updateForm() {
            const role = document.getElementById("role").value;
            document.getElementById("student_select").style.display = (role === "parent") ? "block" : "none";
            document.getElementById("class_select").style.display = (role === "teacher" || role === "student") ? "block" : "none";
        }
    </script>
</head>
<body class="admindsh-body">
<div class="menu">
    <li>
        <a href="admin_dashboard.php"><span class="material-symbols-outlined icons">
<span>shield_person</span>
</span><span>Moduli Administratorit</span></a>
    </li>
<li><a href="manage_users.php"><span class="material-symbols-outlined icons">
    <span>group</span>
</span><span>Menaxho Përdorues</span></a>
</li>
        <li><a href="logout.php">
        <span class="material-symbols-outlined icons">
                        <span>logout</span>
                        </span>
                        <span>Çkyçu</span></a>
                    </li>
    </div>
    <section class="adduser-wrapper">
        <!-- Add User Form -->
         <div>
        <h2>Shto Përdorues</h2>
        <form method="POST">
            <select name="role" id="role" onchange="updateForm()" required>
                <option value="">Zgjedh Rolin</option>
                <option value="teacher">Mësues</option>
                <option value="student">Nxënës</option>
                <option value="parent">Prind</option>
            </select>

            <input placeholder="Përdoruesi" type="text" name="username" required/>
            <input placeholder="Fjalëkalimi" type="password" name="password" required/>

            <!-- Form for parents -->
            <div id="student_select" style="display: none;">
                <label>Filter by Class:</label>
                <select id="filter_class_parent" onchange="filterParentStudents()">
                    <option value="">Të gjitha klasat</option>
                    <?php
                    while ($class = $classes_result->fetch_assoc()) {
                        echo "<option value='{$class['class_id']}'>{$class['class_name']}</option>";
                    }
                    ?>
                </select><br>

                <label>Nxënësit e ndërlidhur:</label>
                <div id="students_list"></div>

                <?php
                $student_query = "
                SELECT u.user_id, u.username, sc.class_id 
                FROM users u
                JOIN student_class sc ON u.user_id = sc.student_id
                WHERE u.role = 'student'
                AND u.user_id NOT IN (
                    SELECT student_id FROM parent_student
                )";
            $student_result = $conn->query($student_query);
            while ($student = $student_result->fetch_assoc()) {
                echo "<div class='student-item' data-class='{$student['class_id']}'>
                        <span>{$student['username']}</span>
                        <button type='button' class='admin-button' onclick='addStudentToParent({$student['user_id']}, \"{$student['username']}\")'>+</button>
                      </div>";
            }
                ?>

                <input type="hidden" name="student_ids" id="selected_student_ids">
            </div>

            <!-- Form for teacher/student -->
            <div id="class_select" style="display: none;">
                <label>Klasa:</label>
                <select name="class_id">
                    <?php
                    $classes_result->data_seek(0); // Reset classes query result pointer
                    while ($class = $classes_result->fetch_assoc()) {
                        echo "<option value='{$class['class_id']}'>{$class['class_name']}</option>";
                    }
                    ?>
                </select><br>
            </div>

            <button class="admin-button" type="submit" name="add_user">Shto Përdorues</button>
            </form>
        </div>
    </section>
</body>
</html>
