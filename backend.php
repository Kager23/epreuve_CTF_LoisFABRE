<?php
$bob_password = 'ilovejamaica';
$root_password = 'marley1977';
$flag = 'ctf{B0b_L0ves_Reggae_Music}';

$notes = "Notes de Bob:\n- MDP : ilovejamaica\n- Document important : /secret/flag.txt\n- BABYLONE !\n- Penser a changer la politique de mot de passe";
$bash_history = "sudo apt-get install reggae-music\necho 'Favorite artist: Bob M.' >> ~/.profile\necho 'First album year: 1977' >> ~/.profile\nsource ~/.profile\nclear";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $user = $_POST['user'] ?? '';

    if ($action === 'verify_password') {
        $password = $_POST['password'] ?? '';
        if (($user === 'awaiting_bob_password' && $password === $bob_password) ||
            ($user === 'awaiting_root_password' && $password === $root_password)) {
            echo "OK";
        } else {
            echo "FAIL";
        }
    } elseif ($action === 'cat_file') {
        $file = $_POST['file'] ?? '';
        if ($file === 'notes.txt') {
            echo $notes;
        } elseif ($file === '.bash_history' && $user === 'bob') {
            echo $bash_history;
        } elseif ($file === '/secret/flag.txt' && $user === 'root') {
            echo "Félicitations ! Voici ton flag:\n" . $flag;
        } elseif ($file === 'secret/flag.txt' && $user === 'bob') {
            echo "Permission refusée. Seul root peut lire ce fichier.";
        } else {
            echo "cat: " . htmlspecialchars($file) . ": Aucun fichier ou dossier de ce type";
        }
    }
}
?>
