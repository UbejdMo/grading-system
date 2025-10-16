<?php
session_start();
include('db.php'); // Përfshirja e skedarit për lidhjen me bazën e të dhënave

// Ditët e javës në shqip
$days_in_shqip = array(
    'Sunday' => 'e Diel',
    'Monday' => 'e Hënë',
    'Tuesday' => 'e Martë',
    'Wednesday' => 'e Mërkurë',
    'Thursday' => 'e Enjte',
    'Friday' => 'e Premte',
    'Saturday' => 'e Shtunë'
);

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $remember_me = isset($_POST['remember_me']);

    // Verifikimi i përdoruesit dhe fjalëkalimit (pa përdorur hash)
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Përdoruesi ekziston, tani kontrollojmë fjalëkalimin
        $user = $result->fetch_assoc();

        if ($user['password'] === $password) {
            // Fjalëkalimi është i saktë
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];

            // Merr kufizimet e kohës për rolin
            $stmt = $conn->prepare("SELECT * FROM user_time_limits WHERE role = ?");
            $stmt->bind_param("s", $user['role']);
            $stmt->execute();
            $time_result = $stmt->get_result();

            // Merr ditën aktuale dhe kohën aktuale
            $current_day = date('l');
            $current_day_shqip = $days_in_shqip[$current_day];
            $current_time = date('H:i');

            if ($time_result->num_rows > 0) {
                $time_limit = $time_result->fetch_assoc();
                $allowed_days = explode(",", $time_limit['days']);
                if (in_array($current_day, $allowed_days)) {
                    if ($current_time < $time_limit['start_time']) {
                        $error = "Për ditën $current_day_shqip, orari i kyçjes nuk ka filluar. Orari fillon në orën " . $time_limit['start_time'] . ".";
                    } elseif ($current_time > $time_limit['end_time']) {
                        $error = "Për ditën $current_day_shqip, orari i kyçjes ka kaluar. Orari ka përfunduar në orën " . $time_limit['end_time'] . ".";
                    } else {
                        if ($user['role'] == 'admin') {
                            header("Location: admin_dashboard.php");
                        } elseif ($user['role'] == 'teacher') {
                            header("Location: teacher_dashboard.php");
                        } elseif ($user['role'] == 'student') {
                            header("Location: student_dashboard.php");
                        } elseif ($user['role'] == 'parent') {
                            header("Location: parent_dashboard.php");
                        }
                        exit();
                    }
                } else {
                    $error = "Ditën $current_day_shqip ju nuk mund të kyçeni.";
                }
            } else {
                $error = "Ditën $current_day_shqip ju nuk mund të kyçeni.";
            }

            // Handle Remember Me functionality
            if (isset($remember_me)) {
                setcookie('username', $username, time() + (86400 * 30), "/");
                setcookie('password', $password, time() + (86400 * 30), "/");
                $token = bin2hex(random_bytes(16));
                setcookie('remember_token', $token, time() + (86400 * 30), "/");

                $stmt = $conn->prepare("INSERT INTO remember_tokens (user_id, token, expiration) VALUES (?, ?, ?)");
                $expiration = date('Y-m-d H:i:s', strtotime('+30 days'));
                $stmt->execute([$_SESSION['user_id'], $token, $expiration]);
            }
        } else {
            $error = "Fjalëkalimi është gabim.";
        }
    } else {
        $error = "Përdoruesi nuk ekziston.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="login-body">
    <div class="login-wrapper">
    <div class="login-container">
        <h2>Kyçja në sistem</h2>
        <form action="" method="POST">
            <div class="form-group">
                <label for="username">Përdoruesi:</label>
                <input type="text" name="username" id="username"
                    value="<?php echo isset($_COOKIE['username']) ? $_COOKIE['username'] : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Fjalëkalimi:</label>
                <input type="password" name="password" id="password"
                    value="<?php echo isset($_COOKIE['password']) ? $_COOKIE['password'] : ''; ?>" required>
            </div>
            <div class="form-group remember-me">
                <input type="checkbox" name="remember_me" id="remember_me" <?php echo isset($_COOKIE['username']) ? 'checked' : ''; ?>>
                <label for="remember_me">Ruaj llogarinë</label>
            </div>
            <button class="admin-button" type="submit" class="btn">Kyçu</button>
        </form>

        <?php if (isset($error)) { ?>
            <div class="error"><?php echo $error; ?></div>
        <?php } ?>
    </div>
    </div>
</body>

</html>
