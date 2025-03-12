<?php
// Démarrage de la session
session_start();

// Connexion à la base de données
try {
    $bdd = new PDO(
        'mysql:host=localhost;dbname=agenceimmo;charset=utf8',
        'root',
        'root',
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    );
} catch (Exception $e) {
    die('Erreur de connexion à la base de données : ' . $e->getMessage());
}

// Fonction pour vérifier si l'utilisateur est connecté
function estConnecte() {
    return isset($_SESSION['id_utilisateur']);
}

// Fonction pour vérifier si l'utilisateur est un agent ou un admin
function estAgent() {
    return estConnecte() && ($_SESSION['niveau_acces'] >= 2);
}

// Fonction pour vérifier si l'utilisateur est un admin
function estAdmin() {
    return estConnecte() && ($_SESSION['niveau_acces'] == 3);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Altimmo</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">


    <!-- Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js" crossorigin="anonymous"></script>
</head>
<body>

<header>
    <nav class="navbar navbar-expand-lg custom-navbar">
        <div class="container">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <a class="navbar-brand" href="index.php">
            <a class="navbar-brand" href="index.php">Altimmo</a>
            </a>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="biens.php">
                            <i class="fas fa-home me-1"></i>Nos biens
                        </a>
                    </li>
                    
                    <?php if(estConnecte()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="messages.php">
                                Messages
                                <?php if ($nb_messages > 0): ?>
                                    <span class="badge bg-danger"><?= $nb_messages; ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                    <?php endif; ?>

                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">
                            <i class="fas fa-envelope me-1"></i>Contact
                        </a>
                    </li>
                </ul>
            </div>

            <ul class="navbar-nav">
                <?php if(estConnecte()): ?>
                    <li class="nav-item">
                        <a href="logout.php" class="nav-link" onclick="return confirm('Êtes-vous sûr de vouloir vous déconnecter ?')">
                            Déconnexion
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a href="login.php" class="nav-link login-btn">Mon Espace</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
</header>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
