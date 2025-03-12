<?php
include 'includes/header.php';

// Initialisation des variables
$success = false;
$erreurs = [];
$nom = $email = $telephone = $sujet = $message = '';

// Traitement du formulaire
if (isset($_POST['envoyer'])) {
    $nom = trim(htmlspecialchars($_POST['nom']));
    $email = trim(htmlspecialchars($_POST['email']));
    $telephone = trim(htmlspecialchars($_POST['telephone']));
    $sujet = trim(htmlspecialchars($_POST['sujet']));
    $message = trim(htmlspecialchars($_POST['message']));
    
    if (empty($nom)) $erreurs[] = "Le nom est obligatoire.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $erreurs[] = "Email invalide.";
    if (empty($telephone) || !preg_match('/^\+?[0-9\s\-]{10,15}$/', $telephone)) $erreurs[] = "Téléphone invalide.";
    if (empty($sujet)) $erreurs[] = "Le sujet est obligatoire.";
    if (empty($message)) $erreurs[] = "Le message est obligatoire.";

    if (empty($erreurs)) {
        $req = $bdd->prepare("SELECT id_utilisateur FROM utilisateurs WHERE niveau_acces IN (3, 2) ORDER BY niveau_acces DESC LIMIT 1");
        $req->execute();
        $destinataire = $req->fetch(PDO::FETCH_ASSOC);

        if ($destinataire) {
            $id_destinataire = $destinataire['id_utilisateur'];
            $contenu = "Message de : $nom\nEmail : $email\nTéléphone : $telephone\n\n$message";
            $req = $bdd->prepare("INSERT INTO messages (id_expediteur, id_destinataire, sujet, contenu, date_envoi) VALUES (NULL, ?, ?, ?, NOW())");
            $req->execute([$id_destinataire, $sujet, $contenu]);
            $success = true;
            $nom = $email = $telephone = $sujet = $message = '';
        } else {
            $erreurs[] = "Aucun destinataire trouvé.";
        }
    }
}
?>

<div class="container mt-4">
    <div class="row">
        <!-- Formulaire de contact -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="btn btn-primary btn-lg w-100 rounded">
                    <h5 class="mb-0">Contactez-nous</h5>
                </div>
                <div class="card-body">
                    <?php if ($success): ?>
                        <div class="alert alert-success text-center">Votre message a été envoyé !</div>
                    <?php elseif (!empty($erreurs)): ?>
                        <div class="alert alert-danger"><ul class="mb-0"><?php foreach ($erreurs as $erreur) echo "<li>$erreur</li>"; ?></ul></div>
                    <?php endif; ?>

                    <form method="post" action="" onsubmit="return confirm('Envoyer ce message ?');">
                        <div class="mb-2">
                            <label for="nom" class="form-label">Nom *</label>
                            <input type="text" class="form-control form-control-sm" id="nom" name="nom" value="<?= $nom ?>" required>
                        </div>
                        <div class="mb-2">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" class="form-control form-control-sm" id="email" name="email" value="<?= $email ?>" required>
                        </div>
                        <div class="mb-2">
                            <label for="telephone" class="form-label">Téléphone *</label>
                            <input type="tel" class="form-control form-control-sm" id="telephone" name="telephone" value="<?= $telephone ?>" required>
                        </div>
                        <div class="mb-2">
                            <label for="sujet" class="form-label">Sujet *</label>
                            <select class="form-select form-select-sm" id="sujet" name="sujet" required>
                                <option value="">Sélectionnez un sujet</option>
                                <option value="Demande d'information" <?= $sujet == 'Demande d\'information' ? 'selected' : '' ?>>Demande d'information</option>
                                <option value="Estimation de bien" <?= $sujet == 'Estimation de bien' ? 'selected' : '' ?>>Estimation de bien</option>
                                <option value="Vendre un bien" <?= $sujet == 'Vendre un bien' ? 'selected' : '' ?>>Vendre un bien</option>
                                <option value="Louer un bien" <?= $sujet == 'Louer un bien' ? 'selected' : '' ?>>Louer un bien</option>
                                <option value="Autre" <?= $sujet == 'Autre' ? 'selected' : '' ?>>Autre</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label for="message" class="form-label">Message *</label>
                            <textarea class="form-control form-control-sm" id="message" name="message" rows="3" required><?= $message ?></textarea>
                        </div>
                        <div class="d-grid">
                            <button type="submit" name="envoyer" class="btn btn-primary btn-sm">Envoyer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Carte Google Maps -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="btn btn-primary btn-lg w-100 rounded">
                    <h5 class="mb-0">Notre emplacement</h5>
                </div>
                <div class="card-body p-0">
                    <div class="ratio ratio-16x9">
                        <iframe src="https://www.google.com/maps/embed?pb=..." allowfullscreen loading="lazy"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Coordonnées en bas et centrées -->
    <div class="row mt-3">
        <div class="col-12">
            <div class="card shadow-sm text-center">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Nos coordonnées</h5>
                </div>
                <div class="card-body">
                    <p><strong>Agence Altimmo</strong><br>123 Avenue des Champs-Élysées, 75008 Paris</p>
                    <p><i class="fas fa-phone me-2 text-primary"></i> 01 23 45 67 89</p>
                    <p><i class="fas fa-envelope me-2 text-primary"></i> contact@altimmo.fr</p>
                    <p><strong>Horaires :</strong><br>
                    Lundi - Vendredi: 9h - 19h | Samedi: 10h - 17h | Dimanche: Fermé</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
