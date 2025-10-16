<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'parent') {
    header("Location: login.php");
    exit();
}

include('db.php');

// Log out functionality
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Fetch the parent_id from the session
$parent_id = $_SESSION['user_id'];

// Fetch the students associated with this parent
$query = "SELECT s.user_id, s.username
          FROM users s
          JOIN parent_student ps ON ps.student_id = s.user_id
          WHERE ps.parent_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $parent_id);
$stmt->execute();
$result = $stmt->get_result();

// Fetch all subjects
$subjects = [];
$subjectQuery = "SELECT subject_id, subject_name FROM subjects";
$subjectResult = $conn->query($subjectQuery);
if ($subjectResult->num_rows > 0) {
    while ($row = $subjectResult->fetch_assoc()) {
        $subjects[$row['subject_id']] = $row['subject_name'];
    }
}

// Handle selected student
$selectedStudentId = isset($_POST['student_id']) ? $_POST['student_id'] : null;
$grades = [];
$finalGrades = [];

if ($selectedStudentId) {
    // Fetch grades for the selected student
    $gradeQuery = "
        SELECT sg.subject_id AS subject_id, sg.grade, sg.month, sg.year, sg.is_approved, s.subject_name
        FROM self_grades sg
        JOIN subjects s ON s.subject_id = sg.subject_id
        WHERE sg.student_id = ? AND sg.is_approved = 1";
    $gradeStmt = $conn->prepare($gradeQuery);
    $gradeStmt->bind_param("i", $selectedStudentId);
    $gradeStmt->execute();
    $gradesResult = $gradeStmt->get_result();
    
    while ($row = $gradesResult->fetch_assoc()) {
        $grades[$row['subject_id']][$row['month']] = $row; // Group grades by subject and month
    }

    // Fetch final grades for the selected student
    $finalGradeQuery = "
        SELECT fg.subject_id AS subject_id, fg.final_grade, fg.semester, s.subject_name
        FROM final_grades fg
        JOIN subjects s ON s.subject_id = fg.subject_id
        WHERE fg.student_id = ?";
    $finalGradeStmt = $conn->prepare($finalGradeQuery);
    $finalGradeStmt->bind_param("i", $selectedStudentId);
    $finalGradeStmt->execute();
    $finalGradesResult = $finalGradeStmt->get_result();
    
    while ($row = $finalGradesResult->fetch_assoc()) {
        $finalGrades[$row['subject_id']][$row['semester']] = $row; // Group final grades by subject and semester
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moduli Prindit</title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }
        h1 {
            color: #333;
            font-size: 2rem;
            margin-top: 30px;
            font-weight: 500;
        }
        .container {
            width: 100%;
            max-width: 1000px;
            padding: 20px;
            margin: 20px 0;
            background-color: #fff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 30px;
        }
        select, button {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 6px;
            outline: none;
            background-color: #f9f9f9;
        }
        select:focus, button:focus {
            border-color: #3b80f1;
        }
        button {
            cursor: pointer;
            background-color: #3b80f1;
            color: white;
            border: none;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #2a64c4;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            background-color: #f9f9f9;
            border-radius: 8px;
        }
        th, td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f1f1f1;
            color: #333;
        }
        td {
            color: #555;
        }
        .table-header {
            background-color: #eef1f5;
        }
        .footer {
            margin-top: 20px;
            font-size: 14px;
            color: #888;
            text-align: center;
        }
        .no-data {
            text-align: center;
            font-size: 18px;
            color: #999;
        }
        @media (max-width: 768px) {
            .container {
                padding: 15px;
                width: 90%;
            }
            h1 {
                font-size: 1.5rem;
            }
            select, button {
                font-size: 14px;
                padding: 8px;
            }
            table {
                font-size: 14px;
            }
        }
    </style>
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
<div class="container">
    <h1>Moduli Prindit</h1>
    
    <!-- Log Out Button
    <form method="POST">
        <button type="submit" name="logout">Log Out</button>
    </form> -->
    
    <form method="POST">
        <label for="student_id">Zgjedh fëmijën tuaj:</label>
        <select name="student_id" required onchange="this.form.submit()">
            <option value="">Zgjedh nxënësin</option>
            <?php
            while ($row = $result->fetch_assoc()) {
                echo "<option value='{$row['user_id']}' " . ($selectedStudentId == $row['user_id'] ? 'selected' : '') . ">{$row['username']}</option>";
            }
            ?>
        </select>
    </form>
    
    <?php if ($selectedStudentId) : ?>
        <h2>Vlerësimet për nxënësin e përzgjedhur</h2>
        
        <!-- Grades for the First Semester (Months 1-4) -->
        <h3>Notat e gjysmëvjetorit të parë (Muaji 1-4)</h3>
        <table>
            <thead>
                <tr class="table-header">
                <th>Lënda</th>
                    <th>Muaji 1</th>
                    <th>Muaji 2</th>
                    <th>Muaji 3</th>
                    <th>Muaji 4</th>
                    <th>Nota përfundimtare</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($subjects as $subjectId => $subjectName) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($subjectName); ?></td>
                        <?php for ($month = 1; $month <= 4; $month++) : ?>
                            <td>
                                <?php echo isset($grades[$subjectId][$month]) ? htmlspecialchars($grades[$subjectId][$month]['grade']) : 'No grade'; ?>
                            </td>
                        <?php endfor; ?>
                        <td>
                        <?php
            $gradeMapping = [
                'A' => 5,
                'B' => 4,
                'C' => 3,
                'D' => 2,
                'E' => 1
            ];
            
            $totalGrades = 0;
            $gradeCount = 0;
            
            for ($month = 1; $month <= 4; $month++) {
                if (isset($grades[$subjectId][$month])) {
                    $grade = $grades[$subjectId][$month]['grade'];
            
                    if (isset($gradeMapping[$grade])) {
                        $totalGrades += $gradeMapping[$grade];
                        $gradeCount++;
                    }
                }
            }
            
            if ($gradeCount > 0) {
                $averageGrade = $totalGrades / $gradeCount;
            
                if ($averageGrade > 4) {
                    $finalGrade = 'A';
                } elseif ($averageGrade > 3) {
                    $finalGrade = 'B';
                } elseif ($averageGrade > 2) {
                    $finalGrade = 'C';
                } elseif ($averageGrade > 1) {
                    $finalGrade = 'D';
                } else {
                    $finalGrade = 'E';
                }

                echo htmlspecialchars($finalGrade);
            } else {
                echo 'Nuk ka notë';
            }
            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Grades for the Second Semester (Months 5-10) -->
        <h3>Notat e gjysmëvjetorit të dytë (Muaji 5-10)</h3>
        <table>
            <thead>
                <tr class="table-header">
                <th>Lënda</th>
                    <th>Muaji 5</th>
                    <th>Muaji 6</th>
                    <th>Muaji 7</th>
                    <th>Muaji 8</th>
                    <th>Muaji 9</th>
                    <th>Muaji 10</th>
                    <th>Nota përfundimtare</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($subjects as $subjectId => $subjectName) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($subjectName); ?></td>
                        <?php for ($month = 5; $month <= 10; $month++) : ?>
                            <td>
                                <?php echo isset($grades[$subjectId][$month]) ? htmlspecialchars($grades[$subjectId][$month]['grade']) : 'No grade'; ?>
                            </td>
                        <?php endfor; ?>
                        <td>
                        <?php
                        $gradeMapping = [
                            'A' => 5,
                            'B' => 4,
                            'C' => 3,
                            'D' => 2,
                            'E' => 1
                        ];

                        $totalGrades = 0;
                        $gradeCount = 0;

                        for ($month = 5; $month <= 10; $month++) {
                            if (isset($grades[$subjectId][$month])) {
                                $grade = $grades[$subjectId][$month]['grade'];

                                if (isset($gradeMapping[$grade])) {
                                    $totalGrades += $gradeMapping[$grade];
                                    $gradeCount++;
                                }
                            }
                        }

                        if ($gradeCount > 0) {
                            $averageGrade = $totalGrades / $gradeCount;

                            if ($averageGrade > 4) {
                                $finalGrade = 'A';
                            } elseif ($averageGrade > 3) {
                                $finalGrade = 'B';
                            } elseif ($averageGrade > 2) {
                                $finalGrade = 'C';
                            } elseif ($averageGrade > 1) {
                                $finalGrade = 'D';
                            } else {
                                $finalGrade = 'E';
                            }

                            echo htmlspecialchars($finalGrade);
                        } else {
                            echo 'Nuk ka notë';
                        }
                        ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Final Grades for Both Semesters -->
        <h3>Notat Përfundimtare</h3>
        <table>
            <thead>
                <tr class="table-header">
                    <th>Lënda</th>
                    <th>Nota Përfundimtare</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($subjects as $subjectId => $subjectName) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($subjectName); ?></td>
                        <td>
                            <?php
                                                    $gradeMapping = [
                            'A' => 5,
                            'B' => 4,
                            'C' => 3,
                            'D' => 2,
                            'E' => 1
                        ];

                        $totalGrades = 0;
                        $gradeCount = 0;

                        for ($month = 1; $month <= 10; $month++) {
                            if (isset($grades[$subjectId][$month])) {
                                $grade = $grades[$subjectId][$month]['grade'];

                                if (isset($gradeMapping[$grade])) {
                                    $totalGrades += $gradeMapping[$grade];
                                    $gradeCount++;
                                }
                            }
                        }

                        if ($gradeCount > 0) {
                            $averageGrade = $totalGrades / $gradeCount;

                            if ($averageGrade > 4) {
                                $finalGrade = 'A';
                            } elseif ($averageGrade > 3) {
                                $finalGrade = 'B';
                            } elseif ($averageGrade > 2) {
                                $finalGrade = 'C';
                            } elseif ($averageGrade > 1) {
                                $finalGrade = 'D';
                            } else {
                                $finalGrade = 'E';
                            }

                            echo htmlspecialchars($finalGrade);
                        } else {
                            echo 'Nuk ka notë';
                        }
                        ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<div class="footer">
    <p>&copy; 2025 Moduli Prindit. Të gjitha të drejtat janë të rezervuara.</p>
</div>

</body>
</html>
