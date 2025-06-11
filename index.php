<?php
session_start();
header("Content-Type: text/html; charset=UTF-8");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Le Terminal Secret de Bob</title>
    <link rel="stylesheet" href="terminal.css" />
</head>
<body>
    <div class="terminal-container">
        <h2>BM Le Terminal Secret de Bob</h2>
        <div id="terminal">
            Bienvenue sur le terminal de Bob.<br />
            Dernière connexion : aujourd'hui à <?php echo date("H:i"); ?>
            <pre id="command-history"></pre>
        </div>
        <div id="input-line">
            <span id="prompt">guest@serveur:$</span>
            <input type="text" id="command-input" autofocus />
            <span class="blink">_</span>
        </div>
        <div class="hint">
            Astuce : Tapez <strong>help</strong> pour voir les commandes disponibles
        </div>
    </div>
    <script>
        const terminal = document.getElementById('terminal');
        const commandInput = document.getElementById('command-input');
        const commandHistory = document.getElementById('command-history');
        let history = [], historyIndex = 0;
        let awaitingPassword = false;
        let loggedInUser = null;

        function getPrompt() {
            if (loggedInUser === 'root') return 'root@serveur:#';
            if (loggedInUser === 'bob') return 'bob@serveur:~$';
            return 'guest@serveur:$';
        }

        function updatePrompt() {
            document.getElementById('prompt').textContent = getPrompt();
        }

        updatePrompt();

        commandInput.addEventListener('keydown', async function(e) {
            if (e.key === 'Enter') {
                const command = this.value.trim();
                if (!command) return;

                commandHistory.innerHTML += getPrompt() + ' ' + command + '\n';
                history.push(command);
                historyIndex = history.length;

                let output = '';

                if (awaitingPassword) {
                    const user = loggedInUser;
                    const res = await fetch('backend.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `action=verify_password&user=${encodeURIComponent(user)}&password=${encodeURIComponent(command)}`
                    });
                    const result = await res.text();
                    if (result.trim() === 'OK') {
                        loggedInUser = (user === 'awaiting_bob_password') ? 'bob' : 'root';
                        output = 'Accès autorisé ! Bienvenue ' + loggedInUser + '.\n';
                    } else {
                        output = 'Mot de passe incorrect.\n';
                        loggedInUser = (user === 'awaiting_root_password') ? 'bob' : null;
                    }
                    awaitingPassword = false;
                    updatePrompt();
                } else {
                    const args = command.split(' ');
                    const baseCmd = args[0].toLowerCase();

                    switch(baseCmd) {
                        case 'help':
                            output = "Commandes disponibles:\n- ls\n- cat [fichier]\n- sudo [commande]\n- su [utilisateur]\n- whoami\n- help";
                            break;
                        case 'whoami':
                            output = loggedInUser ?? 'invité';
                            break;
                        case 'ls':
                            if (args[1] === '/secret') {
                                if (loggedInUser === 'root') output = "flag.txt";
                                else if (loggedInUser === 'bob') output = "flag.txt (permission denied)";
                                else output = "ls: cannot access '/secret': Permission denied";
                            } else {
                                output = (loggedInUser === 'root' || loggedInUser === 'bob')
                                    ? "Documents/  Photos/  secret/  notes.txt  .bash_history"
                                    : "Documents/  Photos/  notes.txt";
                            }
                            break;
                        case 'cat':
                            if (args.length < 2) output = "Usage : cat [fichier]";
                            else {
                                const file = args[1].toLowerCase();
                                const resFile = await fetch('backend.php', {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                                    body: `action=cat_file&file=${encodeURIComponent(file)}&user=${encodeURIComponent(loggedInUser ?? '')}`
                                });
                                output = await resFile.text();
                            }
                            break;
                        case 'sudo':
                            if (loggedInUser !== 'bob') {
                                output = "Erreur : Permission refusée\nAstuce : Connectez-vous en tant que bob";
                            } else {
                                const sudoCmd = args.slice(1).join(' ');
                                if (sudoCmd === 'ls /secret') output = "flag.txt";
                                else if (sudoCmd === 'cat /secret/flag.txt') output = "Permission refusée. Seul root peut lire ce fichier.";
                                else output = "Commande sudo inconnue : " + sudoCmd;
                            }
                            break;
                        case 'su':
                            if (args.length < 2) output = "Usage : su [utilisateur]";
                            else {
                                const user = args[1].toLowerCase();
                                if (user === 'bob') {
                                    output = "Mot de passe requis pour bob:";
                                    awaitingPassword = true;
                                    loggedInUser = 'awaiting_bob_password';
                                } else if (user === 'root' && loggedInUser === 'bob') {
                                    output = "Mot de passe requis pour root:";
                                    awaitingPassword = true;
                                    loggedInUser = 'awaiting_root_password';
                                } else {
                                    output = "Utilisateur inconnu ou accès refusé.";
                                }
                            }
                            break;
                        default:
                            output = "Commande non reconnue. Tapez 'help' pour l'aide.";
                    }
                }

                commandHistory.innerHTML += output + '\n\n';
                terminal.scrollTop = terminal.scrollHeight;
                this.value = '';
                updatePrompt();
            }

            if (e.key === 'ArrowUp') {
                if (historyIndex > 0) this.value = history[--historyIndex];
                e.preventDefault();
            }
            if (e.key === 'ArrowDown') {
                if (historyIndex < history.length - 1) this.value = history[++historyIndex];
                else {
                    historyIndex = history.length;
                    this.value = '';
                }
                e.preventDefault();
            }
        });
    </script>
</body>
</html>
