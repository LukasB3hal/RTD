<?php

//==========================================================================
//==========================================================================
//==========================================================================
// Pokud se tento soubor zahrne před stránkou, stránka bude přístupná pouze pro přihlášené uživatele
//==========================================================================
//==========================================================================
//==========================================================================






// auth.php — include/hook do chráněných stránek
require_once 'config.php';

function is_logged_in(): bool {
    return !empty($_SESSION['user_id']);
}

// Pokud není přihlášen, přesměruj na login
if (!is_logged_in()) {
    // Volitelně ulož požadovanou URL do SESSION, aby se po přihlášení dal uživatel vrátit
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    header('Location: login.php');
    exit;
}
