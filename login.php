<?php
include 'includes/header.php';

// Redirection si déjà connecté
if (estConnecte()) {
    header('Location: index.php');
    exit();
}

// Traitement du formulaire de connexion
$erreur = "";
if (isset($_POST['connexion'])) {
    $email = htmlspecialchars($_POST['email']);
    $mot_de_passe = $_POST['mot_de_passe'];
    
    // Vérification des identifiants
    $req = $bdd->prepare('SELECT * FROM utilisateurs WHERE email = ?');
    $req->execute([$email]);
    $utilisateur = $req->fetch();
    
    if ($utilisateur && $utilisateur['mot_de_passe'] === sha1($mot_de_passe)) {
        // Connexion réussie
        $_SESSION['id_utilisateur'] = $utilisateur['id_utilisateur'];
        $_SESSION['nom'] = $utilisateur['nom'];
        $_SESSION['prenom'] = $utilisateur['prenom'];
        $_SESSION['email'] = $utilisateur['email'];
        $_SESSION['niveau_acces'] = $utilisateur['niveau_acces'];
        
        // Redirection
        header('Location: index.php');
        exit();
    } else {
        $erreur = "Email ou mot de passe incorrect";
    }
}
?>

<!-- Formulaire de connexion -->
<section style="background-color: #f8f9fa; min-height: 100vh;">
    <div class="container py-5">
        <div class="row d-flex justify-content-center align-items-center">
            <div class="col-12 col-xl-10">
                <div class="card shadow" style="border-radius: 1rem;">
                    <div class="row g-0 flex-column-reverse flex-md-row">
                        <div class="col-md-6 col-lg-5 d-none d-md-block">
                            <img src="img/image-log1.webp" alt="login form" class="img-fluid"
                                style="border-radius: 0 0 0 1rem; height: 100%; object-fit: cover;" />
                        </div>
                        <div class="col-md-6 col-lg-7 d-flex align-items-center">
                            <div class="card-body p-4 p-lg-5 text-black">
                                <?php if (!empty($erreur)): ?>
                                    <div class="alert alert-danger"><?= $erreur ?></div>
                                <?php endif; ?>
                                
                                <form method="post" action="">
                                    <div class="d-flex align-items-center mb-3 pb-1">
                                        <i class="fas fa-home fa-2x me-3" style="color:#c0a16b;"></i>
                                        <span class="h1 fw-bold mb-0">Altimmo</span>
                                    </div>
                                    <h5 class="fw-normal mb-3 pb-3" style="letter-spacing: 1px;">Connexion</h5>
                                    <div class="form-outline mb-4">
                                        <label class="form-label" for="email">Adresse Email</label>
                                        <input type="email" name="email" id="email" class="form-control"
                                            placeholder="Votre email" required />
                                    </div>
                                    <div class="form-outline mb-4">
                                        <label class="form-label" for="mot_de_passe">Mot de passe</label>
                                        <input type="password" name="mot_de_passe" id="mot_de_passe" class="form-control"
                                            placeholder="Votre mot de passe" required />
                                    </div>
                                    <div class="pt-1 mb-4">
                                        <button class="btn btn-primary w-100" type="submit" name="connexion">Se connecter</button>
                                    </div>
                                    <a class="small text-muted d-block mb-2" href="#">Mot de passe oublié ?</a>
                                    <p class="mb-3" style="color: #393f81;">Pas encore de compte ? <a href="register.php"
                                            style="color: #393f81;">S'inscrire</a></p>
                                    <div class="d-flex flex-wrap gap-3">
                                        <a href="conditions.php" class="small text-muted">Conditions d'utilisation</a>
                                        <a href="politique.php" class="small text-muted">Politique de confidentialité</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Ajout de CSS personnalisé pour améliorer la mise en page responsive -->

<?php
include 'includes/footer.php';
?>