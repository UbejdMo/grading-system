<?php
require_once __DIR__ . '/includes/layout.php';
require_once __DIR__ . '/includes/grades_view.php';
require_role('teacher');

$teacher_id = current_user_id();

// Nxënësit e klasave të këtij mësuesi
$stmt = $conn->prepare(
    'SELECT s.user_id, s.username, c.class_name
     FROM users s
     JOIN student_class sc ON sc.student_id = s.user_id
     JOIN classes c ON c.class_id = sc.class_id
     JOIN teacher_class tc ON tc.class_id = sc.class_id
     WHERE tc.teacher_id = ?
     ORDER BY c.class_name, s.username'
);
$stmt->bind_param('i', $teacher_id);
$stmt->execute();
$students_result = $stmt->get_result();

$students = [];
while ($row = $students_result->fetch_assoc()) {
    $students[(int) $row['user_id']] = $row;
}

$selected_student_id = (int) ($_GET['student_id'] ?? 0);
// Lejohet vetëm shikimi i nxënësve të klasave të veta
if ($selected_student_id && !isset($students[$selected_student_id])) {
    $selected_student_id = 0;
}

page_header(t('fg.title'), [
    ['href' => 'teacher_dashboard.php', 'icon' => 'arrow_back', 'label' => t('nav.back')],
]);
?>
<div class="card">
    <h1><?= e(t('fg.title')) ?></h1>
    <p class="muted"><?= e(t('fg.intro')) ?></p>

    <form method="GET">
        <div class="form-row">
            <label for="student_id"><?= e(t('fg.select_student')) ?></label>
            <select name="student_id" id="student_id" onchange="this.form.submit()">
                <option value=""><?= e(t('fg.select_placeholder')) ?></option>
                <?php foreach ($students as $id => $student): ?>
                    <option value="<?= $id ?>" <?= $selected_student_id === $id ? 'selected' : '' ?>>
                        <?= e($student['username']) ?> (<?= e($student['class_name']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>

    <?php if ($selected_student_id): ?>
        <h2><?= e(t('fg.grades_for')) ?> <?= e($students[$selected_student_id]['username']) ?></h2>
        <?php render_student_grades($conn, $selected_student_id); ?>
    <?php endif; ?>
</div>
<?php page_footer(); ?>
