<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: index.html");
    exit();
}

include('db.php');

// Fetch current date
date_default_timezone_set('Europe/Tirane');
$current_date = date('Y-m-d');

// Fetch subjects from the database
$subjects_query = "SELECT * FROM subjects";
$subjects_result = $conn->query($subjects_query);

// Fetch student's name and class
$student_id = $_SESSION['user_id'];
$student_query = "SELECT users.username, classes.class_name 
                  FROM users 
                  JOIN student_class ON users.user_id = student_class.student_id 
                  JOIN classes ON student_class.class_id = classes.class_id 
                  WHERE users.user_id = $student_id";
$student_result = $conn->query($student_query);
$student_data = $student_result->fetch_assoc();

// Handle grade submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['grades']) && is_array($_POST['grades']) && !empty($_POST['grades'])) {
        $grades = $_POST['grades'];
        $grade_inserted = false; // Flag to track if any grade was inserted

        foreach ($grades as $subject_id => $grade) {
            if (empty($grade)) {
                continue;
            }

            $check_stmt = $conn->prepare("SELECT grade_id FROM self_grades WHERE student_id = ? AND subject_id = ? AND grade_date = ?");
            $check_stmt->bind_param("iis", $student_id, $subject_id, $current_date);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();

            if ($check_result->num_rows == 0) {
                $insert_stmt = $conn->prepare("INSERT INTO self_grades (student_id, subject_id, grade_date, grade, is_approved) VALUES (?, ?, ?, ?, ?)");
                if (!$insert_stmt) {
                    die("Prepare failed: " . $conn->error);
                }

                $is_approved = 0;

                $insert_stmt->bind_param("iissi", $student_id, $subject_id, $current_date, $grade, $is_approved);

                if ($insert_stmt->execute()) {
                    $grade_inserted = true; // Mark that a grade was inserted
                } else {
                    die("Error executing query: " . $insert_stmt->error);
                }
            } else {
                echo "<script>alert('Ju keni bërë vlerësimin e kësaj lënde për ditën e sotme.');</script>";
            }
        }

        if ($grade_inserted) {
            $success_message = "Notat u dërguan me sukses në vetëvlerësim!";
        }
    } else {
        echo "<script>alert('Nuk është dërguar asnjë notë! Ju lutem, përzgjidhni notat.');</script>";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista e Kontrollit - Vetëvlerësim</title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="styles.css">
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
<div class="student-container">
    <h1>Lista e Kontrollit - Vetëvlerësim</h1>
    <button class="grade-info" onclick="toggleInfo('grades')">Info për notimin</button>
    <button class="discipline-info" onclick="toggleInfo('discipline')">Disiplina</button>
    <button class="forget-info" onclick="toggleInfo('forget')">Mjetet e punës</button>
    <div class="info-div">
        </div>
            <script>
function toggleInfo(type) {
    var infoDiv = document.querySelector('.info-div');
    var gradeButton = document.querySelector('.grade-info');
    var disciplineButton = document.querySelector('.discipline-info');
    var forgetButton = document.querySelector('.forget-info');

    if (type === 'grades') {
        if (!gradeButton.classList.contains("active")) {
            infoDiv.style.display = "flex";
            gradeButton.textContent = "X";
            gradeButton.classList.add("active");

            disciplineButton.style.display = "block";
            disciplineButton.classList.remove("active");
            disciplineButton.textContent = "Disiplina";

            forgetButton.style.display = "block";
            forgetButton.classList.remove("active");
            forgetButton.textContent = "Mjetet e punës";

            infoDiv.innerHTML = `
                <div class="card-1">
                    <h2>A</h2>
                    <p>Plotësisht kam kuptuar mësimin /detyrat, rrallëherë marr sqarime.</p>
                </div>
                <div class="card-2">
                    <h2>B</h2>
                    <p>Pothuajse plotësisht kam kuptuar mësimin /detyrat, nganjëherë marr sqarime.</p>
                </div>
                <div class="card-3">
                    <h2>C</h2>
                    <p>Mesatarisht kam kuptuar mësimin /detyrat, disa herë marr sqarime.</p>
                </div>
                <div class="card-4">
                    <h2>D</h2>
                    <p>Pjesërisht kam kuptuar mësimin /detyrat, shpeshherë marr sqarime.</p>
                </div>
                <div class="card-5">
                    <h2>E</h2>
                    <p>Pothuajse pjesërisht kam kuptuar mësimin /detyrat, shumë herë marr sqarime.</p>
                </div>
            `;
        } else {
            infoDiv.style.display = "none";
            gradeButton.textContent = "Info për notimin";
            gradeButton.classList.remove("active");
        }
    } else if (type === 'discipline') {
        if (!disciplineButton.classList.contains("active")) {
            infoDiv.style.display = "flex";
            disciplineButton.textContent = "X";
            disciplineButton.classList.add("active");

            gradeButton.textContent = "Info për notimin";
            gradeButton.classList.remove("active");

            forgetButton.textContent = "Mjetet e punës";
            forgetButton.classList.remove("active");

            infoDiv.innerHTML = `
                <div class="card-1">
                    <h2>A</h2>
                    <p>Rrallëherë më tërhiqet vërejtja për koncentrim dhe të folurit pa leje. (1 herë)</p>
                </div>
                <div class="card-2">
                    <h2>B</h2>
                    <p>Nganjëherë më tërhiqet vërejtja për koncentrim dhe të folurit pa leje. (2-3 herë)</p>
                </div>
                <div class="card-3">
                    <h2>C</h2>
                    <p>Disa herë më tërhiqet vërejtja për koncentrim dhe të folurit pa leje. (4-5 herë)</p>
                </div>
                <div class="card-4">
                    <h2>D</h2>
                    <p>Shpeshherë më tërhiqet vërejtja për koncentrim dhe të folurit pa leje. (6-7 herë)</p>
                </div>
                <div class="card-5">
                    <h2>E</h2>
                    <p>Shumë herë më tërhiqet vërejtja për koncentrim dhe të folurit pa leje. (8+ herë)</p>
                </div>
            `;
        } else {
            infoDiv.style.display = "none";
            disciplineButton.textContent = "Disiplina";
            disciplineButton.classList.remove("active");
        }
    } else if (type === 'forget') {
        if (!forgetButton.classList.contains("active")) {
            infoDiv.style.display = "flex";
            forgetButton.textContent = "X";
            forgetButton.classList.add("active");

            gradeButton.textContent = "Info për notimin";
            gradeButton.classList.remove("active");

            disciplineButton.textContent = "Disiplina";
            disciplineButton.classList.remove("active");

            infoDiv.innerHTML = `
                <div class="card-1">
                    <h2>A</h2>
                    <p>Nuk i harroj mjetet e punës për mësim.</p>
                </div>
                <div class="card-2">
                    <h2>B</h2>
                    <p>I harroj mjetet e punës për mësim. (lapsin / gomën)</p>
                </div>
                <div class="card-3">
                    <h2>C</h2>
                    <p>I harroj mjetet e punës për mësim. (bllokun/ ngjyrat/ pentagramin)</p>
                </div>
                <div class="card-4">
                    <h2>D</h2>
                    <p>I harroj mjetet e punës për mësim. (librin/ fletoren/ vizoren / kompasin)</p>
                </div>
                <div class="card-5">
                    <h2>E</h2>
                    <p>I harroj mjetet e punës për mësim. (librat / fletoret/ portfolion)</p>
                </div>
            `;
        } else {
            infoDiv.style.display = "none";
            forgetButton.textContent = "Mjetet e punës";
            forgetButton.classList.remove("active");
        }
    }
}

</script>
    <?php if (isset($success_message)): ?>
        <p style="color: green; font-weight: bold;"> <?= $success_message ?> </p>
    <?php endif; ?>

    <form method="POST">
        <div class="student-info">
            <p>Emri dhe Mbiemri: <?= $student_data['username'] ?> </p>
            <p> Klasa: <?= $student_data['class_name'] ?></p>
        </div>
        <div class="table-responsive">
        <table class="student-table">
            <thead>
            <tr>
                <th rowspan="2">Lënda</th>
                <th colspan="5">Ngjyrat e Legos</th>
            </tr>
            <tr class="header-row">
                <th class="grade-a">A</th>
                <th class="grade-b">B</th>
                <th class="grade-c">C</th>
                <th class="grade-d">D</th>
                <th class="grade-e">E</th>
            </tr>
            </thead>
            <tbody>
            <?php while ($subject = $subjects_result->fetch_assoc()): ?>
                <tr>
                    <td><?= $subject['subject_name'] ?></td>
                    <td><input type="radio" name="grades[<?= $subject['subject_id'] ?>]" value="A"></td>
                    <td><input type="radio" name="grades[<?= $subject['subject_id'] ?>]" value="B"></td>
                    <td><input type="radio" name="grades[<?= $subject['subject_id'] ?>]" value="C"></td>
                    <td><input type="radio" name="grades[<?= $subject['subject_id'] ?>]" value="D"></td>
                    <td><input type="radio" name="grades[<?= $subject['subject_id'] ?>]" value="E"></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        </div>

        <button class="student-button" type="submit">Përfundo</button>
    </form>
</div>
</body>
</html>
