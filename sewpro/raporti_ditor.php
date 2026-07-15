<?php
require_once __DIR__ . '/includes/layout.php';
require_role('parent');

$parent_id = current_user_id();
$current_date = date('Y-m-d');

// Fëmijët e lidhur me këtë prind
$stmt = $conn->prepare(
    'SELECT u.user_id, u.username
     FROM users u
     JOIN parent_student ps ON u.user_id = ps.student_id
     WHERE ps.parent_id = ?
     ORDER BY u.username'
);
$stmt->bind_param('i', $parent_id);
$stmt->execute();
$result = $stmt->get_result();

$children = [];
while ($row = $result->fetch_assoc()) {
    $children[(int) $row['user_id']] = $row['username'];
}

$selected_student_id = (int) ($_GET['student_id'] ?? 0);
if ($selected_student_id && !isset($children[$selected_student_id])) {
    $selected_student_id = 0;
}

// Notat e sotme të aprovuara nga mësuesi
$daily_grades = [];
if ($selected_student_id) {
    $stmt = $conn->prepare(
        'SELECT s.subject_name, sg.grade
         FROM self_grades sg
         JOIN subjects s ON s.subject_id = sg.subject_id
         WHERE sg.student_id = ? AND sg.grade_date = ? AND sg.is_approved = 1
         ORDER BY s.subject_name'
    );
    $stmt->bind_param('is', $selected_student_id, $current_date);
    $stmt->execute();
    $daily_grades = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

page_header(t('daily.title'), [
    ['href' => 'parent_dashboard.php', 'icon' => 'arrow_back', 'label' => t('nav.back')],
]);
?>
<div class="card">
    <h1><?= e(t('daily.title')) ?></h1>

    <form method="GET">
        <div class="form-row">
            <label for="student_id"><?= e(t('fg.select_student')) ?></label>
            <select name="student_id" id="student_id" onchange="this.form.submit()">
                <option value=""><?= e(t('daily.select')) ?></option>
                <?php foreach ($children as $id => $name): ?>
                    <option value="<?= $id ?>" <?= $selected_student_id === $id ? 'selected' : '' ?>>
                        <?= e($name) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>

    <?php if ($selected_student_id): ?>
        <h3><?= e(t('daily.for_date')) ?> <?= e(date('d.m.Y')) ?></h3>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th><?= e(t('grades.subject')) ?></th>
                        <th><?= e(t('daily.day_grade')) ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($daily_grades)): ?>
                        <?php foreach ($daily_grades as $grade): ?>
                            <tr>
                                <td class="subject-name"><?= e($grade['subject_name']) ?></td>
                                <td>
                                    <span class="grade-badge grade-<?= strtolower($grade['grade']) ?>">
                                        <?= e($grade['grade']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2" class="muted"><?= e(t('daily.none')) ?></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
<?php page_footer(); ?>
