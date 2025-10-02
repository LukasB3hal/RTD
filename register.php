<?php
// Zajištění spuštění souboru na připojení k databazi
require_once 'config.php';


// Zachycení metody POST z html >> "<form method="post" action="">" a údajů z daného formuláře
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Základní validace – rozšiř podle potřeby
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    $errors = [];

    if ($username === '' || $email === '' || $password === '') {
        $errors[] = "Vyplňte všechny povinné položky.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Neplatný e-mail.";
    }
    if ($password !== $password2) {
        $errors[] = "Hesla se neshodují.";
    }
    if (strlen($password) < 0) {
        $errors[] = "Heslo musí mít alespoň 8 znaků.";
    }

    if (empty($errors)) {
        // Hash hesla
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // Prepared statement pro vložení uživatele
        $stmt = $mysqli->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
        if (!$stmt) {
            $errors[] = "Chyba přípravy dotazu.";
        } else {
            $stmt->bind_param('sss', $username, $email, $hash);
            if ($stmt->execute()) {
                // Úspěch — můžeš přesměrovat na přihlášení
                header('Location: login.php');
                exit;
            } else {
                // zpracuj duplicity (username/email unique)
                if ($mysqli->errno === 1062) {
                    $errors[] = "Uživatel nebo e-mail již existuje.";
                } else {
                    $errors[] = "Chyba při registraci.";
                }
            }
            $stmt->close();
        }
    }
}
?>
<!-- jednoduchý HTML formulář -->
<!doctype html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <meta charset="utf-8"><title>Registrace</title>
</head>
<body>
    <div class="stred" style="color:white">
<h1>Registrace</h1>
<?php if (!empty($errors)): ?>
  <ul style="color: red;">
    <?php foreach ($errors as $e) echo "<li>".htmlspecialchars($e)."</li>"; ?>
  </ul>
<?php endif; ?>
<form method="post" action="">
  <label>Uživatelské jméno: <input name="username" required></label><br>
  <label>E-mail: <input name="email" type="email" required></label><br>
  <label>Heslo: <input name="password" type="password" required></label><br>
  <label>Heslo znovu: <input name="password2" type="password" required></label><br>
  <button type="submit">Registrovat</button>
</form>
    </div>
</body>
</html>
