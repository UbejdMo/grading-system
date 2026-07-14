<?php
require_once __DIR__ . '/includes/layout.php';
require_role('student');

$student_id = current_user_id();
$current_date = date('Y-m-d');

// Emri dhe klasa e nxënësit
$stmt = $conn->prepare(
    'SELECT users.username, classes.class_name
     FROM users
     JOIN student_class ON users.user_id = student_class.student_id
     JOIN classes ON student_class.class_id = classes.class_id
     WHERE users.user_id = ?'
);
$stmt->bind_param('i', $student_id);
$stmt->execute();
$student_data = $stmt->get_result()->fetch_assoc()
    ?: ['username' => '', 'class_name' => 'Pa klasë'];

$success_message = null;
$info_message = null;

// Dërgimi i vetëvlerësimit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $grades = $_POST['grades'] ?? [];
    $valid_grades = ['A', 'B', 'C', 'D', 'E'];
    $grade_inserted = false;
    $already_graded = false;

    if (is_array($grades) && !empty($grades)) {
        $insert_stmt = $conn->prepare(
            'INSERT INTO self_grades (student_id, subject_id, grade_date, grade, is_approved) VALUES (?, ?, ?, ?, 0)'
        );

        foreach ($grades as $subject_id => $grade) {
            if (!in_array($grade, $valid_grades, true)) {
                continue;
            }
            $subject_id = (int) $subject_id;
            try {
                $insert_stmt->bind_param('iiss', $student_id, $subject_id, $current_date, $grade);
                $insert_stmt->execute();
                $grade_inserted = true;
            } catch (mysqli_sql_exception $e) {
                // Çelësi unik në bazë garanton një vlerësim në ditë për lëndë
                if ($e->getCode() === 1062) {
                    $already_graded = true;
                } else {
                    throw $e;
                }
            }
        }

        if ($grade_inserted) {
            $success_message = 'Notat u dërguan me sukses në vetëvlerësim!';
        }
        if ($already_graded) {
            $info_message = 'Disa lëndë i keni vlerësuar tashmë për ditën e sotme.';
        }
    } else {
        $info_message = 'Nuk është dërguar asnjë notë! Ju lutem, përzgjidhni notat.';
    }
}

$subjects_result = $conn->query('SELECT subject_id, subject_name FROM subjects ORDER BY subject_name');

page_header('Lista e Kontrollit - Vetëvlerësim');
?>
<div class="student-container">
    <h1>Lista e Kontrollit - Vetëvlerësim</h1>
    <button class="grade-info" onclick="toggleInfo('grades')">Info për notimin</button>
    <button class="discipline-info" onclick="toggleInfo('discipline')">Disiplina</button>
    <button class="forget-info" onclick="toggleInfo('forget')">Mjetet e punës</button>
    <div class="info-div"></div>
    <script>
        const infoCards = {
            grades: [
                'Plotësisht kam kuptuar mësimin /detyrat, rrallëherë marr sqarime.',
                'Pothuajse plotësisht kam kuptuar mësimin /detyrat, nganjëherë marr sqarime.',
                'Mesatarisht kam kuptuar mësimin /detyrat, disa herë marr sqarime.',
                'Pjesërisht kam kuptuar mësimin /detyrat, shpeshherë marr sqarime.',
                'Pothuajse pjesërisht kam kuptuar mësimin /detyrat, shumë herë marr sqarime.'
            ],
            discipline: [
                'Rrallëherë më tërhiqet vërejtja për koncentrim dhe të folurit pa leje. (1 herë)',
                'Nganjëherë më tërhiqet vërejtja për koncentrim dhe të folurit pa leje. (2-3 herë)',
                'Disa herë më tërhiqet vërejtja për koncentrim dhe të folurit pa leje. (4-5 herë)',
                'Shpeshherë më tërhiqet vërejtja për koncentrim dhe të folurit pa leje. (6-7 herë)',
                'Shumë herë më tërhiqet vërejtja për koncentrim dhe të folurit pa leje. (8+ herë)'
            ],
            forget: [
                'Nuk i harroj mjetet e punës për mësim.',
                'I harroj mjetet e punës për mësim. (lapsin / gomën)',
                'I harroj mjetet e punës për mësim. (bllokun/ ngjyrat/ pentagramin)',
                'I harroj mjetet e punës për mësim. (librin/ fletoren/ vizoren / kompasin)',
                'I harroj mjetet e punës për mësim. (librat / fletoret/ portfolion)'
            ]
        };
        const buttonLabels = { grades: 'Info për notimin', discipline: 'Disiplina', forget: 'Mjetet e punës' };
        const buttons = {
            grades: document.querySelector('.grade-info'),
            discipline: document.querySelector('.discipline-info'),
            forget: document.querySelector('.forget-info')
        };

        function toggleInfo(type) {
            const infoDiv = document.querySelector('.info-div');
            const wasActive = buttons[type].classList.contains('active');

            Object.keys(buttons).forEach(key => {
                buttons[key].classList.remove('active');
                buttons[key].textContent = buttonLabels[key];
            });

            if (wasActive) {
                infoDiv.style.display = 'none';
                return;
            }

            buttons[type].classList.add('active');
            buttons[type].textContent = 'X';
            infoDiv.innerHTML = infoCards[type].map((text, i) =>
                `<div class="card-${i + 1}"><h2>${'ABCDE'[i]}</h2><p>${text}</p></div>`
            ).join('');
            infoDiv.style.display = 'flex';
        }
    </script>

    <?php if ($success_message !== null): ?>
        <p class="success-message"><?= e($success_message) ?></p>
    <?php endif; ?>
    <?php if ($info_message !== null): ?>
        <p class="error"><?= e($info_message) ?></p>
    <?php endif; ?>

    <form method="POST">
        <?= csrf_field() ?>
        <div class="student-info">
            <p>Emri dhe Mbiemri: <?= e($student_data['username']) ?></p>
            <p>Klasa: <?= e($student_data['class_name']) ?></p>
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
                            <td><?= e($subject['subject_name']) ?></td>
                            <?php foreach (['A', 'B', 'C', 'D', 'E'] as $g): ?>
                                <td><input type="radio" name="grades[<?= (int) $subject['subject_id'] ?>]" value="<?= $g ?>"></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <button class="student-button" type="submit">Përfundo</button>
    </form>
</div>
<?php page_footer(); ?>
