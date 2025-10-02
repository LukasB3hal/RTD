<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameOrEmail = trim($_POST['username_or_email'] ?? '');
    $password = $_POST['password'] ?? '';

    $errors = [];
    if ($usernameOrEmail === '' || $password === '') {
        $errors[] = "Vyplňte všechny položky.";
    }

    if (empty($errors)) {
        // Vyhledáme uživatele podle username nebo email – prepared statement
        $stmt = $mysqli->prepare("SELECT id, username, password_hash FROM users WHERE username = ? OR email = ? LIMIT 1");
        if (!$stmt) {
            $errors[] = "Chyba DB dotazu.";
        } else {
            $stmt->bind_param('ss', $usernameOrEmail, $usernameOrEmail);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($user = $result->fetch_assoc()) {
                // Ověření hesla
                if (password_verify($password, $user['password_hash'])) {
                    // Přihlášení úspěšné: bezpečné uložení info do session
                    session_regenerate_id(true); // prevence session fixation
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    // Můžeš uložit i timestamp přihlášení apod.
                    header('Location: index.php');
                    exit;
                } else {
                    $errors[] = "Špatné uživatelské jméno nebo heslo.";
                }
            } else {
                $errors[] = "Špatné uživatelské jméno nebo heslo.";
            }
            $stmt->close();
        }
    }
}
?>
<!doctype html>
<html>
<head>
<link rel="stylesheet" href="style.css">
<meta charset="utf-8"><title>Přihlášení</title>

</head>
<body>
    <div class="stred" style="color:white;">
<h1>Přihlášení</h1>
<?php if (!empty($_GET['registered'])): ?>
  <p style="color:green;">Registrace dokončena — můžete se přihlásit.</p>
<?php endif; ?>
<?php if (!empty($errors)): ?>
  <ul style="color:red;">
    <?php foreach ($errors as $e) echo "<li>".htmlspecialchars($e)."</li>"; ?>
  </ul>
<?php endif; ?>
<form method="post" action="">
  <label>Uživatelské jméno nebo e-mail: <input name="username_or_email" required></label><br>
  <label>Heslo: <input name="password" type="password" required></label><br>
  <button type="submit">Přihlásit</button>
</form>
<br>
<a href="register.php">Nemám účet :/</a>
</div>
</body>
</html>
