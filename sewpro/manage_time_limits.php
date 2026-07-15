<?php
require_once __DIR__ . '/includes/layout.php';
require_role('admin');

$days_of_week = [];
foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $d) {
    $days_of_week[$d] = t("day.$d");
}

$roles_shqip = [
    'admin' => t('role.admin'),
    'teacher' => t('role.teacher'),
    'student' => t('role.student'),
    'parent' => t('role.parent'),
];

$success_message = null;
$error_message = null;

function sanitize_days(array $days, array $days_of_week): string
{
    $valid = array_intersect($days, array_keys($days_of_week));
    return implode(',', $valid);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $role = $_POST['role'] ?? '';
    $days = sanitize_days($_POST['days'] ?? [], $days_of_week);
    $start_time = $_POST['start_time'] ?? '';
    $end_time = $_POST['end_time'] ?? '';

    if (isset($_POST['add_time_limit']) || isset($_POST['update_time_limit'])) {
        if (!isset($roles_shqip[$role])) {
            $error_message = t('limits.err_role');
        } elseif ($days === '') {
            $error_message = t('limits.err_days');
        } elseif ($start_time === '' || $end_time === '' || $start_time >= $end_time) {
            $error_message = t('limits.err_time');
        } elseif (isset($_POST['add_time_limit'])) {
            $stmt = $conn->prepare('INSERT INTO user_time_limits (role, days, start_time, end_time) VALUES (?, ?, ?, ?)');
            $stmt->bind_param('ssss', $role, $days, $start_time, $end_time);
            $stmt->execute();
            $success_message = t('limits.added');
        } else {
            $limit_id = (int) $_POST['limit_id'];
            $stmt = $conn->prepare('UPDATE user_time_limits SET role = ?, days = ?, start_time = ?, end_time = ? WHERE limit_id = ?');
            $stmt->bind_param('ssssi', $role, $days, $start_time, $end_time, $limit_id);
            $stmt->execute();
            $success_message = t('limits.updated');
        }
    }

    if (isset($_POST['delete_time_limit'])) {
        $limit_id = (int) $_POST['limit_id'];
        $stmt = $conn->prepare('DELETE FROM user_time_limits WHERE limit_id = ?');
        $stmt->bind_param('i', $limit_id);
        $stmt->execute();
        $success_message = t('limits.deleted');
    }
}

// Të dhënat për modifikim
$edit_data = null;
if (isset($_GET['edit'])) {
    $edit_id = (int) $_GET['edit'];
    $stmt = $conn->prepare('SELECT * FROM user_time_limits WHERE limit_id = ?');
    $stmt->bind_param('i', $edit_id);
    $stmt->execute();
    $edit_data = $stmt->get_result()->fetch_assoc();
}

$time_limits = $conn->query('SELECT * FROM user_time_limits ORDER BY limit_id')->fetch_all(MYSQLI_ASSOC);

function day_checkboxes(array $days_of_week, array $selected = []): void
{
    foreach ($days_of_week as $en => $sq) {
        $checked = in_array($en, $selected, true) ? 'checked' : '';
        echo "<div class='checkboxes'><input type='checkbox' name='days[]' id='day_$en' value='$en' $checked><p>" . e($sq) . '</p></div>';
    }
}

page_header(t('limits.title'), [
    ['href' => 'admin_dashboard.php', 'icon' => 'shield_person', 'label' => t('nav.panel')],
]);
?>
<div class="card" style="max-width: 760px; margin-left: auto; margin-right: auto;">
    <h1><?= e(t('limits.title')) ?></h1>

    <?php if ($success_message !== null): ?>
        <p class="success-message"><?= e($success_message) ?></p>
    <?php endif; ?>
    <?php if ($error_message !== null): ?>
        <p class="error"><?= e($error_message) ?></p>
    <?php endif; ?>

    <?php if ($edit_data): ?>
        <h2><?= e(t('limits.edit')) ?></h2>
        <form method="POST" action="manage_time_limits.php">
            <?= csrf_field() ?>
            <input type="hidden" name="limit_id" value="<?= (int) $edit_data['limit_id'] ?>">
            <div class="form-row">
                <label for="role"><?= e(t('limits.role')) ?></label>
                <select name="role" required>
                    <?php foreach ($roles_shqip as $role_key => $role_name): ?>
                        <option value="<?= $role_key ?>" <?= $edit_data['role'] === $role_key ? 'selected' : '' ?>><?= $role_name ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <label><?= e(t('limits.days')) ?></label>
            <?php day_checkboxes($days_of_week, explode(',', $edit_data['days'])); ?>
            <div class="form-row">
                <label for="start_time"><?= e(t('limits.start')) ?></label>
                <input type="time" name="start_time" value="<?= e(substr($edit_data['start_time'], 0, 5)) ?>" required>
            </div>
            <div class="form-row">
                <label for="end_time"><?= e(t('limits.end')) ?></label>
                <input type="time" name="end_time" value="<?= e(substr($edit_data['end_time'], 0, 5)) ?>" required>
            </div>
            <button class="admin-button" type="submit" name="update_time_limit"><?= e(t('limits.save_btn')) ?></button>
        </form>
    <?php else: ?>
        <h2><?= e(t('limits.add')) ?></h2>
        <form method="POST" action="manage_time_limits.php">
            <?= csrf_field() ?>
            <div class="form-row">
                <label for="role"><?= e(t('limits.role')) ?></label>
                <select class="select-role" name="role" required>
                    <?php foreach ($roles_shqip as $role_key => $role_name): ?>
                        <option value="<?= $role_key ?>"><?= $role_name ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <label><?= e(t('limits.days')) ?></label>
            <?php day_checkboxes($days_of_week); ?>
            <div class="form-row">
                <label for="start_time"><?= e(t('limits.start')) ?></label>
                <input type="time" name="start_time" required>
            </div>
            <div class="form-row">
                <label for="end_time"><?= e(t('limits.end')) ?></label>
                <input type="time" name="end_time" required>
            </div>
            <button class="admin-button" type="submit" name="add_time_limit"><?= e(t('limits.add_btn')) ?></button>
        </form>
    <?php endif; ?>

    <h2><?= e(t('limits.current')) ?></h2>
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th><?= e(t('users.role')) ?></th>
                    <th><?= e(t('limits.days')) ?></th>
                    <th><?= e(t('limits.start_col')) ?></th>
                    <th><?= e(t('limits.end_col')) ?></th>
                    <th><?= e(t('users.actions')) ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($time_limits as $limit): ?>
                    <tr>
                        <td><span class="role-badge"><?= e($roles_shqip[$limit['role']] ?? $limit['role']) ?></span></td>
                        <td style="text-align: left;">
                            <?php
                            $translated = array_map(
                                fn($d) => $days_of_week[trim($d)] ?? trim($d),
                                explode(',', $limit['days'])
                            );
                            echo e(implode(', ', $translated));
                            ?>
                        </td>
                        <td><?= e(substr($limit['start_time'], 0, 5)) ?></td>
                        <td><?= e(substr($limit['end_time'], 0, 5)) ?></td>
                        <td>
                            <div class="actions-cell">
                                <a class="btn btn-small" style="text-decoration: none;" href="?edit=<?= (int) $limit['limit_id'] ?>"><?= e(t('users.edit')) ?></a>
                                <form method="POST" onsubmit="return confirm(<?= e(json_encode(t('limits.confirm_delete'), JSON_UNESCAPED_UNICODE)) ?>);">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="limit_id" value="<?= (int) $limit['limit_id'] ?>">
                                    <button class="btn btn-small btn-danger" type="submit" name="delete_time_limit"><?= e(t('users.delete')) ?></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php page_footer(); ?>
