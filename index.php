<?php
<<<<<<< HEAD

$sessionPath = __DIR__ . '/storage/sessions';
if (!is_dir($sessionPath)) {
    mkdir($sessionPath, 0775, true);
}
session_save_path($sessionPath);
session_start();

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/helpers.php';
require_once __DIR__ . '/config/database.php';

require_once __DIR__ . '/modele/modele.class.php';
require_once __DIR__ . '/modele/utilisateur.class.php';
require_once __DIR__ . '/modele/categorie.class.php';
require_once __DIR__ . '/modele/bien.class.php';
require_once __DIR__ . '/modele/message.class.php';

require_once __DIR__ . '/controleur/controleur.class.php';
require_once __DIR__ . '/controleur/clientControleur.class.php';
require_once __DIR__ . '/controleur/adminControleur.class.php';

$bdd = Database::getConnection();
$clientControleur = new ClientControleur($bdd);
$adminControleur = new AdminControleur($bdd);

$page = $_GET['page'] ?? 'accueil';

try {
    switch ($page) {
        case 'accueil':
            $clientControleur->accueil();
            break;
        case 'biens':
            $clientControleur->biens();
            break;
        case 'bien':
            $clientControleur->bien();
            break;
        case 'contact':
            $clientControleur->contact();
            break;
        case 'login':
            $clientControleur->login();
            break;
        case 'register':
            $clientControleur->register();
            break;
        case 'logout':
            $clientControleur->logout();
            break;
        case 'profile':
            $clientControleur->profile();
            break;
        case 'messages':
            $clientControleur->messages();
            break;
        case 'conditions':
            $clientControleur->pageTexte('conditions');
            break;
        case 'politique':
            $clientControleur->pageTexte('politique');
            break;
        case 'admin':
            $adminControleur->dashboard();
            break;
        case 'gestion':
            $adminControleur->gestionBiens();
            break;
        default:
            http_response_code(404);
            $clientControleur->erreur404();
    }
} catch (Throwable $e) {
    http_response_code(500);

    if (APP_DEBUG) {
        echo '<pre style="padding:20px;background:#fee;color:#900;">';
        echo e($e->getMessage()) . "\n\n" . e($e->getTraceAsString());
        echo '</pre>';
    } else {
        $clientControleur->erreur500();
    }
}
=======
include 'includes/header.php';

// Récupération des paramètres de recherche
$recherche = [];
$where_clauses = [];
$params = [];

if (isset($_GET['recherche'])) {
    // Recherche par ville ou code postal
    if (!empty($_GET['ville_cp'])) {
        $ville_cp = htmlspecialchars($_GET['ville_cp']);
        $where_clauses[] = "(ville LIKE ? OR cp LIKE ?)"; 
        $params[] = "%$ville_cp%";
        $params[] = "%$ville_cp%";
        $recherche['ville_cp'] = $ville_cp;
    }
    
    // Recherche par catégorie de bien (au lieu de type)
    if (!empty($_GET['type']) && $_GET['type'] != 'tous') {
        $type = intval($_GET['type']);
        $where_clauses[] = "cat_id = ?";
        $params[] = $type;
        $recherche['type'] = $type;
    }
    
    // Recherche par nombre de pièces
    if (!empty($_GET['nb_pieces']) && $_GET['nb_pieces'] != 'tous') {
        $nb_pieces = intval($_GET['nb_pieces']);
        if ($nb_pieces == 5) {
            $where_clauses[] = "pieces >= ?"; 
        } else {
            $where_clauses[] = "pieces = ?";
        }
        $params[] = $nb_pieces;
        $recherche['nb_pieces'] = $nb_pieces;
    }
    
    // Recherche par prix
    if (!empty($_GET['prix_min'])) {
        $prix_min = intval($_GET['prix_min']);
        $where_clauses[] = "prix >= ?";
        $params[] = $prix_min;
        $recherche['prix_min'] = $prix_min;
    }
    
    if (!empty($_GET['prix_max'])) {
        $prix_max = intval($_GET['prix_max']);
        $where_clauses[] = "prix <= ?";
        $params[] = $prix_max;
        $recherche['prix_max'] = $prix_max;
    }
    
    // Recherche par surface
    if (!empty($_GET['surface_min'])) {
        $surface_min = intval($_GET['surface_min']);
        $where_clauses[] = "superficie >= ?";
        $params[] = $surface_min;
        $recherche['surface_min'] = $surface_min;
    }
    
    if (!empty($_GET['surface_max'])) {
        $surface_max = intval($_GET['surface_max']);
        $where_clauses[] = "superficie <= ?";
        $params[] = $surface_max;
        $recherche['surface_max'] = $surface_max;
    }
}

// Récupération des biens immobiliers (version simplifiée)
$sql = "SELECT * FROM biens ORDER BY date_v DESC LIMIT 6";

try {
    $req = $bdd->query($sql);
    $biens = $req->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // En cas d'erreur, on continue sans afficher les biens
    error_log("Erreur lors de la récupération des biens : " . $e->getMessage());
    $biens = [];
}
?>

<!-- Barre de recherche améliorée -->
<div class="container mt-4">
    <div class="search-bar p-4 bg-light rounded shadow-sm text-center">
        <form class="row justify-content-center g-2" method="get" action="">
            <input type="hidden" name="recherche" value="1">
            <div class="col-md-6">
                <input
                    type="text"
                    name="ville_cp"
                    class="form-control form-control-lg rounded"
                    placeholder="Ville ou Code Postal"
                    value="<?= isset($recherche['ville_cp']) ? $recherche['ville_cp'] : '' ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-lg w-100 rounded">Rechercher</button>
            </div>
            <div class="col-12 mt-2">
                <a
                    href="#"
                    class="text-muted small"
                    data-bs-toggle="collapse"
                    data-bs-target="#advancedSearch">
                    <i class="fas fa-sliders-h"></i>
                    Recherche avancée
                </a>
            </div>
        

            <!-- Section Recherche avancée -->
            <div class="collapse mt-3 p-3 bg-white border rounded" id="advancedSearch">
                <div class="row g-2">
                    <div class="col-md-3">
                        <select class="form-select form-select-sm" name="type">
                            <option value="tous" <?= !isset($recherche['type']) ? 'selected' : '' ?>>Type du bien</option>
                            <option value="Maison" <?= isset($recherche['type']) && $recherche['type'] == 'Maison' ? 'selected' : '' ?>>Maison</option>
                            <option value="Appartement" <?= isset($recherche['type']) && $recherche['type'] == 'Appartement' ? 'selected' : '' ?>>Appartement</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select form-select-sm" name="nb_pieces">
                            <option value="tous" <?= !isset($recherche['nb_pieces']) ? 'selected' : '' ?>>Nombre de pièces</option>
                            <option value="1" <?= isset($recherche['nb_pieces']) && $recherche['nb_pieces'] == 1 ? 'selected' : '' ?>>1 pièce</option>
                            <option value="2" <?= isset($recherche['nb_pieces']) && $recherche['nb_pieces'] == 2 ? 'selected' : '' ?>>2 pièces</option>
                            <option value="3" <?= isset($recherche['nb_pieces']) && $recherche['nb_pieces'] == 3 ? 'selected' : '' ?>>3 pièces</option>
                            <option value="4" <?= isset($recherche['nb_pieces']) && $recherche['nb_pieces'] == 4 ? 'selected' : '' ?>>4 pièces</option>
                            <option value="5" <?= isset($recherche['nb_pieces']) && $recherche['nb_pieces'] == 5 ? 'selected' : '' ?>>+5 pièces</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="row g-1">
                            <div class="col-6">
                                <input
                                    type="number"
                                    name="prix_min"
                                    class="form-control form-control-sm"
                                    placeholder="Prix min (€)"
                                    value="<?= isset($recherche['prix_min']) ? $recherche['prix_min'] : '' ?>">
                            </div>
                            <div class="col-6">
                                <input
                                    type="number"
                                    name="prix_max"
                                    class="form-control form-control-sm"
                                    placeholder="Prix max (€)"
                                    value="<?= isset($recherche['prix_max']) ? $recherche['prix_max'] : '' ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="row g-1">
                            <div class="col-6">
                                <input
                                    type="number"
                                    name="surface_min"
                                    class="form-control form-control-sm"
                                    placeholder="Surface min (m²)"
                                    value="<?= isset($recherche['surface_min']) ? $recherche['surface_min'] : '' ?>">
                            </div>
                            <div class="col-6">
                                <input
                                    type="number"
                                    name="surface_max"
                                    class="form-control form-control-sm"
                                    placeholder="Surface max (m²)"
                                    value="<?= isset($recherche['surface_max']) ? $recherche['surface_max'] : '' ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Carrousel d'images -->
<div id="carouselExample" class="carousel slide mt-5" data-bs-ride="carousel" data-bs-interval="5000">
    <!-- Indicateurs -->
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#carouselExample" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
        <button type="button" data-bs-target="#carouselExample" data-bs-slide-to="1" aria-label="Slide 2"></button>
        <button type="button" data-bs-target="#carouselExample" data-bs-slide-to="2" aria-label="Slide 3"></button>
    </div>
    
    <!-- Slides -->
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="img/slide -hus.jpeg" class="d-block w-100" alt="Slide 1" style="height: 500px; object-fit: cover;">
            <div class="carousel-caption d-none d-md-block">
                <h2>Des biens d'exception</h2>
                <p>Découvrez notre sélection de propriétés haut de gamme</p>
            </div>
        </div>
        <div class="carousel-item">
            <img src="img/slide2.jpg" class="d-block w-100" alt="Slide 2" style="height: 500px; object-fit: cover;">
            <div class="carousel-caption d-none d-md-block">
                <h2>Votre maison idéale</h2>
                <p>Nous vous accompagnons dans votre projet immobilier</p>
            </div>
        </div>
        <div class="carousel-item">
            <img src="img/selide-con.jpeg" class="d-block w-100" alt="Slide 3" style="height: 500px; object-fit: cover;">
            <div class="carousel-caption d-none d-md-block">
                <h2>Expertise immobilière</h2>
                <p>Des agents expérimentés à votre service</p>
            </div>
        </div>
    </div>
    
    <!-- Contrôles de navigation -->
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Précédent</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Suivant</span>
    </button>
</div>

<!-- Section des annonces -->
<div class="container mt-4">
    <h2 class="text-center mb-4">Exclusive</h2>
    
    <!-- Section des biens immobiliers -->
    <div class="row">
        <h2 class="mb-4">Nos derniers biens</h2>
        <?php if (!empty($biens)): ?>
            <?php foreach ($biens as $bien): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <?php if (!empty($bien['image'])): ?>
                    <img src="<?= htmlspecialchars($bien['image']) ?>" class="card-img-top" alt="Bien immobilier" style="height: 200px; object-fit: cover;">
                    <?php else: ?>
                    <img src="img/exc1.jpg" class="card-img-top" alt="Bien immobilier" style="height: 200px; object-fit: cover;">
                    <?php endif; ?>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?= !empty($bien['titre']) ? htmlspecialchars($bien['titre']) : 'Bien à ' . htmlspecialchars($bien['ville']) ?></h5>
                        <p class="card-text">
                            <strong><?= number_format($bien['prix'], 0, ',', ' ') ?> €</strong><br>
                            <?= htmlspecialchars($bien['ville']) ?>, <?= htmlspecialchars($bien['cp']) ?><br>
                            <?= htmlspecialchars($bien['superficie']) ?> m², <?= htmlspecialchars($bien['pieces']) ?> pièce(s)
                        </p>
                        <a href="bien.php?id=<?= $bien['id_b'] ?>" class="btn btn-primary mt-auto">Voir plus</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <!-- Affichage des exemples statiques si aucun bien n'est disponible -->
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="img/exc1.jpg" class="card-img-top" alt="Annonce" style="height: 200px; object-fit: cover;">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Titre de l'annonce</h5>
                        <p class="card-text">Description courte de l'annonce.</p>
                        <a href="#" class="btn btn-primary mt-auto">Voir plus</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <img src="img/exc2.webp" class="card-img-top" alt="Annonce">
                    <div class="card-body">
                        <h5 class="card-title">Titre de l'annonce</h5>
                        <p class="card-text">Description courte de l'annonce.</p>
                        <a href="#" class="btn btn-primary">Voir plus</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <img src="img/exc3.webp" class="card-img-top" alt="Annonce">
                    <div class="card-body">
                        <h5 class="card-title">Titre de l'annonce</h5>
                        <p class="card-text">Description courte de l'annonce.</p>
                        <a href="#" class="btn btn-primary">Voir plus</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
include 'includes/footer.php';
?>
>>>>>>> a3a6479586a2984f840440d0b07222f8debfd793
