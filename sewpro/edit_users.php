<?php
require_once __DIR__ . '/includes/layout.php';
require_role('admin');

$success_message = null;
$error_message = null;

// Modifikimi i të dhënave të përdoruesit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user'])) {
    csrf_check();

    $user_id = (int) $_POST['user_id'];
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    if ($username === '' || !in_array($role, ['admin', 'teacher', 'student', 'parent'], true)) {
        $error_message = t('edit.err_fields');
    } else {
        try {
            // Fjalëkalimi ndryshohet vetëm nëse është shkruar një i ri
            if ($password !== '') {
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare('UPDATE users SET username = ?, password = ?, role = ? WHERE user_id = ?');
                $stmt->bind_param('sssi', $username, $password_hash, $role, $user_id);
            } else {
                $stmt = $conn->prepare('UPDATE users SET username = ?, role = ? WHERE user_id = ?');
                $stmt->bind_param('ssi', $username, $role, $user_id);
            }
            $stmt->execute();

            // Përditëso klasën për mësues/nxënës
            if (($role === 'teacher' || $role === 'student') && !empty($_POST['class_id'])) {
                $class_id = (int) $_POST['class_id'];
                $table = $role === 'teacher' ? 'teacher_class' : 'student_class';
                $column = $role === 'teacher' ? 'teacher_id' : 'student_id';
                $conn->query("DELETE FROM $table WHERE $column = " . $user_id);
                $stmt = $conn->prepare("INSERT INTO $table ($column, class_id) VALUES (?, ?)");
                $stmt->bind_param('ii', $user_id, $class_id);
                $stmt->execute();
            }

            $success_message = t('edit.success');
        } catch (mysqli_sql_exception $e) {
            $error_message = $e->getCode() === 1062
                ? t('add.err_exists')
                : t('edit.err_save');
        }
    }
    $_GET['user_id'] = $user_id;
}

// Heqja e lidhjes prind-nxënës
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_child'])) {
    csrf_check();
    $parent_id = (int) $_POST['parent_id'];
    $student_id = (int) $_POST['student_id'];

    $stmt = $conn->prepare('DELETE FROM parent_student WHERE parent_id = ? AND student_id = ?');
    $stmt->bind_param('ii', $parent_id, $student_id);
    $stmt->execute();
    $success_message = t('edit.unlink_success');
    $_GET['user_id'] = $parent_id;
}

// Shtimi i lidhjes prind-nxënës
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_child'])) {
    csrf_check();
    $parent_id = (int) $_POST['parent_id'];
    $student_id = (int) $_POST['new_student_id'];

    if ($student_id > 0) {
        try {
            $stmt = $conn->prepare('INSERT INTO parent_student (parent_id, student_id) VALUES (?, ?)');
            $stmt->bind_param('ii', $parent_id, $student_id);
            $stmt->execute();
            $success_message = t('edit.link_success');
        } catch (mysqli_sql_exception $e) {
            $error_message = t('edit.err_linked');
        }
    }
    $_GET['user_id'] = $parent_id;
}

// Lista e përdoruesve për përzgjedhje
$users_result = $conn->query("SELECT user_id, username, role FROM users ORDER BY FIELD(role, 'admin', 'teacher', 'student', 'parent'), username");

$selected_user_id = (int) ($_GET['user_id'] ?? 0);
$selected_user = null;
$selected_class_id = 0;
$children = [];
$available_students = [];

if ($selected_user_id) {
    $stmt = $conn->prepare('SELECT user_id, username, role FROM users WHERE user_id = ?');
    $stmt->bind_param('i', $selected_user_id);
    $stmt->execute();
    $selected_user = $stmt->get_result()->fetch_assoc();
}

if ($selected_user) {
    if ($selected_user['role'] === 'teacher' || $selected_user['role'] === 'student') {
        $table = $selected_user['role'] === 'teacher' ? 'teacher_class' : 'student_class';
        $column = $selected_user['role'] === 'teacher' ? 'teacher_id' : 'student_id';
        $stmt = $conn->prepare("SELECT class_id FROM $table WHERE $column = ?");
        $stmt->bind_param('i', $selected_user_id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $selected_class_id = $row ? (int) $row['class_id'] : 0;
    }

    if ($selected_user['role'] === 'parent') {
        $stmt = $conn->prepare(
            'SELECT s.user_id, s.username
             FROM users s
             JOIN parent_student ps ON s.user_id = ps.student_id
             WHERE ps.parent_id = ?
             ORDER BY s.username'
        );
        $stmt->bind_param('i', $selected_user_id);
        $stmt->execute();
        $children = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $available_result = $conn->query(
            "SELECT u.user_id, u.username
             FROM users u
             WHERE u.role = 'student'
               AND u.user_id NOT IN (SELECT student_id FROM parent_student)
             ORDER BY u.username"
        );
        $available_students = $available_result->fetch_all(MYSQLI_ASSOC);
    }
}

$classes = $conn->query('SELECT * FROM classes ORDER BY class_name')->fetch_all(MYSQLI_ASSOC);

page_header(t('edit.title'), [
    ['href' => 'admin_dashboard.php', 'icon' => 'shield_person', 'label' => t('nav.panel')],
    ['href' => 'manage_users.php', 'icon' => 'group', 'label' => t('nav.users')],
]);
?>
<div class="card" style="max-width: 640px; margin-left: auto; margin-right: auto;">
    <h1><?= e(t('edit.title')) ?></h1>

    <?php if ($success_message !== null): ?>
        <p class="success-message"><?= e($success_message) ?></p>
    <?php endif; ?>
    <?php if ($error_message !== null): ?>
        <p class="error"><?= e($error_message) ?></p>
    <?php endif; ?>

    <form method="GET">
        <div class="form-row">
            <label for="user_id"><?= e(t('edit.select_user')) ?></label>
            <select name="user_id" id="user_id" onchange="this.form.submit()">
                <option value=""><?= e(t('edit.select_placeholder')) ?></option>
                <?php while ($user = $users_result->fetch_assoc()): ?>
                    <option value="<?= (int) $user['user_id'] ?>" <?= $selected_user_id === (int) $user['user_id'] ? 'selected' : '' ?>>
                        <?= e($user['username']) ?> (<?= e(t('role.' . $user['role'])) ?>)
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
    </form>

    <?php if ($selected_user): ?>
        <form method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="user_id" value="<?= (int) $selected_user['user_id'] ?>">

            <div class="form-row">
                <label for="username"><?= e(t('add.username')) ?></label>
                <input type="text" name="username" id="username" value="<?= e($selected_user['username']) ?>" required>
            </div>
            <div class="form-row">
                <label for="password"><?= e(t('edit.password_hint')) ?></label>
                <input type="password" name="password" id="password" placeholder="<?= e(t('edit.password_placeholder')) ?>">
            </div>
            <div class="form-row">
                <label for="role"><?= e(t('add.role')) ?></label>
                <select name="role" id="role" required>
                    <?php foreach (['admin', 'teacher', 'student', 'parent'] as $role_key): ?>
                        <option value="<?= $role_key ?>" <?= $selected_user['role'] === $role_key ? 'selected' : '' ?>><?= e(t('role.' . $role_key)) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <?php if ($selected_user['role'] === 'teacher' || $selected_user['role'] === 'student'): ?>
                <div class="form-row">
                    <label for="class_id"><?= e(t('add.class')) ?></label>
                    <select name="class_id" id="class_id">
                        <?php foreach ($classes as $class): ?>
                            <option value="<?= (int) $class['class_id'] ?>" <?= $selected_class_id === (int) $class['class_id'] ? 'selected' : '' ?>>
                                <?= e($class['class_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>

            <button class="admin-button" type="submit" name="edit_user"><?= e(t('edit.save')) ?></button>
        </form>

        <?php if ($selected_user['role'] === 'parent'): ?>
            <h2><?= e(t('edit.linked')) ?></h2>
            <?php if ($children): ?>
                <ul>
                    <?php foreach ($children as $child): ?>
                        <li class="no-style">
                            <?= e($child['username']) ?>
                            <form method="POST" style="display: inline;"
                                onsubmit="return confirm(<?= e(json_encode(sprintf(t('edit.confirm_unlink'), $child['username']), JSON_UNESCAPED_UNICODE)) ?>);">
                                <?= csrf_field() ?>
                                <input type="hidden" name="parent_id" value="<?= (int) $selected_user['user_id'] ?>">
                                <input type="hidden" name="student_id" value="<?= (int) $child['user_id'] ?>">
                                <button type="submit" name="remove_child" class="remove-btn">x</button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="muted"><?= e(t('edit.none_linked')) ?></p>
            <?php endif; ?>

            <?php if ($available_students): ?>
                <form method="POST" style="flex-direction: row; align-items: flex-end; gap: 0.5rem; margin-top: 0.75rem;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="parent_id" value="<?= (int) $selected_user['user_id'] ?>">
                    <div class="form-row" style="flex: 1;">
                        <label for="new_student_id"><?= e(t('edit.link_new')) ?></label>
                        <select name="new_student_id" id="new_student_id">
                            <?php foreach ($available_students as $student): ?>
                                <option value="<?= (int) $student['user_id'] ?>"><?= e($student['username']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button class="admin-button" type="submit" name="add_child"><?= e(t('edit.link')) ?></button>
                </form>
            <?php endif; ?>
        <?php endif; ?>
    <?php endif; ?>
</div>
<?php page_footer(); ?>
