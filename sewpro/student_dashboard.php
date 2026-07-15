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
    ?: ['username' => '', 'class_name' => t('student.no_class')];

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
            $success_message = t('student.success');
        }
        if ($already_graded) {
            $info_message = t('student.already');
        }
    } else {
        $info_message = t('student.none');
    }
}

$subjects_result = $conn->query('SELECT subject_id, subject_name FROM subjects ORDER BY subject_name');

// Rubrikat e vlerësimit në gjuhën aktuale - u kalohen kartelave në JS
$rubrics = [];
foreach (['grades', 'discipline', 'forget'] as $type) {
    for ($i = 1; $i <= 5; $i++) {
        $rubrics[$type][] = t("rubric.$type.$i");
    }
}
$button_labels = [
    'grades' => t('student.info_grades'),
    'discipline' => t('student.info_discipline'),
    'forget' => t('student.info_forget'),
];

page_header(t('student.title'));
?>
<div class="student-container">
    <h1><?= e(t('student.title')) ?></h1>
    <button class="grade-info" onclick="toggleInfo('grades')"><?= e($button_labels['grades']) ?></button>
    <button class="discipline-info" onclick="toggleInfo('discipline')"><?= e($button_labels['discipline']) ?></button>
    <button class="forget-info" onclick="toggleInfo('forget')"><?= e($button_labels['forget']) ?></button>
    <div class="info-div"></div>
    <script>
        const infoCards = <?= json_encode($rubrics, JSON_UNESCAPED_UNICODE) ?>;
        const buttonLabels = <?= json_encode($button_labels, JSON_UNESCAPED_UNICODE) ?>;
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
            <p><?= e(t('student.name')) ?> <?= e($student_data['username']) ?></p>
            <p><?= e(t('student.class')) ?> <?= e($student_data['class_name']) ?></p>
        </div>
        <div class="table-responsive">
            <table class="student-table">
                <thead>
                    <tr>
                        <th rowspan="2"><?= e(t('student.subject')) ?></th>
                        <th colspan="5"><?= e(t('student.lego')) ?></th>
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

        <button class="student-button" type="submit"><?= e(t('student.submit')) ?></button>
    </form>
</div>
<?php page_footer(); ?>
