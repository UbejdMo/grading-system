<?php
require_once __DIR__ . '/includes/layout.php';
require_role('teacher');

$teacher_id = current_user_id();
$success_message = null;

// Aprovimi i notave: çdo notë identifikohet me grade_id specifik,
// kështu aprovohet vetëm vlerësimi konkret (jo të gjitha ditët përnjëherë).
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $updated_grades = $_POST['updated_grades'] ?? [];
    $valid_grades = ['A', 'B', 'C', 'D', 'E'];
    $approved_count = 0;

    // Vetëm notat e nxënësve të klasave të këtij mësuesi mund të aprovohen
    $stmt = $conn->prepare(
        'UPDATE self_grades sg
         JOIN student_class sc ON sc.student_id = sg.student_id
         JOIN teacher_class tc ON tc.class_id = sc.class_id AND tc.teacher_id = ?
         SET sg.grade = ?, sg.is_approved = 1, sg.approved_by = ?, sg.approved_at = NOW()
         WHERE sg.grade_id = ? AND sg.is_approved = 0'
    );

    foreach ($updated_grades as $grade_id => $grade) {
        if (!in_array($grade, $valid_grades, true)) {
            continue;
        }
        $grade_id = (int) $grade_id;
        $stmt->bind_param('isii', $teacher_id, $grade, $teacher_id, $grade_id);
        $stmt->execute();
        $approved_count += $stmt->affected_rows;
    }

    $success_message = $approved_count > 0
        ? sprintf(t('teacher.approved_n'), $approved_count)
        : t('teacher.none_new');
}

$subjects_result = $conn->query('SELECT subject_id, subject_name FROM subjects ORDER BY subject_name');

// Notat në pritje për lëndën e zgjedhur (vetëm nxënësit e klasave të mësuesit)
$selected_subject_id = (int) ($_GET['subject_id'] ?? 0);
$students_grades = [];
$pending_count = 0;

if ($selected_subject_id) {
    $stmt = $conn->prepare(
        'SELECT sg.grade_id, sg.grade, sg.grade_date, u.username
         FROM self_grades sg
         JOIN users u ON sg.student_id = u.user_id
         JOIN student_class sc ON sg.student_id = sc.student_id
         JOIN teacher_class tc ON sc.class_id = tc.class_id
         WHERE sg.subject_id = ?
           AND sg.is_approved = 0
           AND tc.teacher_id = ?
         ORDER BY sg.grade_date, u.username'
    );
    $stmt->bind_param('ii', $selected_subject_id, $teacher_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $students_grades[$row['grade']][] = $row;
        $pending_count++;
    }
}

page_header(t('teacher.title'), [
    ['href' => 'final_grades.php', 'icon' => 'grading', 'label' => t('teacher.final_nav')],
]);
?>
<div class="card">
    <h1><?= e(t('teacher.title')) ?></h1>
    <p class="muted"><?= e(t('teacher.intro')) ?></p>

    <?php if ($success_message !== null): ?>
        <p class="success-message"><?= e($success_message) ?></p>
    <?php endif; ?>

    <form method="GET">
        <div class="form-row">
            <label for="subject_id"><?= e(t('teacher.select_subject')) ?></label>
            <select name="subject_id" id="subject_id" onchange="this.form.submit()">
                <option value=""><?= e(t('select.placeholder')) ?></option>
                <?php while ($subject = $subjects_result->fetch_assoc()): ?>
                    <option value="<?= (int) $subject['subject_id'] ?>" <?= $selected_subject_id === (int) $subject['subject_id'] ? 'selected' : '' ?>>
                        <?= e($subject['subject_name']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
    </form>

    <?php if ($selected_subject_id): ?>
        <?php if ($pending_count === 0): ?>
            <p class="muted" style="margin-top: 1rem;"><?= e(t('teacher.no_pending')) ?></p>
        <?php else: ?>
            <form method="POST">
                <?= csrf_field() ?>
                <div class="drag-container">
                    <?php foreach (['A', 'B', 'C', 'D', 'E'] as $grade): ?>
                        <div class="grade-column" data-grade="<?= $grade ?>" ondrop="drop(event, '<?= $grade ?>')" ondragover="allowDrop(event)" ondragleave="dragLeave(event)">
                            <h3><?= e(t('teacher.grade')) ?> <?= $grade ?></h3>
                            <?php foreach ($students_grades[$grade] ?? [] as $entry): ?>
                                <div id="grade-<?= (int) $entry['grade_id'] ?>" class="student" draggable="true" ondragstart="drag(event)">
                                    <?= e($entry['username']) ?>
                                    <?php if ($entry['grade_date'] !== date('Y-m-d')): ?>
                                        <small><?= e(date('d.m.Y', strtotime($entry['grade_date']))) ?></small>
                                    <?php endif; ?>
                                    <input type="hidden" name="updated_grades[<?= (int) $entry['grade_id'] ?>]" value="<?= $grade ?>">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <button class="student-button" type="submit"><?= e(t('teacher.approve')) ?></button>
            </form>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script>
    function allowDrop(ev) {
        ev.preventDefault();
        ev.currentTarget.classList.add('drag-over');
    }

    function dragLeave(ev) {
        ev.currentTarget.classList.remove('drag-over');
    }

    function drag(ev) {
        ev.dataTransfer.setData('text', ev.target.id);
    }

    function drop(ev, grade) {
        ev.preventDefault();
        const column = ev.currentTarget;
        column.classList.remove('drag-over');
        const studentElement = document.getElementById(ev.dataTransfer.getData('text'));
        if (!studentElement) return;
        studentElement.querySelector('input').value = grade;
        column.appendChild(studentElement);
    }
</script>
<?php page_footer(); ?>
