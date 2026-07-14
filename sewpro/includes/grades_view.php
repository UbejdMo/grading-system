<?php
/**
 * Pamja e përbashkët e notave të një nxënësi (mujore, gjysmëvjetore, vjetore).
 * Përdoret nga moduli i mësuesit dhe ai i prindit.
 */

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/grading.php';

/**
 * Tabela e një gjysmëvjetori: mesatarja mujore për çdo lëndë + nota e gjysmëvjetorit.
 *
 * @param array $subjects [subject_id => subject_name]
 * @param array $monthly  rezultati i monthly_averages()
 * @param int[] $months   muajt e gjysmëvjetorit
 */
function render_semester_table(array $subjects, array $monthly, array $months, string $heading): void
{
    ?>
    <h3><?= e($heading) ?></h3>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Lënda</th>
                    <?php foreach ($months as $m): ?>
                        <th><?= e(ALBANIAN_MONTHS[$m]) ?></th>
                    <?php endforeach; ?>
                    <th>Nota e gjysmëvjetorit</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($subjects as $subject_id => $subject_name): ?>
                    <?php $subject_months = $monthly[$subject_id] ?? []; ?>
                    <tr>
                        <td class="subject-name"><?= e($subject_name) ?></td>
                        <?php foreach ($months as $m): ?>
                            <td>
                                <?php if (isset($subject_months[$m])): ?>
                                    <span class="grade-badge grade-<?= strtolower(points_to_letter($subject_months[$m])) ?>">
                                        <?= e(format_average($subject_months[$m])) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="muted">&mdash;</span>
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                        <td>
                            <?php $sem = semester_average($subject_months, $months); ?>
                            <?php if ($sem !== null): ?>
                                <span class="grade-badge grade-<?= strtolower(points_to_letter($sem)) ?>">
                                    <?= e(format_average($sem)) ?>
                                </span>
                            <?php else: ?>
                                <span class="muted">Nuk ka notë</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}

/** Pamja e plotë: dy gjysmëvjetorët + nota vjetore për çdo lëndë. */
function render_student_grades(mysqli $conn, int $student_id): void
{
    $subjects = [];
    $result = $conn->query('SELECT subject_id, subject_name FROM subjects ORDER BY subject_name');
    while ($row = $result->fetch_assoc()) {
        $subjects[(int) $row['subject_id']] = $row['subject_name'];
    }

    $monthly = monthly_averages($conn, $student_id);

    render_semester_table($subjects, $monthly, SEMESTER_1_MONTHS, 'Gjysmëvjetori i parë (Shtator - Dhjetor)');
    render_semester_table($subjects, $monthly, SEMESTER_2_MONTHS, 'Gjysmëvjetori i dytë (Janar - Qershor)');
    ?>
    <h3>Nota vjetore</h3>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Lënda</th>
                    <th>Gjysmëvjetori I</th>
                    <th>Gjysmëvjetori II</th>
                    <th>Nota vjetore</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($subjects as $subject_id => $subject_name): ?>
                    <?php
                    $subject_months = $monthly[$subject_id] ?? [];
                    $sem1 = semester_average($subject_months, SEMESTER_1_MONTHS);
                    $sem2 = semester_average($subject_months, SEMESTER_2_MONTHS);
                    $annual = annual_average($sem1, $sem2);
                    ?>
                    <tr>
                        <td class="subject-name"><?= e($subject_name) ?></td>
                        <td><?= $sem1 !== null ? '<span class="grade-badge grade-' . strtolower(points_to_letter($sem1)) . '">' . e(format_average($sem1)) . '</span>' : '<span class="muted">&mdash;</span>' ?></td>
                        <td><?= $sem2 !== null ? '<span class="grade-badge grade-' . strtolower(points_to_letter($sem2)) . '">' . e(format_average($sem2)) . '</span>' : '<span class="muted">&mdash;</span>' ?></td>
                        <td><?= $annual !== null ? '<span class="grade-badge grade-' . strtolower(points_to_letter($annual)) . '">' . e(format_average($annual)) . '</span>' : '<span class="muted">Nuk ka notë</span>' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}
