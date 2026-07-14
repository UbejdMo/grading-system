<?php
/**
 * Motori qendror i llogaritjes së notave.
 *
 * Rrjedha: nota ditore (vetëvlerësim i aprovuar nga mësuesi)
 *   -> mesatarja mujore  (mesatarja e notave ditore të muajit kalendarik)
 *   -> nota e gjysmëvjetorit (mesatarja e mesatareve mujore)
 *   -> nota vjetore (mesatarja e dy gjysmëvjetorëve).
 */

require_once __DIR__ . '/../config.php';

const GRADE_POINTS = ['A' => 5, 'B' => 4, 'C' => 3, 'D' => 2, 'E' => 1];

const ALBANIAN_MONTHS = [
    1 => 'Janar', 2 => 'Shkurt', 3 => 'Mars', 4 => 'Prill', 5 => 'Maj', 6 => 'Qershor',
    7 => 'Korrik', 8 => 'Gusht', 9 => 'Shtator', 10 => 'Tetor', 11 => 'Nëntor', 12 => 'Dhjetor',
];

/** Konverton mesataren numerike (1-5) në notë shkronjë me rrumbullakim standard. */
function points_to_letter(float $avg): string
{
    if ($avg >= 4.5) return 'A';
    if ($avg >= 3.5) return 'B';
    if ($avg >= 2.5) return 'C';
    if ($avg >= 1.5) return 'D';
    return 'E';
}

/**
 * Kufijtë e vitit aktual shkollor (Shtator - Qershor).
 * P.sh. në tetor 2025 kthen [2025-09-01, 2026-06-30].
 */
function school_year_bounds(?int $today_ts = null): array
{
    $today_ts = $today_ts ?? time();
    $year = (int) date('Y', $today_ts);
    $month = (int) date('n', $today_ts);
    $start_year = ($month >= 9) ? $year : $year - 1;
    return [sprintf('%d-09-01', $start_year), sprintf('%d-06-30', $start_year + 1)];
}

/**
 * Mesataret mujore të një nxënësi për vitin aktual shkollor.
 * Llogariten vetëm nga notat e aprovuara.
 *
 * @return array [subject_id][muaji kalendarik] => mesatarja numerike (float)
 */
function monthly_averages(mysqli $conn, int $student_id): array
{
    [$from, $to] = school_year_bounds();

    $stmt = $conn->prepare(
        "SELECT subject_id, MONTH(grade_date) AS m, grade
         FROM self_grades
         WHERE student_id = ? AND is_approved = 1 AND grade_date BETWEEN ? AND ?"
    );
    $stmt->bind_param('iss', $student_id, $from, $to);
    $stmt->execute();
    $result = $stmt->get_result();

    $sums = [];
    $counts = [];
    while ($row = $result->fetch_assoc()) {
        $points = GRADE_POINTS[$row['grade']] ?? null;
        if ($points === null) {
            continue;
        }
        $subject = (int) $row['subject_id'];
        $month = (int) $row['m'];
        $sums[$subject][$month] = ($sums[$subject][$month] ?? 0) + $points;
        $counts[$subject][$month] = ($counts[$subject][$month] ?? 0) + 1;
    }

    $averages = [];
    foreach ($sums as $subject => $months) {
        foreach ($months as $month => $sum) {
            $averages[$subject][$month] = $sum / $counts[$subject][$month];
        }
    }
    return $averages;
}

/**
 * Nota e gjysmëvjetorit: mesatarja e mesatareve mujore të muajve përkatës.
 *
 * @param array $subject_months [muaji] => mesatarja mujore për një lëndë
 * @param int[] $semester_months p.sh. SEMESTER_1_MONTHS
 */
function semester_average(array $subject_months, array $semester_months): ?float
{
    $values = [];
    foreach ($semester_months as $month) {
        if (isset($subject_months[$month])) {
            $values[] = $subject_months[$month];
        }
    }
    return $values ? array_sum($values) / count($values) : null;
}

/** Nota vjetore: mesatarja e dy gjysmëvjetorëve (ose vetëm njëri nëse tjetri mungon). */
function annual_average(?float $sem1, ?float $sem2): ?float
{
    if ($sem1 !== null && $sem2 !== null) {
        return ($sem1 + $sem2) / 2;
    }
    return $sem1 ?? $sem2;
}

/** Formaton mesataren si "B (3.75)" ose kthen null nëse s'ka notë. */
function format_average(?float $avg): ?string
{
    if ($avg === null) {
        return null;
    }
    return points_to_letter($avg) . ' (' . number_format($avg, 2) . ')';
}
