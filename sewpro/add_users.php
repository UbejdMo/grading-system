<?php
require_once __DIR__ . '/includes/layout.php';
require_role('admin');

$success_message = null;
$error_message = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    csrf_check();

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    if ($username === '' || $password === '' || !in_array($role, ['teacher', 'student', 'parent'], true)) {
        $error_message = 'Plotësoni të gjitha fushat dhe zgjidhni një rol valid.';
    } elseif (($role === 'teacher' || $role === 'student') && empty($_POST['class_id'])) {
        $error_message = 'Zgjidhni klasën për mësuesin/nxënësin.';
    } else {
        try {
            $conn->begin_transaction();

            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare('INSERT INTO users (username, password, role) VALUES (?, ?, ?)');
            $stmt->bind_param('sss', $username, $password_hash, $role);
            $stmt->execute();
            $user_id = $stmt->insert_id;

            if ($role === 'parent' && !empty($_POST['student_ids'])) {
                $stmt = $conn->prepare('INSERT INTO parent_student (parent_id, student_id) VALUES (?, ?)');
                foreach (explode(',', $_POST['student_ids']) as $student_id) {
                    $student_id = (int) $student_id;
                    if ($student_id > 0) {
                        $stmt->bind_param('ii', $user_id, $student_id);
                        $stmt->execute();
                    }
                }
            }

            if ($role === 'teacher' || $role === 'student') {
                $class_id = (int) $_POST['class_id'];
                $table = $role === 'teacher' ? 'teacher_class' : 'student_class';
                $column = $role === 'teacher' ? 'teacher_id' : 'student_id';
                $stmt = $conn->prepare("INSERT INTO $table ($column, class_id) VALUES (?, ?)");
                $stmt->bind_param('ii', $user_id, $class_id);
                $stmt->execute();
            }

            $conn->commit();
            $success_message = "Përdoruesi \"$username\" u shtua me sukses!";
        } catch (mysqli_sql_exception $e) {
            $conn->rollback();
            $error_message = $e->getCode() === 1062
                ? 'Ky emër përdoruesi ekziston tashmë.'
                : 'Gabim gjatë ruajtjes së përdoruesit.';
        }
    }
}

$classes_result = $conn->query('SELECT * FROM classes ORDER BY class_name');
$classes = $classes_result->fetch_all(MYSQLI_ASSOC);

// Nxënësit që nuk kanë ende prind të lidhur
$students_result = $conn->query(
    "SELECT u.user_id, u.username, sc.class_id
     FROM users u
     JOIN student_class sc ON u.user_id = sc.student_id
     WHERE u.role = 'student'
       AND u.user_id NOT IN (SELECT student_id FROM parent_student)
     ORDER BY u.username"
);

page_header('Shto Përdorues', [
    ['href' => 'admin_dashboard.php', 'icon' => 'shield_person', 'label' => 'Paneli'],
    ['href' => 'manage_users.php', 'icon' => 'group', 'label' => 'Përdoruesit'],
]);
?>
<div class="card" style="max-width: 640px; margin-left: auto; margin-right: auto;">
    <h1>Shto Përdorues</h1>

    <?php if ($success_message !== null): ?>
        <p class="success-message"><?= e($success_message) ?></p>
    <?php endif; ?>
    <?php if ($error_message !== null): ?>
        <p class="error"><?= e($error_message) ?></p>
    <?php endif; ?>

    <form method="POST">
        <?= csrf_field() ?>
        <div class="form-row">
            <label for="role">Roli:</label>
            <select name="role" id="role" onchange="updateForm()" required>
                <option value="">Zgjedh Rolin</option>
                <option value="teacher">Mësues</option>
                <option value="student">Nxënës</option>
                <option value="parent">Prind</option>
            </select>
        </div>

        <div class="form-row">
            <label for="username">Përdoruesi:</label>
            <input placeholder="Përdoruesi" type="text" name="username" id="username" required>
        </div>
        <div class="form-row">
            <label for="password">Fjalëkalimi:</label>
            <input placeholder="Fjalëkalimi" type="password" name="password" id="password" required>
        </div>

        <!-- Për prindër: lidhja me nxënës -->
        <div id="student_select">
            <div class="form-row">
                <label for="filter_class_parent">Filtro sipas klasës:</label>
                <select id="filter_class_parent" onchange="filterParentStudents()">
                    <option value="">Të gjitha klasat</option>
                    <?php foreach ($classes as $class): ?>
                        <option value="<?= (int) $class['class_id'] ?>"><?= e($class['class_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <label>Nxënësit e ndërlidhur:</label>
            <div id="students_list"></div>

            <div class="student-picker">
                <?php while ($student = $students_result->fetch_assoc()): ?>
                    <div class="student-item" data-class="<?= (int) $student['class_id'] ?>">
                        <span><?= e($student['username']) ?></span>
                        <button type="button" class="btn btn-small"
                            onclick="addStudentToParent(<?= (int) $student['user_id'] ?>, this)">+</button>
                    </div>
                <?php endwhile; ?>
            </div>

            <input type="hidden" name="student_ids" id="selected_student_ids">
        </div>

        <!-- Për mësues/nxënës: klasa -->
        <div id="class_select">
            <div class="form-row">
                <label for="class_id">Klasa:</label>
                <select name="class_id" id="class_id">
                    <?php foreach ($classes as $class): ?>
                        <option value="<?= (int) $class['class_id'] ?>"><?= e($class['class_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <button class="admin-button" type="submit" name="add_user">Shto Përdorues</button>
    </form>
</div>

<script>
    function filterParentStudents() {
        const selectedClass = document.getElementById('filter_class_parent').value;
        document.querySelectorAll('.student-item').forEach(item => {
            item.style.display = (selectedClass === '' || item.dataset.class === selectedClass) ? 'flex' : 'none';
        });
    }

    function addStudentToParent(studentId, button) {
        const hidden = document.getElementById('selected_student_ids');
        const ids = hidden.value ? hidden.value.split(',') : [];
        if (ids.includes(String(studentId))) return;

        ids.push(String(studentId));
        hidden.value = ids.join(',');

        const name = button.parentElement.querySelector('span').textContent;
        const chip = document.createElement('div');
        chip.textContent = name;
        document.getElementById('students_list').appendChild(chip);
        button.parentElement.style.display = 'none';
    }

    function updateForm() {
        const role = document.getElementById('role').value;
        document.getElementById('student_select').style.display = (role === 'parent') ? 'block' : 'none';
        document.getElementById('class_select').style.display = (role === 'teacher' || role === 'student') ? 'block' : 'none';
    }
</script>
<?php page_footer(); ?>
