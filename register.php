<?php
include 'includes/header.php';

// Redirection si déjà connecté
if (estConnecte()) {
    header('Location: index.php');
    exit();
}

// Traitement du formulaire d'inscription
$erreurs = [];
$success = false;

if (isset($_POST['inscription'])) {
    // Récupération et nettoyage des données
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $email = htmlspecialchars($_POST['email']);
    $telephone = htmlspecialchars($_POST['telephone']);
    $mot_de_passe = $_POST['mot_de_passe'];
    $confirm_mot_de_passe = $_POST['confirm_mot_de_passe'];
    
    // Validation des données
    if (empty($nom) || strlen($nom) < 2) {
        $erreurs[] = "Le nom doit contenir au moins 2 caractères";
    }
    
    if (empty($prenom) || strlen($prenom) < 2) {
        $erreurs[] = "Le prénom doit contenir au moins 2 caractères";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreurs[] = "L'adresse email n'est pas valide";
    } else {
        // Vérifier si l'email existe déjà
        $req = $bdd->prepare("SELECT COUNT(*) FROM utilisateurs WHERE email = ?");
        $req->execute([$email]);
        if ($req->fetchColumn() > 0) {
            $erreurs[] = "Cette adresse email est déjà utilisée";
        }
    }
    
    if (!empty($telephone) && !preg_match("/^[0-9]{10}$/", $telephone)) {
        $erreurs[] = "Le numéro de téléphone doit contenir 10 chiffres";
    }
    
    if (strlen($mot_de_passe) < 6) {
        $erreurs[] = "Le mot de passe doit contenir au moins 6 caractères";
    }
    
    if ($mot_de_passe !== $confirm_mot_de_passe) {
        $erreurs[] = "Les mots de passe ne correspondent pas";
    }
    
    if (!isset($_POST['conditions'])) {
        $erreurs[] = "Vous devez accepter les conditions d'utilisation";
    }
    
    // Si aucune erreur, inscription de l'utilisateur
    if (empty($erreurs)) {
        try {
            $req = $bdd->prepare("INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, telephone, niveau_acces) VALUES (?, ?, ?, ?, ?, 1)");
            $req->execute([$nom, $prenom, $email, sha1($mot_de_passe), $telephone]);
            
            $success = true;
            
            // Redirection vers la page de connexion après 3 secondes
            header("refresh:3;url=login.php");
        } catch (PDOException $e) {
            $erreurs[] = "Erreur lors de l'inscription : " . $e->getMessage();
        }
    }
}
?>

<!-- Formulaire d'inscription -->
<section style="background-color: #f8f9fa; min-height: 100vh;">
    <div class="container py-5">
        <div class="row d-flex justify-content-center align-items-center">
            <div class="col-12 col-xl-10">
                <div class="card shadow" style="border-radius: 1rem;">
                    <div class="row g-0 flex-column-reverse flex-md-row">
                        <div class="col-md-6 col-lg-5 d-none d-md-block">
                            <img src="img/image-reg.webp" alt="register form" class="img-fluid"
                                style="border-radius: 0 0 0 1rem; height: 100%; object-fit: cover;" />
                        </div>
                        <div class="col-md-6 col-lg-7 d-flex align-items-center">
                            <div class="card-body p-4 p-lg-5 text-black">
                                <?php if ($success): ?>
                                    <div class="alert alert-success">
                                        <p>Votre compte a été créé avec succès !</p>
                                        <p>Vous allez être redirigé vers la page de connexion...</p>
                                    </div>
                                <?php else: ?>
                                    <?php if (!empty($erreurs)): ?>
                                        <div class="alert alert-danger">
                                            <ul class="mb-0">
                                                <?php foreach ($erreurs as $erreur): ?>
                                                    <li><?= $erreur ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <form method="post" action="">
                                        <div class="d-flex align-items-center mb-3 pb-1">
                                            <i class="fas fa-user-plus fa-2x me-3" style="color:#c0a16b;"></i>
                                            <span class="h1 fw-bold mb-0">Altimmo</span>
                                        </div>
                                        <h5 class="fw-normal mb-3 pb-3" style="letter-spacing: 1px;">Créer un compte</h5>
                                                        <div class="row mb-4">
                                            <div class="col-md-6 mb-3 mb-md-0">
                                                <label class="form-label" for="nom">Nom</label>
                                                <input type="text" id="nom" name="nom" class="form-control" 
                                                    placeholder="Votre nom" value="<?= isset($nom) ? $nom : '' ?>" required />
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label" for="prenom">Prénom</label>
                                                <input type="text" id="prenom" name="prenom" class="form-control" 
                                                    placeholder="Votre prénom" value="<?= isset($prenom) ? $prenom : '' ?>" required />
                                            </div>
                                        </div>
                                        <div class="form-outline mb-4">
                                            <label class="form-label" for="email">Adresse Email</label>
                                            <input type="email" id="email" name="email" class="form-control" 
                                                placeholder="Votre email" value="<?= isset($email) ? $email : '' ?>" required />
                                        </div>
                                        <div class="form-outline mb-4">
                                            <label class="form-label" for="telephone">Téléphone</label>
                                            <input type="tel" id="telephone" name="telephone" class="form-control" 
                                                placeholder="Votre téléphone" value="<?= isset($telephone) ? $telephone : '' ?>" />
                                        </div>
                                        <div class="form-outline mb-4">
                                            <label class="form-label" for="mot_de_passe">Mot de passe</label>
                                            <input type="password" id="mot_de_passe" name="mot_de_passe" class="form-control" 
                                                placeholder="Créer un mot de passe" required />
                                        </div>
                                        <div class="form-outline mb-4">
                                            <label class="form-label" for="confirm_mot_de_passe">Confirmer le mot de passe</label>
                                            <input type="password" id="confirm_mot_de_passe" name="confirm_mot_de_passe" class="form-control" 
                                                placeholder="Confirmez votre mot de passe" required />
                                        </div>
                                        <div class="form-check mb-4">
                                            <input class="form-check-input" type="checkbox" id="conditions" name="conditions" required>
                                            <label class="form-check-label" for="conditions" style="line-height: 1.4;">
                                                J'accepte les <a href="conditions.php" style="color: #007bff;">conditions d'utilisation</a> et la <a href="politique.php" style="color: #007bff;">politique de confidentialité</a>
                                            </label>
                                        </div>
                                        <div class="pt-1 mb-4">
                                            <button class="btn btn-primary w-100" type="submit" name="inscription">S'inscrire</button>
                                        </div>
                                        <p class="mb-3" style="color: #393f81;">Déjà un compte ? <a href="login.php" style="color: #393f81;">Se connecter</a></p>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Ajout de CSS personnalisé pour améliorer la mise en page responsive -->
<style>
    @media (max-width: 767.98px) {
        .card {
            border-radius: 0.5rem !important;
        }
        .card-body {
            padding: 1.5rem !important;
        }
        .form-check-label {
            font-size: 0.9rem;
        }
    }
</style>

<?php
include 'includes/footer.php';
?>