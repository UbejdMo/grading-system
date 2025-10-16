<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'teacher') {
    header("Location: index.html");
    exit();
}

include('db.php');

// Fetch subjects for the dropdown
$subjects_query = "SELECT * FROM subjects";
$subjects_result = $conn->query($subjects_query);

// Handle grade update and approval
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $subject_id = $_POST['subject_id'];
    $updated_grades = $_POST['updated_grades'];

    $current_date = date('Y-m-d'); // Get the current date

    foreach ($updated_grades as $student_id => $grade) {
        // Update the `self_grades` table to mark grades as approved
        $stmt = $conn->prepare("UPDATE self_grades SET grade = ?, is_approved = 1 WHERE student_id = ? AND subject_id = ? AND is_approved = 0");
        $stmt->bind_param("sii", $grade, $student_id, $subject_id);
        $stmt->execute();

        // Check if a grade for the same date already exists in the `final_grades` table
        $check_stmt = $conn->prepare("SELECT grade_id FROM final_grades WHERE student_id = ? AND subject_id = ? AND grade_date = ?");
        $check_stmt->bind_param("iis", $student_id, $subject_id, $current_date);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows == 0) {
            // Insert the final grade into the `final_grades` table
            $insert_stmt = $conn->prepare("INSERT INTO final_grades (student_id, subject_id, grade_date, final_grade) VALUES (?, ?, ?, ?)");
            $insert_stmt->bind_param("iiss", $student_id, $subject_id, $current_date, $grade);
            $insert_stmt->execute();
        }
    }
    $success_message = "Notat u miratuan dhe u ruajtën me sukses në tabelën përfundimtare!";
}

// Fetch students' self-grades for the selected subject
$selected_subject_id = $_GET['subject_id'] ?? '';
$students_grades = [];
if ($selected_subject_id) {
    $grades_query = "SELECT sg.student_id, sg.grade, u.username 
                 FROM self_grades sg
                 JOIN users u ON sg.student_id = u.user_id
                 JOIN student_class sc ON sg.student_id = sc.student_id
                 JOIN teacher_class tc ON sc.class_id = tc.class_id
                 WHERE sg.subject_id = ? 
                 AND sg.is_approved = 0
                 AND tc.teacher_id = ?";

$stmt = $conn->prepare($grades_query);
$stmt->bind_param("ii", $selected_subject_id, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $students_grades[$row['grade']][] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moduli Mësuesit</title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="styles.css">
    <script>
        function allowDrop(ev) {
            ev.preventDefault();
        }

        function drag(ev) {
            ev.dataTransfer.setData("text", ev.target.id);
        }

        function drop(ev, grade) {
            ev.preventDefault();
            var data = ev.dataTransfer.getData("text");
            var studentElement = document.getElementById(data);
            var formInput = studentElement.querySelector('input');
            formInput.value = grade;
            ev.target.appendChild(studentElement);
        }
    </script>
</head>
<body>
<div class="menu">
        <li><a href="logout.php">
        <span class="material-symbols-outlined icons">
                        <span>logout</span>
                        </span>
                        <span>Çkyçu</span></a>
                    </li>
        </div>
    <div class="tch-dashboard-wrapper">
        <div class="tch-dashboard-container">
            <h1>Moduli Mësusesit</h1>

            <?php if (isset($success_message)): ?>
                <p class="success-message"><?= $success_message ?></p>
            <?php endif; ?>

            <form method="GET">
                <label for="subject_id">Zgjedh lëndën:</label>
                <select name="subject_id" id="subject_id" onchange="this.form.submit()">
                    <option value="">Zgjedh...</option>
                    <?php while ($subject = $subjects_result->fetch_assoc()): ?>
                        <option value="<?= $subject['subject_id'] ?>" <?= $selected_subject_id == $subject['subject_id'] ? 'selected' : '' ?>>
                            <?= $subject['subject_name'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </form>

            <?php if ($selected_subject_id): ?>
                <form method="POST">
                    <input type="hidden" name="subject_id" value="<?= $selected_subject_id ?>">

                    <div class="drag-container">
                        <?php foreach (['A', 'B', 'C', 'D', 'E'] as $grade): ?>
                            <div class="grade-column" ondrop="drop(event, '<?= $grade ?>')" ondragover="allowDrop(event)">
                                <h3>Nota <?= $grade ?></h3>
                                <?php if (!empty($students_grades[$grade])): ?>
                                    <?php foreach ($students_grades[$grade] as $student): ?>
                                        <div id="student-<?= $student['student_id'] ?>" class="student" draggable="true" ondragstart="drag(event)">
                                            <?= $student['username'] ?>
                                            <input type="hidden" name="updated_grades[<?= $student['student_id'] ?>]" value="<?= $grade ?>">
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <button class="student-button" type="submit">Përfundo</button>
                    <a href="final_grades.php">Notat përfundimtare</a>
                </form>
            <?php endif; ?>

        </div>
    </div>
</body>
</html>
