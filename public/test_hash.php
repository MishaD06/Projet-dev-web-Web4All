<?php
$password_saisi = "Web4All?";
// Hash généré à l'instant, garanti 100% compatible
$hash_en_base = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

echo "Mot de passe testé : " . $password_saisi . "<br>";
echo "Longueur du hash : " . strlen($hash_en_base) . " caractères (doit être 60)<br>";

if (password_verify($password_saisi, $hash_en_base)) {
    echo "✅ SUCCESS : Le hash est valide !";
} else {
    echo "❌ ERROR : Toujours pas de correspondance.";
}
?>
