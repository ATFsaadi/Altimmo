<?php
include 'includes/header.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id_utilisateur'])) {
    header('Location: login.php');
    exit();
}

$id_utilisateur = $_SESSION['id_utilisateur'];

// Récupérer les informations de l'utilisateur
$req_user = $bdd->prepare("SELECT * FROM utilisateurs WHERE id_utilisateur = ?");
$req_user->execute([$id_utilisateur]);
$utilisateur = $req_user->fetch(PDO::FETCH_ASSOC);

// Traitement de l'envoi d'un nouveau message
$message_envoye = false;
$erreurs = [];

if (isset($_POST['envoyer_message'])) {
    // Vérification des champs
    if (empty($_POST['destinataire'])) {
        $erreurs[] = "Veuillez sélectionner un destinataire";
    }
    
    if (empty($_POST['sujet'])) {
        $erreurs[] = "Veuillez saisir un sujet";
    }
    
    if (empty($_POST['contenu'])) {
        $erreurs[] = "Veuillez saisir un message";
    }
    
    // Si pas d'erreurs, enregistrer le message
    if (empty($erreurs)) {
        $destinataire = intval($_POST['destinataire']);
        $sujet = htmlspecialchars($_POST['sujet']);
        $contenu = htmlspecialchars($_POST['contenu']);
        
        // Vérifier si le message est lié à un bien immobilier
        $id_bien = isset($_POST['id_bien']) ? intval($_POST['id_bien']) : null;
        
        // Insérer le message dans la base de données
        if ($id_bien) {
            // Message lié à un bien immobilier
            $req_message = $bdd->prepare("INSERT INTO messages (id_expediteur, id_destinataire, sujet, contenu, date_envoi, id_bien) VALUES (?, ?, ?, ?, NOW(), ?)");
            $req_message->execute([
                $id_utilisateur,
                $destinataire,
                $sujet,
                $contenu,
                $id_bien
            ]);
        } else {
            // Message standard
            $req_message = $bdd->prepare("INSERT INTO messages (id_expediteur, id_destinataire, sujet, contenu, date_envoi) VALUES (?, ?, ?, ?, NOW())");
            $req_message->execute([
                $id_utilisateur,
                $destinataire,
                $sujet,
                $contenu
            ]);
        }
        
        $message_envoye = true;
    }
}

// Récupérer les messages reçus avec informations sur les biens immobiliers associés
$req_recus = $bdd->prepare("
    SELECT m.*, u.nom, u.prenom, b.titre as bien_titre, b.reference as bien_reference, b.id_bien
    FROM messages m 
    JOIN utilisateurs u ON m.id_expediteur = u.id_utilisateur 
    LEFT JOIN biens b ON m.id_bien = b.id_bien
    WHERE m.id_destinataire = ? 
    ORDER BY m.date_envoi DESC
");
$req_recus->execute([$id_utilisateur]);
$messages_recus = $req_recus->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les messages envoyés avec informations sur les biens immobiliers associés
$req_envoyes = $bdd->prepare("
    SELECT m.*, u.nom, u.prenom, b.titre as bien_titre, b.reference as bien_reference, b.id_bien
    FROM messages m 
    JOIN utilisateurs u ON m.id_destinataire = u.id_utilisateur 
    LEFT JOIN biens b ON m.id_bien = b.id_bien
    WHERE m.id_expediteur = ? 
    ORDER BY m.date_envoi DESC
");
$req_envoyes->execute([$id_utilisateur]);
$messages_envoyes = $req_envoyes->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les agents immobiliers pour le formulaire d'envoi
$req_agents = $bdd->prepare("SELECT id_utilisateur, nom, prenom FROM utilisateurs WHERE niveau_acces >= 2 ORDER BY nom, prenom");
$req_agents->execute();
$agents = $req_agents->fetchAll(PDO::FETCH_ASSOC);

// Récupérer le détail d'un message si demandé
$message_detail = null;
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_message = intval($_GET['id']);
    
    // Vérifier que l'utilisateur est bien le destinataire ou l'expéditeur du message
    $req_message = $bdd->prepare("
        SELECT m.*, 
               exp.nom as exp_nom, exp.prenom as exp_prenom, 
               dest.nom as dest_nom, dest.prenom as dest_prenom,
               b.titre as bien_titre, b.reference as bien_reference, b.id_bien
        FROM messages m 
        JOIN utilisateurs exp ON m.id_expediteur = exp.id_utilisateur 
        JOIN utilisateurs dest ON m.id_destinataire = dest.id_utilisateur 
        LEFT JOIN biens b ON m.id_bien = b.id_bien
        WHERE m.id_message = ? AND (m.id_expediteur = ? OR m.id_destinataire = ?)
    ");
    $req_message->execute([$id_message, $id_utilisateur, $id_utilisateur]);
    $message_detail = $req_message->fetch(PDO::FETCH_ASSOC);
    
    // Marquer le message comme lu si l'utilisateur est le destinataire
    if ($message_detail && $message_detail['id_destinataire'] == $id_utilisateur && !$message_detail['lu']) {
        $req_lu = $bdd->prepare("UPDATE messages SET lu = 1 WHERE id_message = ?");
        $req_lu->execute([$id_message]);
        $message_detail['lu'] = 1;
    }
    
    // Si le message est lié à un bien, récupérer les détails du bien
    if ($message_detail && $message_detail['id_bien']) {
        $req_bien = $bdd->prepare("SELECT * FROM biens WHERE id_bien = ?");
        $req_bien->execute([$message_detail['id_bien']]);
        $bien_detail = $req_bien->fetch(PDO::FETCH_ASSOC);
        $message_detail['bien_detail'] = $bien_detail;
    }
}
?>

<div class="container mt-4">
    <h1 class="mb-4">Messagerie</h1>
    
    <?php if ($message_envoye): ?>
    <div class="alert alert-success">
        Votre message a bien été envoyé.
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
        <!-- Sidebar de la messagerie -->
        <div class="col-md-3">
            <div class="list-group mb-4">
                <a href="#nouveauMessage" class="list-group-item list-group-item-action" data-bs-toggle="collapse">
                    <i class="fas fa-pen me-2"></i> Nouveau message
                </a>
                <a href="#messagesRecus" class="list-group-item list-group-item-action active" data-bs-toggle="tab">
                    <i class="fas fa-inbox me-2"></i> Boîte de réception
                    <?php 
                    $nb_non_lus = 0;
                    foreach ($messages_recus as $message) {
                        if (!$message['lu']) $nb_non_lus++;
                    }
                    if ($nb_non_lus > 0): 
                    ?>
                    <span class="badge bg-danger float-end"><?= $nb_non_lus ?></span>
                    <?php endif; ?>
                </a>
                <a href="#messagesEnvoyes" class="list-group-item list-group-item-action" data-bs-toggle="tab">
                    <i class="fas fa-paper-plane me-2"></i> Messages envoyés
                </a>
            </div>
        </div>
        
        <!-- Contenu principal -->
        <div class="col-md-9">
            <!-- Formulaire de nouveau message -->
            <div class="collapse mb-4" id="nouveauMessage">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Nouveau message</h5>
                    </div>
                    <div class="card-body">
                        <form method="post" action="">
                            <div class="mb-3">
                                <label for="destinataire" class="form-label">Destinataire</label>
                                <select class="form-select" id="destinataire" name="destinataire" required>
                                    <option value="">Sélectionnez un destinataire</option>
                                    <?php foreach ($agents as $agent): ?>
                                    <option value="<?= $agent['id_utilisateur'] ?>"><?= htmlspecialchars($agent['prenom']) ?> <?= htmlspecialchars($agent['nom']) ?> (Agent)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="sujet" class="form-label">Sujet</label>
                                <input type="text" class="form-control" id="sujet" name="sujet" required>
                            </div>
                            <div class="mb-3">
                                <label for="contenu" class="form-label">Message</label>
                                <textarea class="form-control" id="contenu" name="contenu" rows="5" required></textarea>
                            </div>
                            <button type="submit" name="envoyer_message" class="btn btn-primary">Envoyer</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Détail d'un message -->
            <?php if ($message_detail): ?>
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><?= htmlspecialchars($message_detail['sujet']) ?></h5>
                    <a href="messages.php" class="btn btn-sm btn-outline-secondary">Retour</a>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <div>
                            <strong>De:</strong> <?= htmlspecialchars($message_detail['exp_prenom']) ?> <?= htmlspecialchars($message_detail['exp_nom']) ?>
                            <br>
                            <strong>À:</strong> <?= htmlspecialchars($message_detail['dest_prenom']) ?> <?= htmlspecialchars($message_detail['dest_nom']) ?>
                            <?php if (!empty($message_detail['bien_titre'])): ?>
                            <br>
                            <strong>Bien:</strong> <a href="bien.php?id=<?= $message_detail['id_bien'] ?>"><?= htmlspecialchars($message_detail['bien_titre']) ?></a> (Réf: <?= htmlspecialchars($message_detail['bien_reference']) ?>)
                            <?php endif; ?>
                        </div>
                        <div class="text-muted">
                            <?= date('d/m/Y à H:i', strtotime($message_detail['date_envoi'])) ?>
                        </div>
                    </div>
                    <hr>
                    <div class="message-content">
                        <?= nl2br(htmlspecialchars($message_detail['contenu'])) ?>
                    </div>
                    
                    <?php if ($message_detail['id_expediteur'] != $id_utilisateur): ?>
                    <hr>
                    <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#repondreMessage">
                        <i class="fas fa-reply me-1"></i> Répondre
                    </button>
                    
                    <div class="collapse mt-3" id="repondreMessage">
                        <form method="post" action="">
                            <input type="hidden" name="destinataire" value="<?= $message_detail['id_expediteur'] ?>">
                            <input type="hidden" name="sujet" value="RE: <?= htmlspecialchars($message_detail['sujet']) ?>">
                            <?php if (!empty($message_detail['id_bien'])): ?>
                            <input type="hidden" name="id_bien" value="<?= $message_detail['id_bien'] ?>">
                            <?php endif; ?>
                            <div class="mb-3">
                                <label for="contenu_reponse" class="form-label">Votre réponse</label>
                                <textarea class="form-control" id="contenu_reponse" name="contenu" rows="5" required></textarea>
                            </div>
                            <button type="submit" name="envoyer_message" class="btn btn-primary">Envoyer</button>
                        </form>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Onglets des messages -->
            <?php if (!$message_detail): ?>
            <div class="tab-content">
                <!-- Messages reçus -->
                <div class="tab-pane fade show active" id="messagesRecus">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Boîte de réception</h5>
                        </div>
                        <div class="card-body p-0">
                            <?php if (empty($messages_recus)): ?>
                            <div class="p-4 text-center text-muted">
                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                <p>Vous n'avez pas de messages dans votre boîte de réception.</p>
                            </div>
                            <?php else: ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($messages_recus as $message): ?>
                                <a href="messages.php?id=<?= $message['id_message'] ?>" class="list-group-item list-group-item-action <?= !$message['lu'] ? 'fw-bold' : '' ?>">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-1">
                                            <?= !$message['lu'] ? '<i class="fas fa-circle text-primary me-2" style="font-size: 8px;"></i>' : '' ?>
                                            <?= htmlspecialchars($message['prenom']) ?> <?= htmlspecialchars($message['nom']) ?>
                                        </h6>
                                        <small class="text-muted"><?= date('d/m/Y', strtotime($message['date_envoi'])) ?></small>
                                    </div>
                                    <p class="mb-1"><?= htmlspecialchars($message['sujet']) ?></p>
                                    <?php if (!empty($message['bien_titre'])): ?>
                                    <small class="text-primary"><i class="fas fa-home me-1"></i> Bien: <a href="bien.php?id=<?= $message['id_bien'] ?>"><?= htmlspecialchars($message['bien_titre']) ?></a> (Réf: <?= htmlspecialchars($message['bien_reference']) ?>)</small><br>
                                    <?php endif; ?>
                                    <small class="text-muted"><?= substr(htmlspecialchars($message['contenu']), 0, 100) ?>...</small>
                                </a>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Messages envoyés -->
                <div class="tab-pane fade" id="messagesEnvoyes">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Messages envoyés</h5>
                        </div>
                        <div class="card-body p-0">
                            <?php if (empty($messages_envoyes)): ?>
                            <div class="p-4 text-center text-muted">
                                <i class="fas fa-paper-plane fa-3x mb-3"></i>
                                <p>Vous n'avez pas envoyé de messages.</p>
                            </div>
                            <?php else: ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($messages_envoyes as $message): ?>
                                <a href="messages.php?id=<?= $message['id_message'] ?>" class="list-group-item list-group-item-action">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-1">À: <?= htmlspecialchars($message['prenom']) ?> <?= htmlspecialchars($message['nom']) ?></h6>
                                        <small class="text-muted"><?= date('d/m/Y', strtotime($message['date_envoi'])) ?></small>
                                    </div>
                                    <p class="mb-1"><?= htmlspecialchars($message['sujet']) ?></p>
                                    <?php if (!empty($message['bien_titre'])): ?>
                                    <small class="text-primary"><i class="fas fa-home me-1"></i> Bien: <a href="bien.php?id=<?= $message['id_bien'] ?>"><?= htmlspecialchars($message['bien_titre']) ?></a> (Réf: <?= htmlspecialchars($message['bien_reference']) ?>)</small><br>
                                    <?php endif; ?>
                                    <small class="text-muted"><?= substr(htmlspecialchars($message['contenu']), 0, 100) ?>...</small>
                                </a>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
