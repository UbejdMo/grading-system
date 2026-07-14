<?php
require_once __DIR__ . '/includes/layout.php';
require_once __DIR__ . '/includes/grades_view.php';
require_role('parent');

$parent_id = current_user_id();

// Vetëm fëmijët e lidhur me këtë prind
$stmt = $conn->prepare(
    'SELECT s.user_id, s.username
     FROM users s
     JOIN parent_student ps ON ps.student_id = s.user_id
     WHERE ps.parent_id = ?
     ORDER BY s.username'
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

page_header('Notat Përfundimtare', [
    ['href' => 'parent_dashboard.php', 'icon' => 'arrow_back', 'label' => 'Kthehu'],
]);
?>
<div class="card">
    <h1>Notat Përfundimtare</h1>

    <form method="GET">
        <div class="form-row">
            <label for="student_id">Zgjedh fëmijën tuaj:</label>
            <select name="student_id" id="student_id" onchange="this.form.submit()">
                <option value="">Zgjedh nxënësin</option>
                <?php foreach ($children as $id => $name): ?>
                    <option value="<?= $id ?>" <?= $selected_student_id === $id ? 'selected' : '' ?>>
                        <?= e($name) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>

    <?php if ($selected_student_id): ?>
        <h2>Vlerësimet për: <?= e($children[$selected_student_id]) ?></h2>
        <?php render_student_grades($conn, $selected_student_id); ?>
    <?php endif; ?>
</div>
<?php page_footer(); ?>
