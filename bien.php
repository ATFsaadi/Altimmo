<?php
include 'includes/header.php';

// Vérifier si l'ID du bien est fourni
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$id_bien = intval($_GET['id']);

// Récupérer les informations du bien
$req = $bdd->prepare("SELECT * FROM biens WHERE id_bien = ?");
$req->execute([$id_bien]);
$bien = $req->fetch(PDO::FETCH_ASSOC);

// Vérifier si le bien existe
if (!$bien) {
    header('Location: index.php');
    exit();
}

// Récupérer les images du bien
$req_images = $bdd->prepare("SELECT * FROM images_bien WHERE id_bien = ? ORDER BY est_principale DESC");
$req_images->execute([$id_bien]);
$images = $req_images->fetchAll(PDO::FETCH_ASSOC);

// Récupérer l'agent immobilier associé au bien
$req_agent = $bdd->prepare("SELECT * FROM utilisateurs WHERE id_utilisateur = ? AND niveau_acces >= 2");
$req_agent->execute([$bien['id_agent']]);
$agent = $req_agent->fetch(PDO::FETCH_ASSOC);

// Traitement du formulaire de contact
$message_envoye = false;
$erreurs = [];

if (isset($_POST['envoyer_message'])) {
    // Vérification des champs
    if (empty($_POST['nom'])) {
        $erreurs[] = "Veuillez saisir votre nom";
    }
    
    if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $erreurs[] = "Veuillez saisir une adresse email valide";
    }
    
    if (empty($_POST['telephone'])) {
        $erreurs[] = "Veuillez saisir votre numéro de téléphone";
    }
    
    if (empty($_POST['message'])) {
        $erreurs[] = "Veuillez saisir votre message";
    }
    
    // Si pas d'erreurs, enregistrer le message
    if (empty($erreurs)) {
        // Vérifier si l'utilisateur est connecté
        $expediteur = isset($_SESSION['id_utilisateur']) ? $_SESSION['id_utilisateur'] : null;
        
        // Si l'utilisateur n'est pas connecté, on vérifie s'il existe déjà un compte avec cet email
        if (!$expediteur) {
            $email = htmlspecialchars($_POST['email']);
            $req_user = $bdd->prepare("SELECT id_utilisateur FROM utilisateurs WHERE email = ?");
            $req_user->execute([$email]);
            $user_exists = $req_user->fetch(PDO::FETCH_ASSOC);
            
            if ($user_exists) {
                // Utiliser l'ID de l'utilisateur existant
                $expediteur = $user_exists['id_utilisateur'];
            } else {
                // Créer un nouvel utilisateur avec niveau d'accès 1 (utilisateur normal)
                $nom = htmlspecialchars($_POST['nom']);
                $telephone = htmlspecialchars($_POST['telephone']);
                $password = bin2hex(random_bytes(8)); // Mot de passe aléatoire
                
                // Extraire prénom et nom
                $nom_parts = explode(' ', $nom, 2);
                $prenom = $nom_parts[0];
                $nom_famille = isset($nom_parts[1]) ? $nom_parts[1] : '';
                
                $req_new_user = $bdd->prepare("INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, telephone, niveau_acces) VALUES (?, ?, ?, SHA1(?), ?, 1)");
                $req_new_user->execute([$nom_famille, $prenom, $email, $password, $telephone]);
                
                $expediteur = $bdd->lastInsertId();
                
                // Ici on pourrait envoyer un email à l'utilisateur avec ses identifiants
            }
        }
        
        $nom = htmlspecialchars($_POST['nom']);
        $email = htmlspecialchars($_POST['email']);
        $telephone = htmlspecialchars($_POST['telephone']);
        $message_texte = htmlspecialchars($_POST['message']);
        
        // Préparer le contenu du message
        $contenu = "Demande d'information pour le bien : " . $bien['titre'] . "\n";
        $contenu .= "De : " . $nom . " (" . $email . ", " . $telephone . ")\n\n";
        $contenu .= $message_texte;
        
        // Vérifier si la colonne id_bien existe dans la table messages
        try {
            $checkColumn = $bdd->query("SHOW COLUMNS FROM messages LIKE 'id_bien'");
            if ($checkColumn->rowCount() == 0) {
                // La colonne n'existe pas, on l'ajoute
                $bdd->exec("ALTER TABLE messages ADD COLUMN id_bien INT DEFAULT NULL");
                $bdd->exec("ALTER TABLE messages ADD CONSTRAINT fk_messages_biens FOREIGN KEY (id_bien) REFERENCES biens(id_bien) ON DELETE SET NULL");
            }
            
            // Insérer le message avec l'ID du bien
            $req_message = $bdd->prepare("INSERT INTO messages (id_expediteur, id_destinataire, sujet, contenu, date_envoi, id_bien) VALUES (?, ?, ?, ?, NOW(), ?)");
            $req_message->execute([
                $expediteur,
                $bien['id_agent'],
                "Demande d'information - " . $bien['titre'],
                $contenu,
                $id_bien
            ]);
        } catch (PDOException $e) {
            // Si une erreur se produit (par exemple si la colonne id_bien n'existe pas), on insère sans l'ID du bien
            $req_message = $bdd->prepare("INSERT INTO messages (id_expediteur, id_destinataire, sujet, contenu, date_envoi) VALUES (?, ?, ?, ?, NOW())");
            $req_message->execute([
                $expediteur,
                $bien['id_agent'],
                "Demande d'information - " . $bien['titre'],
                $contenu
            ]);
        }
        
        $message_envoye = true;
    }
}
?>

<div class="container mt-4">
    <!-- Fil d'Ariane -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Accueil</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($bien['titre']) ?></li>
        </ol>
    </nav>
    
    <?php if ($message_envoye): ?>
    <div class="alert alert-success">
        Votre message a bien été envoyé à l'agent immobilier. Il vous contactera dans les plus brefs délais.
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
    
    <div class="row">
        <!-- Colonne principale avec les détails du bien -->
        <div class="col-md-8">
            <div class="card mb-4">
                <!-- Carrousel d'images -->
                <?php if (!empty($images)): ?>
                <div id="carouselBien" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-indicators">
                        <?php foreach ($images as $key => $image): ?>
                        <button type="button" data-bs-target="#carouselBien" data-bs-slide-to="<?= $key ?>" <?= $key === 0 ? 'class="active" aria-current="true"' : '' ?> aria-label="Slide <?= $key + 1 ?>"></button>
                        <?php endforeach; ?>
                    </div>
                    <div class="carousel-inner">
                        <?php foreach ($images as $key => $image): ?>
                        <div class="carousel-item <?= $key === 0 ? 'active' : '' ?>">
                            <img src="<?= htmlspecialchars($image['url_image']) ?>" class="d-block w-100" alt="Image <?= $key + 1 ?>" style="height: 400px; object-fit: cover;">
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselBien" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Précédent</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselBien" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Suivant</span>
                    </button>
                </div>
                <?php else: ?>
                <div class="bg-light text-center py-5">
                    <i class="fas fa-home fa-5x text-muted mt-3"></i>
                    <p class="mt-2 text-muted">Pas d'images disponibles</p>
                </div>
                <?php endif; ?>
                
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h1 class="card-title h3"><?= htmlspecialchars($bien['titre']) ?></h1>
                        <span class="badge bg-<?= $bien['statut'] == 'À vendre' ? 'success' : 'primary' ?> fs-6"><?= htmlspecialchars($bien['statut']) ?></span>
                    </div>
                    
                    <h2 class="text-primary fw-bold h4"><?= number_format($bien['prix'], 0, ',', ' ') ?> €</h2>
                    
                    <p class="card-text text-muted">
                        <i class="fas fa-map-marker-alt me-1"></i> 
                        <?= htmlspecialchars($bien['adresse']) ?>, <?= htmlspecialchars($bien['ville']) ?> <?= htmlspecialchars($bien['code_postal']) ?>
                    </p>
                    
                    <div class="row mt-4 mb-4">
                        <div class="col-6 col-md-3 text-center border-end">
                            <div class="fw-bold"><?= htmlspecialchars($bien['surface']) ?> m²</div>
                            <small class="text-muted">Surface</small>
                        </div>
                        <div class="col-6 col-md-3 text-center border-end">
                            <div class="fw-bold"><?= htmlspecialchars($bien['nb_pieces']) ?></div>
                            <small class="text-muted">Pièces</small>
                        </div>
                        <div class="col-6 col-md-3 text-center border-end">
                            <div class="fw-bold"><?= htmlspecialchars($bien['nb_chambres']) ?></div>
                            <small class="text-muted">Chambres</small>
                        </div>
                        <div class="col-6 col-md-3 text-center">
                            <div class="fw-bold"><?= htmlspecialchars($bien['nb_salles_bain']) ?></div>
                            <small class="text-muted">Salles de bain</small>
                        </div>
                    </div>
                    
                    <h3 class="h5 mt-4">Description</h3>
                    <p><?= nl2br(htmlspecialchars($bien['description'])) ?></p>
                    
                    <h3 class="h5 mt-4">Caractéristiques</h3>
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item"><i class="fas fa-check-circle text-success me-2"></i> Type: <?= htmlspecialchars($bien['type']) ?></li>
                                <li class="list-group-item"><i class="fas fa-check-circle text-success me-2"></i> Année de construction: <?= htmlspecialchars($bien['annee_construction']) ?></li>
                                <?php if ($bien['garage']): ?>
                                <li class="list-group-item"><i class="fas fa-check-circle text-success me-2"></i> Garage</li>
                                <?php endif; ?>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-group list-group-flush">
                                <?php if ($bien['jardin']): ?>
                                <li class="list-group-item"><i class="fas fa-check-circle text-success me-2"></i> Jardin</li>
                                <?php endif; ?>
                                <?php if ($bien['piscine']): ?>
                                <li class="list-group-item"><i class="fas fa-check-circle text-success me-2"></i> Piscine</li>
                                <?php endif; ?>
                                <li class="list-group-item"><i class="fas fa-check-circle text-success me-2"></i> DPE: <?= htmlspecialchars($bien['classe_energie']) ?></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Colonne latérale avec les informations de contact -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h3 class="h5 mb-0">Contacter l'agent</h3>
                </div>
                <div class="card-body">
                    <?php if ($agent): ?>
                    <div class="text-center mb-3">
                        <img src="img/agent.jpg" alt="Agent immobilier" class="rounded-circle" width="100" height="100">
                        <button type="button" class="btn btn-primary btn-block mt-3 w-100" data-bs-toggle="collapse" data-bs-target="#contactForm">
                            <i class="fas fa-envelope me-2"></i> Contacter pour ce bien
                        </button>
                        <h4 class="h6 mt-2"><?= htmlspecialchars($agent['prenom']) ?> <?= htmlspecialchars($agent['nom']) ?></h4>
                        <p class="text-muted small">Agent immobilier</p>
                    </div>
                    <hr>
                    <?php endif; ?>
                    
                    <form method="post" action="">
                        <?php if (isset($_SESSION['id_utilisateur'])): ?>
                        <!-- Utilisateur connecté - informations pré-remplies -->
                        <div class="alert alert-info mb-3">
                            <small>Vous êtes connecté en tant que <?= htmlspecialchars($_SESSION['prenom'] . ' ' . $_SESSION['nom']) ?>. Votre message sera envoyé avec vos informations de profil.</small>
                        </div>
                        <input type="hidden" name="nom" value="<?= htmlspecialchars($_SESSION['prenom'] . ' ' . $_SESSION['nom']) ?>">
                        <input type="hidden" name="email" value="<?= htmlspecialchars($_SESSION['email']) ?>">
                        <input type="hidden" name="telephone" value="<?= htmlspecialchars($_SESSION['telephone']) ?>">
                        <?php else: ?>
                        <!-- Utilisateur non connecté - doit remplir ses informations -->
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom complet</label>
                            <input type="text" class="form-control" id="nom" name="nom" value="<?= isset($_POST['nom']) ? htmlspecialchars($_POST['nom']) : '' ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="telephone" class="form-label">Téléphone</label>
                            <input type="tel" class="form-control" id="telephone" name="telephone" value="<?= isset($_POST['telephone']) ? htmlspecialchars($_POST['telephone']) : '' ?>" required>
                        </div>
                        <?php endif; ?>
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="4" required><?= isset($_POST['message']) ? htmlspecialchars($_POST['message']) : "Bonjour, je suis intéressé(e) par ce bien immobilier (Réf: {$bien['reference']}). Merci de me contacter pour plus d'informations." ?></textarea>
                        </div>
                        <button type="submit" name="envoyer_message" class="btn btn-primary w-100">Envoyer</button>
                        <?php if (!isset($_SESSION['id_utilisateur'])): ?>
                        <div class="mt-2 text-center">
                            <small class="text-muted">Ou <a href="login.php?redirect=bien.php?id=<?= $id_bien ?>">connectez-vous</a> pour envoyer ce message</small>
                        </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header bg-light">
                    <h3 class="h5 mb-0">Informations</h3>
                </div>
                <div class="card-body">
                    <p><i class="fas fa-calendar-alt me-2 text-primary"></i> Publié le: <?= date('d/m/Y', strtotime($bien['date_publication'])) ?></p>
                    <p><i class="fas fa-eye me-2 text-primary"></i> Référence: <?= htmlspecialchars($bien['reference']) ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
