<?php
include 'includes/header.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id_utilisateur'])) {
    header('Location: login.php');
    exit();
}

$id_utilisateur = $_SESSION['id_utilisateur'];

// Récupérer les informations de l'utilisateur
$req = $bdd->prepare("SELECT * FROM utilisateurs WHERE id_utilisateur = ?");
$req->execute([$id_utilisateur]);
$utilisateur = $req->fetch(PDO::FETCH_ASSOC);

// Traitement du formulaire de mise à jour du profil
$success = false;
$erreurs = [];

if (isset($_POST['modifier_profil'])) {
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $email = htmlspecialchars($_POST['email']);
    $telephone = htmlspecialchars($_POST['telephone']);
    
    // Vérification des champs
    if (empty($nom)) {
        $erreurs[] = "Le nom est obligatoire";
    }
    
    if (empty($prenom)) {
        $erreurs[] = "Le prénom est obligatoire";
    }
    
    if (empty($email)) {
        $erreurs[] = "L'email est obligatoire";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreurs[] = "L'email n'est pas valide";
    }
    
    // Vérifier si l'email existe déjà (pour un autre utilisateur)
    if ($email != $utilisateur['email']) {
        $req_email = $bdd->prepare("SELECT COUNT(*) FROM utilisateurs WHERE email = ? AND id_utilisateur != ?");
        $req_email->execute([$email, $id_utilisateur]);
        if ($req_email->fetchColumn() > 0) {
            $erreurs[] = "Cet email est déjà utilisé par un autre compte";
        }
    }
    
    // Vérification du mot de passe actuel si l'utilisateur souhaite le changer
    $changer_mot_de_passe = !empty($_POST['nouveau_mot_de_passe']);
    
    if ($changer_mot_de_passe) {
        $mot_de_passe_actuel = $_POST['mot_de_passe_actuel'];
        $nouveau_mot_de_passe = $_POST['nouveau_mot_de_passe'];
        $confirmer_mot_de_passe = $_POST['confirmer_mot_de_passe'];
        
        if (empty($mot_de_passe_actuel)) {
            $erreurs[] = "Le mot de passe actuel est obligatoire pour le changer";
        } elseif (sha1($mot_de_passe_actuel) !== $utilisateur['mot_de_passe']) {
            $erreurs[] = "Le mot de passe actuel est incorrect";
        }
        
        if (empty($nouveau_mot_de_passe)) {
            $erreurs[] = "Le nouveau mot de passe est obligatoire";
        } elseif (strlen($nouveau_mot_de_passe) < 6) {
            $erreurs[] = "Le nouveau mot de passe doit contenir au moins 6 caractères";
        }
        
        if ($nouveau_mot_de_passe !== $confirmer_mot_de_passe) {
            $erreurs[] = "Les deux mots de passe ne correspondent pas";
        }
    }
    
    // Si pas d'erreurs, mettre à jour le profil
    if (empty($erreurs)) {
        if ($changer_mot_de_passe) {
            $req_update = $bdd->prepare("UPDATE utilisateurs SET nom = ?, prenom = ?, email = ?, telephone = ?, mot_de_passe = ? WHERE id_utilisateur = ?");
            $req_update->execute([$nom, $prenom, $email, $telephone, sha1($nouveau_mot_de_passe), $id_utilisateur]);
        } else {
            $req_update = $bdd->prepare("UPDATE utilisateurs SET nom = ?, prenom = ?, email = ?, telephone = ? WHERE id_utilisateur = ?");
            $req_update->execute([$nom, $prenom, $email, $telephone, $id_utilisateur]);
        }
        
        $success = true;
        
        // Mettre à jour les informations de l'utilisateur
        $req = $bdd->prepare("SELECT * FROM utilisateurs WHERE id_utilisateur = ?");
        $req->execute([$id_utilisateur]);
        $utilisateur = $req->fetch(PDO::FETCH_ASSOC);
    }
}

// Récupérer les messages non lus
$req_messages = $bdd->prepare("SELECT COUNT(*) FROM messages WHERE id_destinataire = ? AND lu = 0");
$req_messages->execute([$id_utilisateur]);
$nb_messages_non_lus = $req_messages->fetchColumn();

// Récupérer les biens favoris (à implémenter plus tard)
$nb_favoris = 0;
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Mon compte</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="#profil" class="list-group-item list-group-item-action active" data-bs-toggle="tab">
                        <i class="fas fa-user me-2"></i> Profil
                    </a>
                    <a href="messages.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-envelope me-2"></i> Messages
                        <?php if ($nb_messages_non_lus > 0): ?>
                        <span class="badge bg-danger float-end"><?= $nb_messages_non_lus ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="#favoris" class="list-group-item list-group-item-action" data-bs-toggle="tab">
                        <i class="fas fa-heart me-2"></i> Favoris
                        <?php if ($nb_favoris > 0): ?>
                        <span class="badge bg-secondary float-end"><?= $nb_favoris ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="logout.php" class="list-group-item list-group-item-action text-danger">
                        <i class="fas fa-sign-out-alt me-2"></i> Déconnexion
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-9">
            <div class="tab-content">
                <!-- Onglet Profil -->
                <div class="tab-pane fade show active" id="profil">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Mon profil</h5>
                        </div>
                        <div class="card-body">
                            <?php if ($success): ?>
                            <div class="alert alert-success">
                                Votre profil a été mis à jour avec succès.
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($erreurs)): ?>
                            <div class="alert alert-danger">
                                <strong>Des erreurs sont survenues :</strong>
                                <ul class="mb-0">
                                    <?php foreach ($erreurs as $erreur): ?>
                                    <li><?= $erreur ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <?php endif; ?>
                            
                            <form method="post" action="">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="nom" class="form-label">Nom</label>
                                        <input type="text" class="form-control" id="nom" name="nom" value="<?= htmlspecialchars($utilisateur['nom']) ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="prenom" class="form-label">Prénom</label>
                                        <input type="text" class="form-control" id="prenom" name="prenom" value="<?= htmlspecialchars($utilisateur['prenom']) ?>" required>
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($utilisateur['email']) ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="telephone" class="form-label">Téléphone</label>
                                        <input type="tel" class="form-control" id="telephone" name="telephone" value="<?= htmlspecialchars($utilisateur['telephone']) ?>">
                                    </div>
                                </div>
                                
                                <hr>
                                <h6>Changer de mot de passe</h6>
                                <p class="text-muted small">Laissez les champs vides si vous ne souhaitez pas changer votre mot de passe.</p>
                                
                                <div class="mb-3">
                                    <label for="mot_de_passe_actuel" class="form-label">Mot de passe actuel</label>
                                    <input type="password" class="form-control" id="mot_de_passe_actuel" name="mot_de_passe_actuel">
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="nouveau_mot_de_passe" class="form-label">Nouveau mot de passe</label>
                                        <input type="password" class="form-control" id="nouveau_mot_de_passe" name="nouveau_mot_de_passe">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="confirmer_mot_de_passe" class="form-label">Confirmer le mot de passe</label>
                                        <input type="password" class="form-control" id="confirmer_mot_de_passe" name="confirmer_mot_de_passe">
                                    </div>
                                </div>
                                
                                <div class="d-grid gap-2">
                                    <button type="submit" name="modifier_profil" class="btn btn-primary">Enregistrer les modifications</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Onglet Favoris -->
                <div class="tab-pane fade" id="favoris">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Mes biens favoris</h5>
                        </div>
                        <div class="card-body">
                            <div class="text-center py-5">
                                <i class="fas fa-heart fa-4x text-muted mb-3"></i>
                                <h6>Vous n'avez pas encore de biens favoris</h6>
                                <p class="text-muted">Explorez nos annonces et ajoutez des biens à vos favoris pour les retrouver facilement.</p>
                                <a href="index.php" class="btn btn-primary">Voir les annonces</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
