<?php
require_once __DIR__ . '/includes/layout.php';
require_role('admin');

$success_message = null;
$error_message = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    try {
        if (isset($_POST['add_class'])) {
            $class_name = trim($_POST['class_name'] ?? '');
            if ($class_name === '') {
                $error_message = t('classes.err_name');
            } else {
                $stmt = $conn->prepare('INSERT INTO classes (class_name) VALUES (?)');
                $stmt->bind_param('s', $class_name);
                $stmt->execute();
                $success_message = sprintf(t('classes.added'), $class_name);
            }
        }

        if (isset($_POST['edit_class'])) {
            $class_id = (int) $_POST['edit_class_id'];
            $new_class_name = trim($_POST['new_class_name'] ?? '');
            if ($new_class_name === '') {
                $error_message = t('classes.err_newname');
            } else {
                $stmt = $conn->prepare('UPDATE classes SET class_name = ? WHERE class_id = ?');
                $stmt->bind_param('si', $new_class_name, $class_id);
                $stmt->execute();
                $success_message = t('classes.updated');
            }
        }

        if (isset($_POST['delete_class'])) {
            $class_id = (int) $_POST['delete_class_id'];
            $stmt = $conn->prepare('DELETE FROM classes WHERE class_id = ?');
            $stmt->bind_param('i', $class_id);
            $stmt->execute();
            $success_message = t('classes.deleted');
        }
    } catch (mysqli_sql_exception $e) {
        $error_message = $e->getCode() === 1062
            ? t('classes.err_exists')
            : t('classes.err_save');
    }
}

// Klasat me numrin e nxënësve dhe mësuesin e caktuar
$classes = $conn->query(
    "SELECT c.class_id, c.class_name,
            COUNT(DISTINCT sc.student_id) AS student_count,
            GROUP_CONCAT(DISTINCT t.username ORDER BY t.username SEPARATOR ', ') AS teachers
     FROM classes c
     LEFT JOIN student_class sc ON sc.class_id = c.class_id
     LEFT JOIN teacher_class tc ON tc.class_id = c.class_id
     LEFT JOIN users t ON t.user_id = tc.teacher_id
     GROUP BY c.class_id
     ORDER BY c.class_name"
)->fetch_all(MYSQLI_ASSOC);

page_header(t('classes.title'), [
    ['href' => 'admin_dashboard.php', 'icon' => 'shield_person', 'label' => t('nav.panel')],
]);
?>
<div class="card" style="max-width: 760px; margin-left: auto; margin-right: auto;">
    <h1><?= e(t('classes.title')) ?></h1>

    <?php if ($success_message !== null): ?>
        <p class="success-message"><?= e($success_message) ?></p>
    <?php endif; ?>
    <?php if ($error_message !== null): ?>
        <p class="error"><?= e($error_message) ?></p>
    <?php endif; ?>

    <h2><?= e(t('classes.add')) ?></h2>
    <form method="POST" style="flex-direction: row; align-items: flex-end; gap: 0.5rem; flex-wrap: wrap;">
        <?= csrf_field() ?>
        <div class="form-row" style="flex: 1; min-width: 200px;">
            <label for="class_name"><?= e(t('classes.name_label')) ?></label>
            <input type="text" name="class_name" id="class_name" required>
        </div>
        <button class="admin-button" type="submit" name="add_class"><?= e(t('classes.add_btn')) ?></button>
    </form>

    <h2><?= e(t('classes.existing')) ?></h2>
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th><?= e(t('classes.class')) ?></th>
                    <th><?= e(t('classes.teacher')) ?></th>
                    <th><?= e(t('classes.students')) ?></th>
                    <th><?= e(t('users.actions')) ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($classes as $class): ?>
                    <tr>
                        <td style="font-weight: 500;"><?= e($class['class_name']) ?></td>
                        <td><?= $class['teachers'] !== null ? e($class['teachers']) : '<span class="muted">&mdash;</span>' ?></td>
                        <td><?= (int) $class['student_count'] ?></td>
                        <td>
                            <div class="actions-cell">
                                <form method="POST" style="flex-direction: row; gap: 0.4rem;">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="edit_class_id" value="<?= (int) $class['class_id'] ?>">
                                    <input type="text" name="new_class_name" value="<?= e($class['class_name']) ?>" required
                                        style="width: 9rem; padding: 0.3rem 0.5rem;">
                                    <button class="btn btn-small" type="submit" name="edit_class"><?= e(t('classes.save')) ?></button>
                                </form>
                                <form method="POST"
                                    onsubmit="return confirm(<?= e(json_encode(t('classes.confirm_delete'), JSON_UNESCAPED_UNICODE)) ?>);">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="delete_class_id" value="<?= (int) $class['class_id'] ?>">
                                    <button class="btn btn-small btn-danger" type="submit" name="delete_class"><?= e(t('users.delete')) ?></button>
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
