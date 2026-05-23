<?php
include 'includes/header.php';

// Vérifier si l'utilisateur est connecté et est un administrateur
if (!estConnecte() || !estAdmin()) {
    header('Location: login.php');
    exit();
}

// Initialisation des variables
$success = false;
$erreur = '';
$tab_active = isset($_GET['tab']) ? $_GET['tab'] : 'utilisateurs';

// Traitement de la modification du niveau d'accès d'un utilisateur
if (isset($_POST['modifier_niveau_acces'])) {
    $id_utilisateur = intval($_POST['id_utilisateur']);
    $niveau_acces = intval($_POST['niveau_acces']);
    
    if ($id_utilisateur != $_SESSION['id_utilisateur']) { // Empêcher de modifier son propre niveau
        $req = $bdd->prepare("UPDATE utilisateurs SET niveau_acces = ? WHERE id_utilisateur = ?");
        $req->execute([$niveau_acces, $id_utilisateur]);
        $success = "Le niveau d'accès de l'utilisateur a été modifié avec succès.";
    } else {
        $erreur = "Vous ne pouvez pas modifier votre propre niveau d'accès.";
    }
}

// Traitement de la suppression d'un utilisateur
if (isset($_GET['supprimer_utilisateur']) && !empty($_GET['supprimer_utilisateur'])) {
    $id_utilisateur = intval($_GET['supprimer_utilisateur']);
    
    if ($id_utilisateur != $_SESSION['id_utilisateur']) { // Empêcher de se supprimer soi-même
        // Supprimer les messages associés
        $req = $bdd->prepare("DELETE FROM messages WHERE id_expediteur = ? OR id_destinataire = ?");
        $req->execute([$id_utilisateur, $id_utilisateur]);
        
        // Récupérer les biens de l'utilisateur pour supprimer les images
        $req = $bdd->prepare("SELECT id_bien FROM biens WHERE id_agent = ?");
        $req->execute([$id_utilisateur]);
        $biens = $req->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($biens as $bien) {
            // Supprimer les images associées au bien
            $req = $bdd->prepare("DELETE FROM images_bien WHERE id_bien = ?");
            $req->execute([$bien['id_bien']]);
        }
        
        // Supprimer les biens
        $req = $bdd->prepare("DELETE FROM biens WHERE id_agent = ?");
        $req->execute([$id_utilisateur]);
        
        // Supprimer l'utilisateur
        $req = $bdd->prepare("DELETE FROM utilisateurs WHERE id_utilisateur = ?");
        $req->execute([$id_utilisateur]);
        
        $success = "L'utilisateur a été supprimé avec succès.";
    } else {
        $erreur = "Vous ne pouvez pas supprimer votre propre compte.";
    }
}

// Traitement de la suppression d'un bien
if (isset($_GET['supprimer_bien']) && !empty($_GET['supprimer_bien'])) {
    $id_bien = intval($_GET['supprimer_bien']);
    
    // Supprimer les images associées
    $req = $bdd->prepare("SELECT url_image FROM images_bien WHERE id_bien = ?");
    $req->execute([$id_bien]);
    $images = $req->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($images as $image) {
        if (file_exists($image['url_image'])) {
            unlink($image['url_image']);
        }
    }
    
    $req = $bdd->prepare("DELETE FROM images_bien WHERE id_bien = ?");
    $req->execute([$id_bien]);
    
    // Supprimer le bien
    $req = $bdd->prepare("DELETE FROM biens WHERE id_bien = ?");
    $req->execute([$id_bien]);
    
    $success = "Le bien immobilier a été supprimé avec succès.";
}

// Récupérer les statistiques
$stats = [
    'nb_utilisateurs' => 0,
    'nb_biens' => 0,
    'nb_messages' => 0,
    'nb_agents' => 0
];

$req = $bdd->query("SELECT COUNT(*) AS nb FROM utilisateurs");
$stats['nb_utilisateurs'] = $req->fetch()['nb'];

$req = $bdd->query("SELECT COUNT(*) AS nb FROM biens");
$stats['nb_biens'] = $req->fetch()['nb'];

$req = $bdd->query("SELECT COUNT(*) AS nb FROM messages");
$stats['nb_messages'] = $req->fetch()['nb'];

$req = $bdd->query("SELECT COUNT(*) AS nb FROM utilisateurs WHERE niveau_acces = 2");
$stats['nb_agents'] = $req->fetch()['nb'];

// Récupérer les utilisateurs
$req = $bdd->query("SELECT * FROM utilisateurs ORDER BY date_inscription DESC");
$utilisateurs = $req->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les biens
$req = $bdd->query("
    SELECT b.*, u.nom, u.prenom 
    FROM biens b 
    JOIN utilisateurs u ON b.id_agent = u.id_utilisateur 
    ORDER BY b.date_publication DESC
");
$biens = $req->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les messages récents
$req = $bdd->query("
    SELECT m.*, 
           exp.nom AS expediteur_nom, exp.prenom AS expediteur_prenom,
           dest.nom AS destinataire_nom, dest.prenom AS destinataire_prenom
    FROM messages m
    LEFT JOIN utilisateurs exp ON m.id_expediteur = exp.id_utilisateur
    JOIN utilisateurs dest ON m.id_destinataire = dest.id_utilisateur
    ORDER BY m.date_envoi DESC
    LIMIT 20
");
$messages = $req->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-4">
    <h1 class="mb-4">Administration</h1>
    
    <?php if ($success): ?>
    <div class="alert alert-success">
        <?= $success ?>
    </div>
    <?php endif; ?>
    
    <?php if ($erreur): ?>
    <div class="alert alert-danger">
        <?= $erreur ?>
    </div>
    <?php endif; ?>
    
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Utilisateurs</h6>
                            <h2 class="mb-0"><?= $stats['nb_utilisateurs'] ?></h2>
                        </div>
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Biens immobiliers</h6>
                            <h2 class="mb-0"><?= $stats['nb_biens'] ?></h2>
                        </div>
                        <i class="fas fa-home fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Messages</h6>
                            <h2 class="mb-0"><?= $stats['nb_messages'] ?></h2>
                        </div>
                        <i class="fas fa-envelope fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Agents immobiliers</h6>
                            <h2 class="mb-0"><?= $stats['nb_agents'] ?></h2>
                        </div>
                        <i class="fas fa-user-tie fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link <?= $tab_active == 'utilisateurs' ? 'active' : '' ?>" href="?tab=utilisateurs">Utilisateurs</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $tab_active == 'biens' ? 'active' : '' ?>" href="?tab=biens">Biens immobiliers</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $tab_active == 'messages' ? 'active' : '' ?>" href="?tab=messages">Messages récents</a>
        </li>
    </ul>
    
    <?php if ($tab_active == 'utilisateurs'): ?>
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0">Gestion des utilisateurs</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                            <th>Niveau d'accès</th>
                            <th>Date d'inscription</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($utilisateurs as $utilisateur): ?>
                        <tr>
                            <td><?= $utilisateur['id_utilisateur'] ?></td>
                            <td><?= htmlspecialchars($utilisateur['prenom'] . ' ' . $utilisateur['nom']) ?></td>
                            <td><?= htmlspecialchars($utilisateur['email']) ?></td>
                            <td><?= htmlspecialchars($utilisateur['telephone']) ?></td>
                            <td>
                                <form method="post" action="" class="d-flex">
                                    <input type="hidden" name="id_utilisateur" value="<?= $utilisateur['id_utilisateur'] ?>">
                                    <select name="niveau_acces" class="form-select form-select-sm me-2" <?= $utilisateur['id_utilisateur'] == $_SESSION['id_utilisateur'] ? 'disabled' : '' ?>>
                                        <option value="1" <?= $utilisateur['niveau_acces'] == 1 ? 'selected' : '' ?>>Utilisateur</option>
                                        <option value="2" <?= $utilisateur['niveau_acces'] == 2 ? 'selected' : '' ?>>Agent</option>
                                        <option value="3" <?= $utilisateur['niveau_acces'] == 3 ? 'selected' : '' ?>>Administrateur</option>
                                    </select>
                                    <button type="submit" name="modifier_niveau_acces" class="btn btn-sm btn-primary" <?= $utilisateur['id_utilisateur'] == $_SESSION['id_utilisateur'] ? 'disabled' : '' ?>>
                                        <i class="fas fa-save"></i>
                                    </button>
                                </form>
                            </td>
                            <td><?= date('d/m/Y', strtotime($utilisateur['date_inscription'])) ?></td>
                            <td>
                                <?php if ($utilisateur['id_utilisateur'] != $_SESSION['id_utilisateur']): ?>
                                <a href="?tab=utilisateurs&supprimer_utilisateur=<?= $utilisateur['id_utilisateur'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.')">
                                    <i class="fas fa-trash"></i>
                                </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if ($tab_active == 'biens'): ?>
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0">Gestion des biens immobiliers</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Référence</th>
                            <th>Titre</th>
                            <th>Type</th>
                            <th>Statut</th>
                            <th>Prix</th>
                            <th>Agent</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($biens as $bien): ?>
                        <tr>
                            <td><?= $bien['id_bien'] ?></td>
                            <td><?= htmlspecialchars($bien['reference']) ?></td>
                            <td><?= htmlspecialchars($bien['titre']) ?></td>
                            <td><?= htmlspecialchars($bien['type']) ?></td>
                            <td>
                                <span class="badge bg-<?= $bien['statut'] == 'À vendre' ? 'success' : ($bien['statut'] == 'À louer' ? 'primary' : 'secondary') ?>">
                                    <?= htmlspecialchars($bien['statut']) ?>
                                </span>
                            </td>
                            <td><?= number_format($bien['prix'], 0, ',', ' ') ?> €</td>
                            <td><?= htmlspecialchars($bien['prenom'] . ' ' . $bien['nom']) ?></td>
                            <td><?= date('d/m/Y', strtotime($bien['date_publication'])) ?></td>
                            <td>
                                <a href="bien.php?id=<?= $bien['id_bien'] ?>" class="btn btn-sm btn-info" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="?tab=biens&supprimer_bien=<?= $bien['id_bien'] ?>" class="btn btn-sm btn-danger" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce bien ? Cette action est irréversible.')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if ($tab_active == 'messages'): ?>
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0">Messages récents</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Expéditeur</th>
                            <th>Destinataire</th>
                            <th>Sujet</th>
                            <th>Date</th>
                            <th>Lu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($messages as $message): ?>
                        <tr>
                            <td><?= $message['id_message'] ?></td>
                            <td>
                                <?php if ($message['id_expediteur']): ?>
                                    <?= htmlspecialchars($message['expediteur_prenom'] . ' ' . $message['expediteur_nom']) ?>
                                <?php else: ?>
                                    <span class="text-muted">Contact site</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($message['destinataire_prenom'] . ' ' . $message['destinataire_nom']) ?></td>
                            <td><?= htmlspecialchars($message['sujet']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($message['date_envoi'])) ?></td>
                            <td>
                                <?php if ($message['lu']): ?>
                                    <span class="badge bg-success">Lu</span>
                                <?php else: ?>
                                    <span class="badge bg-warning">Non lu</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
