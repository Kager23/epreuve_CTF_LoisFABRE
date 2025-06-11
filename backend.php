<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Méthode non autorisée";
    exit;
}

$action = $_POST['action'] ?? '';

$users = [
    'bob' => 'ilovejamaica',
    'root' => 'marley1977'
];

switch($action) {
    case 'verify_password':
        $userKey = $_POST['user'] ?? '';
        $password = $_POST['password'] ?? '';

        // Remplacement pour attendre les valeurs exactes
        if ($userKey === 'awaiting_bob_password') $userKey = 'bob';
        if ($userKey === 'awaiting_root_password') $userKey = 'root';

        if (isset($users[$userKey]) && $users[$userKey] === $password) {
            $_SESSION['user'] = $userKey;
            echo "OK";
        } else {
            echo "NO";
        }
        break;

    case 'get_flag':
        if (isset($_SESSION['user']) && $_SESSION['user'] === 'root') {
            echo "Félicitations ! Voici ton flag:\nctf{B0b_L0ves_Reggae_Music}";
        } else {
            http_response_code(403);
            echo "Accès refusé.";
        }
        break;

    default:
        http_response_code(400);
        echo "Action inconnue";
        break;
}
