<?php
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
    
    // Note: Les colonnes garage, jardin et piscine n'existent pas dans la structure actuelle de la table
    // Nous les retirons donc des filtres pour le moment
}

// Construction de la requête SQL simplifiée
$sql = "SELECT * FROM biens";

if (!empty($where_clauses)) {
    $sql .= " WHERE " . implode(" AND ", $where_clauses);
}

$sql .= " ORDER BY date_v DESC";

try {
    // Exécution de la requête
    $req = $bdd->prepare($sql);
    $req->execute($params);
    $biens = $req->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // En cas d'erreur, on continue sans afficher les biens
    error_log("Erreur lors de la récupération des biens : " . $e->getMessage());
    $biens = [];
}

// Récupération des catégories de biens pour le filtre
$req_types = $bdd->query("SELECT id_cat, nom_cat FROM categories ORDER BY nom_cat");
$categories = $req_types->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- En-tête de la page -->
<div class="container-fluid bg-light py-5 text-center">
    <h1 class="display-4">Nos biens immobiliers</h1>
    <p class="lead">Découvrez notre sélection de propriétés à vendre et à louer</p>
</div>

<<!-- Barre de recherche -->
<div class="container mt-4">
    <div class="search-bar p-3 bg-light rounded shadow-sm">
        <form class="row g-2 justify-content-center" method="get" action="">
            <input type="hidden" name="recherche" value="1">

            <!-- Première ligne : Ville, Type de bien, Nombre de pièces -->
            <div class="col-12 col-md-4 text-center">
                <label for="ville_cp" class="form-label">Ville ou code postal</label>
                <input type="text" class="form-control input-sm" id="ville_cp" name="ville_cp" 
                       value="<?= isset($recherche['ville_cp']) ? $recherche['ville_cp'] : '' ?>">
            </div>

            <div class="col-12 col-md-4 text-center">
                <label for="type" class="form-label">Type de bien</label>
                <select class="form-select input-sm" id="type" name="type">
                    <option value="tous">Toutes les catégories</option>
                    <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id_cat'] ?>" <?= isset($recherche['type']) && $recherche['type'] == $cat['id_cat'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['nom_cat']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-12 col-md-4 text-center">
                <label for="nb_pieces" class="form-label">Nombre de pièces</label>
                <select class="form-select input-sm" id="nb_pieces" name="nb_pieces">
                    <option value="tous">Toutes</option>
                    <option value="1" <?= isset($recherche['nb_pieces']) && $recherche['nb_pieces'] == 1 ? 'selected' : '' ?>>1 pièce</option>
                    <option value="2" <?= isset($recherche['nb_pieces']) && $recherche['nb_pieces'] == 2 ? 'selected' : '' ?>>2 pièces</option>
                    <option value="3" <?= isset($recherche['nb_pieces']) && $recherche['nb_pieces'] == 3 ? 'selected' : '' ?>>3 pièces</option>
                    <option value="4" <?= isset($recherche['nb_pieces']) && $recherche['nb_pieces'] == 4 ? 'selected' : '' ?>>4 pièces</option>
                    <option value="5" <?= isset($recherche['nb_pieces']) && $recherche['nb_pieces'] == 5 ? 'selected' : '' ?>>5 pièces et plus</option>
                </select>
            </div>

            <!-- Deuxième ligne : Prix min, Prix max, Surface min, Surface max -->
            <div class="col-6 col-md-3">
                <input type="number" class="form-control input-sm text-center" id="prix_min" name="prix_min" 
                       placeholder="Prix min (€)"
                       value="<?= isset($recherche['prix_min']) ? $recherche['prix_min'] : '' ?>">
            </div>

            <div class="col-6 col-md-3">
                <input type="number" class="form-control input-sm text-center" id="prix_max" name="prix_max" 
                       placeholder="Prix max (€)"
                       value="<?= isset($recherche['prix_max']) ? $recherche['prix_max'] : '' ?>">
            </div>

            <div class="col-6 col-md-3">
                <input type="number" class="form-control input-sm text-center" id="surface_min" name="surface_min" 
                       placeholder="Surface min (m²)"
                       value="<?= isset($recherche['surface_min']) ? $recherche['surface_min'] : '' ?>">
            </div>

            <div class="col-6 col-md-3">
                <input type="number" class="form-control input-sm text-center" id="surface_max" name="surface_max" 
                       placeholder="Surface max (m²)"
                       value="<?= isset($recherche['surface_max']) ? $recherche['surface_max'] : '' ?>">
            </div>

            <!-- Boutons -->
            <div class="col-12 text-center mt-3">
                <button type="submit" class="btn btn-primary btn-sm px-4 rounded-pill">Rechercher</button>
                <a href="biens.php" class="btn btn-outline-secondary btn-sm ms-2 rounded-pill">Réinitialiser</a>
            </div>
        </form>
    </div>
</div>

<style>
   
</style>


<!-- Liste des biens -->
<div class="container mt-5 mb-5">
    <?php if (empty($biens)): ?>
    <div class="alert alert-info">
        Aucun bien immobilier ne correspond à votre recherche.
    </div>
    <?php else: ?>
    <h2 class="mb-4"><?= count($biens) ?> bien(s) trouvé(s)</h2>
    
    <div class="row">
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
    </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
