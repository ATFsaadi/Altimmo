<?php
// Connexion à la base de données
try {
    $bdd = new PDO('mysql:host=localhost;dbname=agenceimmo;charset=utf8', 'root', '');
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Erreur de connexion : ' . $e->getMessage());
}

// Vérifier si la colonne id_bien existe déjà dans la table messages
$checkColumn = $bdd->query("SHOW COLUMNS FROM messages LIKE 'id_bien'");
if ($checkColumn->rowCount() == 0) {
    // La colonne n'existe pas, on l'ajoute
    try {
        $bdd->exec("ALTER TABLE messages ADD COLUMN id_bien INT DEFAULT NULL");
        $bdd->exec("ALTER TABLE messages ADD FOREIGN KEY (id_bien) REFERENCES biens(id_bien) ON DELETE SET NULL");
        echo "La colonne id_bien a été ajoutée avec succès à la table messages.";
    } catch (PDOException $e) {
        echo "Erreur lors de l'ajout de la colonne : " . $e->getMessage();
    }
} else {
    echo "La colonne id_bien existe déjà dans la table messages.";
}
?>
