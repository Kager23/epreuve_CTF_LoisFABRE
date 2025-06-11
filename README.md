# CTF Terminal Secret de Bob

Difficulté : facile

## Description technique

Ce CTF simule un terminal en ligne accessible via une page web (`index.php`). L’interface est en HTML/JS et communique avec un backend PHP (`backend.php`) pour valider les mots de passe et retourner le contenu protégé (notamment le flag).

- Les mots de passe ne sont **pas visibles dans le code client** (JS ou HTML) : la validation est faite côté serveur via des requêtes AJAX POST.
- Le flag est stocké côté serveur et n’est accessible qu’à l’utilisateur `root` après authentification.
- Les commandes disponibles imitent un terminal Linux simplifié : `ls`, `cat`, `su`, `sudo`, etc.
- Les permissions sont simulées : seul `root` peut accéder au fichier `/secret/flag.txt`.

## Comment résoudre le CTF étape par étape

1. **Explorer les commandes disponibles et les fichiers accessibles**  
   Utiliser `help` pour afficher la liste des commandes utilisables.

2. **Se connecter en tant que Bob**  
   Taper `su bob` puis entrer le mot de passe de Bob (`le mot de passe est dans notes.txt`) lorsqu’il est demandé.

3. **Explorer les fichiers accessibles à Bob**  
   Utiliser `ls`, `cat notes.txt` et `cat .bash_history` pour récupérer des indices sur le mot de passe root.

4. **Se connecter en root**  
   Avec les indices récupérés, taper `su root` puis entrer le mot de passe root (`marley1977`).

5. **Accéder au flag**  
   Une fois root connecté, taper `cat /secret/flag.txt` pour afficher le flag.


```sql
INSERT INTO epreuve VALUES (
    NULL,
    'Le Terminal Secret de Bob',
    'ctf{B0b_L0ves_Reggae_Music}',
    'web/bobterminal',
    1,
    1
);
