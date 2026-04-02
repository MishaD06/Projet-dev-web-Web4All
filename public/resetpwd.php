<?php
$passwords = array(
    'admin@stagelab.fr'       => 'Admin1234!',
    'sophie.dubois@cesi.fr'   => 'Pilote1234!',
    'thomas.martin@cesi.fr'   => 'Etudiant1234!',
    'julie.bernard@cesi.fr'   => 'Etudiant1234!',
    'alexandre.petit@cesi.fr' => 'Etudiant1234!'
);

$lines = file(dirname(__DIR__).'/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($lines as $line) {
    $line = trim($line);
    if ($line === '' || $line[0] === '#') continue;
    if (strpos($line, '=') !== false) {
        list($k, $v) = explode('=', $line, 2);
        $_ENV[trim($k)] = trim($v);
    }
}

$dsn = 'mysql:host=localhost;dbname=' . $_ENV['DB_DATABASE'] . ';charset=utf8mb4';
$pdo = new PDO($dsn, $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD']);

foreach ($passwords as $email => $pwd) {
    $hash = password_hash($pwd, PASSWORD_BCRYPT);
    $stmt = $pdo->prepare('UPDATE user SET mot_de_passe = ? WHERE email = ?');
    $stmt->execute(array($hash, $email));
    echo 'OK : ' . $email . '<br>';
}
echo '<br><strong>Termine ! Supprimez ce fichier.</strong>';
```

Sauvegarder : `Ctrl+O` `Entrée` `Ctrl+X`

Puis ouvrir dans le navigateur :
```
http://stagelab.local/resetpwd.php
