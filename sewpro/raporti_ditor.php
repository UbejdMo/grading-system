<?php
session_start();
require_once "db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'parent') {
    header("Location: index.php");
    exit();
}

$parentId = $_SESSION['user_id'];
$selectedStudentId = isset($_GET['student_id']) ? $_GET['student_id'] : null;
$currentDate = date('Y-m-d'); // Ensure date format matches database

// Fetch the students linked to the parent
$studentsQuery = "SELECT u.user_id, u.username FROM users u 
                  JOIN parent_student ps ON u.user_id = ps.student_id 
                  WHERE ps.parent_id = ?";
$studentsStmt = $conn->prepare($studentsQuery);
$studentsStmt->bind_param("i", $parentId);
$studentsStmt->execute();
$studentsResult = $studentsStmt->get_result();

$students = [];
while ($row = $studentsResult->fetch_assoc()) {
    $students[] = $row;
}

// Fetch daily grades if a student is selected
$dailyGrades = [];
if ($selectedStudentId) {
    $dailyGradeQuery = "SELECT fg.subject_id, sg.grade AS final_grade, fg.grade_date, s.subject_name
                        FROM final_grades fg
                        JOIN subjects s ON s.subject_id = fg.subject_id
                        JOIN self_grades sg ON sg.student_id = fg.student_id AND sg.subject_id = fg.subject_id AND sg.grade_date = fg.grade_date
                        WHERE fg.student_id = ? 
                        AND fg.grade_date = ?";
    
    $dailyGradeStmt = $conn->prepare($dailyGradeQuery);
    $dailyGradeStmt->bind_param("is", $selectedStudentId, $currentDate);
    $dailyGradeStmt->execute();
    $dailyGradesResult = $dailyGradeStmt->get_result();
    
    while ($row = $dailyGradesResult->fetch_assoc()) {
        $dailyGrades[$row['subject_id']] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Grades</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
</head>
<body>
<div class="menu">
    <li><a href="parent_dashboard.php">
<span class="material-symbols-outlined icons">
        <span>arrow_back</span>
        </span>
        <span>Kthehu</span></a>
    </li>
        <li><a href="logout.php">
        <span class="material-symbols-outlined icons">
                        <span>logout</span>
                        </span>
                        <span>Çkyçu</span></a>
                    </li>
        </div>
    <h2>Raporti Ditorë i Notave</h2>
    <form method="GET" action="">
        <label for="student_id">Zgjedh nxënësin:</label>
        <select class="option-half" name="student_id" id="student_id" onchange="this.form.submit()">
            <option class="option-half" value="">-- Zgjedh --</option>
            <?php foreach ($students as $student): ?>
                <option value="<?= $student['user_id']; ?>" <?= ($selectedStudentId == $student['user_id']) ? 'selected' : ''; ?>>
                    <?= htmlspecialchars($student['username']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>
    
    <?php if ($selectedStudentId): ?>
        <h3>Vlerësimi për datën: <?= htmlspecialchars($currentDate); ?></h3>
        <table class="student-table">
            <tr>
                <th>Lënda</th>
                <th>Nota Përfundimtare</th>
            </tr>
            <?php if (!empty($dailyGrades)): ?>
                <?php foreach ($dailyGrades as $grade): ?>
                    <tr>
                        <td><?= htmlspecialchars($grade['subject_name']); ?></td>
                        <td><?= htmlspecialchars($grade['final_grade']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="2">Nuk ka nota të disponueshme për sot.</td>
                </tr>
            <?php endif; ?>
        </table>
    <?php endif; ?>
</body>
</html>
