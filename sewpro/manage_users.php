<?php
require_once __DIR__ . '/includes/layout.php';
require_role('admin');

$roles_shqip = [
    'admin' => t('role.admin'),
    'teacher' => t('role.teacher'),
    'student' => t('role.student'),
    'parent' => t('role.parent'),
];
$success_message = null;
$error_message = null;

// Fshirja e përdoruesit (POST me konfirmim, jo GET)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    csrf_check();
    $user_id = (int) $_POST['user_id'];

    if ($user_id === current_user_id()) {
        $error_message = t('users.cant_self');
    } else {
        $stmt = $conn->prepare('DELETE FROM users WHERE user_id = ?');
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $success_message = $stmt->affected_rows > 0
            ? t('users.deleted')
            : t('users.notfound');
    }
}

$classes_result = $conn->query('SELECT * FROM classes ORDER BY class_name');

// Filtrimi sipas klasës (nxënës/mësues të klasës, plus prindër të nxënësve të saj)
$class_id_filter = (int) ($_GET['class_id'] ?? 0);
$role_filter = $_GET['role'] ?? '';
if (!isset($roles_shqip[$role_filter])) {
    $role_filter = '';
}

$sql = "SELECT u.user_id, u.username, u.role,
               GROUP_CONCAT(DISTINCT c.class_name ORDER BY c.class_name SEPARATOR ', ') AS class_names
        FROM users u
        LEFT JOIN teacher_class tc ON u.user_id = tc.teacher_id
        LEFT JOIN student_class sc ON u.user_id = sc.student_id
        LEFT JOIN parent_student ps ON u.user_id = ps.parent_id
        LEFT JOIN student_class sc2 ON ps.student_id = sc2.student_id
        LEFT JOIN classes c ON c.class_id = COALESCE(tc.class_id, sc.class_id, sc2.class_id)";

$conditions = [];
$params = [];
$types = '';

if ($class_id_filter) {
    $conditions[] = '(tc.class_id = ? OR sc.class_id = ? OR sc2.class_id = ?)';
    array_push($params, $class_id_filter, $class_id_filter, $class_id_filter);
    $types .= 'iii';
}
if ($role_filter !== '') {
    $conditions[] = 'u.role = ?';
    $params[] = $role_filter;
    $types .= 's';
}
if ($conditions) {
    $sql .= ' WHERE ' . implode(' AND ', $conditions);
}
$sql .= " GROUP BY u.user_id ORDER BY FIELD(u.role, 'admin', 'teacher', 'student', 'parent'), u.username";

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$users_result = $stmt->get_result();

page_header(t('admin.users'), [
    ['href' => 'admin_dashboard.php', 'icon' => 'shield_person', 'label' => t('nav.panel')],
]);
?>
<div class="card">
    <h1><?= e(t('admin.users')) ?></h1>

    <?php if ($success_message !== null): ?>
        <p class="success-message"><?= e($success_message) ?></p>
    <?php endif; ?>
    <?php if ($error_message !== null): ?>
        <p class="error"><?= e($error_message) ?></p>
    <?php endif; ?>

    <form method="GET" style="flex-direction: row; flex-wrap: wrap; align-items: flex-end; gap: 1rem;">
        <div class="form-row" style="flex: 1; min-width: 160px;">
            <label for="class_id"><?= e(t('users.filter_class')) ?></label>
            <select name="class_id" id="class_id" onchange="this.form.submit()">
                <option value=""><?= e(t('users.all_classes')) ?></option>
                <?php while ($class = $classes_result->fetch_assoc()): ?>
                    <option value="<?= (int) $class['class_id'] ?>" <?= $class_id_filter === (int) $class['class_id'] ? 'selected' : '' ?>>
                        <?= e($class['class_name']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-row" style="flex: 1; min-width: 160px;">
            <label for="role"><?= e(t('users.filter_role')) ?></label>
            <select name="role" id="role" onchange="this.form.submit()">
                <option value=""><?= e(t('users.all_roles')) ?></option>
                <?php foreach ($roles_shqip as $role_key => $role_name): ?>
                    <option value="<?= $role_key ?>" <?= $role_filter === $role_key ? 'selected' : '' ?>>
                        <?= e($role_name) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <a class="btn" href="add_users.php" style="text-decoration: none;">
            <span class="material-symbols-outlined">person_add</span> <?= e(t('users.add')) ?>
        </a>
    </form>

    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th><?= e(t('users.user')) ?></th>
                    <th><?= e(t('users.role')) ?></th>
                    <th><?= e(t('users.class')) ?></th>
                    <th><?= e(t('users.actions')) ?></th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $users_result->fetch_assoc()): ?>
                    <tr>
                        <td style="text-align: left; font-weight: 500;"><?= e($user['username']) ?></td>
                        <td><span class="role-badge"><?= e($roles_shqip[$user['role']] ?? $user['role']) ?></span></td>
                        <td><?= $user['class_names'] !== null ? e($user['class_names']) : '<span class="muted">&mdash;</span>' ?></td>
                        <td>
                            <div class="actions-cell">
                                <a class="btn btn-small" href="edit_users.php?user_id=<?= (int) $user['user_id'] ?>" style="text-decoration: none;"><?= e(t('users.edit')) ?></a>
                                <?php if ((int) $user['user_id'] !== current_user_id()): ?>
                                    <form method="POST" onsubmit="return confirm(<?= e(json_encode(sprintf(t('users.confirm_delete'), $user['username']), JSON_UNESCAPED_UNICODE)) ?>);" style="display: inline;">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="user_id" value="<?= (int) $user['user_id'] ?>">
                                        <button type="submit" name="delete_user" class="btn btn-small btn-danger"><?= e(t('users.delete')) ?></button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<?php page_footer(); ?>
