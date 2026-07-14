<?php
// Konfigurimi qendror i aplikacionit
date_default_timezone_set('Europe/Tirane');

// Baza e të dhënave
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'sewpro');

// Muajt e vitit shkollor sipas gjysmëvjetorëve.
// Gjysmëvjetori i parë: Shtator - Dhjetor, i dyti: Janar - Qershor.
const SEMESTER_1_MONTHS = [9, 10, 11, 12];
const SEMESTER_2_MONTHS = [1, 2, 3, 4, 5, 6];
