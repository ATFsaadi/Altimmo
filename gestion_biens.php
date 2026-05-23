<?php
include 'includes/header.php';

// Vérifier si l'utilisateur est connecté et est un agent
if (!estConnecte() || !estAgent()) {
    header('Location: login.php');
    exit();
}

$id_utilisateur = $_SESSION['id_utilisateur'];

// Traitement de l'ajout/modification d'un bien
$success = false;
$erreurs = [];
$bien = [
    'id_bien' => '',
    'titre' => '',
    'description' => '',
    'type' => '',
    'statut' => '',
    'prix' => '',
    'surface' => '',
    'nb_pieces' => '',
    'nb_chambres' => '',
    'nb_salles_bain' => '',
    'adresse' => '',
    'ville' => '',
    'code_postal' => '',
    'annee_construction' => '',
    'classe_energie' => '',
    'garage' => 0,
    'jardin' => 0,
    'piscine' => 0,
    'reference' => ''
];

// Si on modifie un bien existant
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_bien = intval($_GET['id']);
    
    // Récupérer les informations du bien
    $req = $bdd->prepare("SELECT * FROM biens WHERE id_bien = ? AND id_agent = ?");
    $req->execute([$id_bien, $id_utilisateur]);
    $bien_existant = $req->fetch(PDO::FETCH_ASSOC);
    
    if ($bien_existant) {
        $bien = $bien_existant;
    } else {
        header('Location: gestion_biens.php');
        exit();
    }
}

// Traitement du formulaire
if (isset($_POST['enregistrer_bien'])) {
    // Récupération des données du formulaire
    $bien = [
        'id_bien' => isset($_POST['id_bien']) ? intval($_POST['id_bien']) : null,
        'titre' => htmlspecialchars($_POST['titre']),
        'description' => htmlspecialchars($_POST['description']),
        'type' => htmlspecialchars($_POST['type']),
        'statut' => htmlspecialchars($_POST['statut']),
        'prix' => intval($_POST['prix']),
        'surface' => floatval($_POST['surface']),
        'nb_pieces' => intval($_POST['nb_pieces']),
        'nb_chambres' => intval($_POST['nb_chambres']),
        'nb_salles_bain' => intval($_POST['nb_salles_bain']),
        'adresse' => htmlspecialchars($_POST['adresse']),
        'ville' => htmlspecialchars($_POST['ville']),
        'code_postal' => htmlspecialchars($_POST['code_postal']),
        'annee_construction' => intval($_POST['annee_construction']),
        'classe_energie' => htmlspecialchars($_POST['classe_energie']),
        'garage' => isset($_POST['garage']) ? 1 : 0,
        'jardin' => isset($_POST['jardin']) ? 1 : 0,
        'piscine' => isset($_POST['piscine']) ? 1 : 0,
        'reference' => htmlspecialchars($_POST['reference'])
    ];
    
    // Validation des champs obligatoires
    if (empty($bien['titre'])) {
        $erreurs[] = "Le titre est obligatoire";
    }
    
    if (empty($bien['description'])) {
        $erreurs[] = "La description est obligatoire";
    }
    
    if (empty($bien['prix'])) {
        $erreurs[] = "Le prix est obligatoire";
    }
    
    if (empty($bien['surface'])) {
        $erreurs[] = "La surface est obligatoire";
    }
    
    if (empty($bien['adresse'])) {
        $erreurs[] = "L'adresse est obligatoire";
    }
    
    if (empty($bien['ville'])) {
        $erreurs[] = "La ville est obligatoire";
    }
    
    if (empty($bien['code_postal'])) {
        $erreurs[] = "Le code postal est obligatoire";
    }
    
    // Si pas d'erreurs, enregistrer le bien
    if (empty($erreurs)) {
        // Générer une référence si elle n'existe pas
        if (empty($bien['reference'])) {
            $bien['reference'] = 'ALT-' . strtoupper(substr($bien['type'], 0, 3)) . '-' . rand(1000, 9999);
        }
        
        // Si c'est un nouveau bien
        if (empty($bien['id_bien'])) {
            $req = $bdd->prepare("
                INSERT INTO biens (
                    titre, description, type, statut, prix, surface, nb_pieces, nb_chambres, 
                    nb_salles_bain, adresse, ville, code_postal, annee_construction, 
                    classe_energie, garage, jardin, piscine, reference, id_agent, date_publication
                ) VALUES (
                    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW()
                )
            ");
            
            $req->execute([
                $bien['titre'], $bien['description'], $bien['type'], $bien['statut'], $bien['prix'],
                $bien['surface'], $bien['nb_pieces'], $bien['nb_chambres'], $bien['nb_salles_bain'],
                $bien['adresse'], $bien['ville'], $bien['code_postal'], $bien['annee_construction'],
                $bien['classe_energie'], $bien['garage'], $bien['jardin'], $bien['piscine'],
                $bien['reference'], $id_utilisateur
            ]);
            
            $id_bien = $bdd->lastInsertId();
            $success = "Le bien a été ajouté avec succès.";
        } else {
            // Modification d'un bien existant
            $req = $bdd->prepare("
                UPDATE biens SET
                    titre = ?, description = ?, type = ?, statut = ?, prix = ?, surface = ?,
                    nb_pieces = ?, nb_chambres = ?, nb_salles_bain = ?, adresse = ?, ville = ?,
                    code_postal = ?, annee_construction = ?, classe_energie = ?, garage = ?,
                    jardin = ?, piscine = ?, reference = ?
                WHERE id_bien = ? AND id_agent = ?
            ");
            
            $req->execute([
                $bien['titre'], $bien['description'], $bien['type'], $bien['statut'], $bien['prix'],
                $bien['surface'], $bien['nb_pieces'], $bien['nb_chambres'], $bien['nb_salles_bain'],
                $bien['adresse'], $bien['ville'], $bien['code_postal'], $bien['annee_construction'],
                $bien['classe_energie'], $bien['garage'], $bien['jardin'], $bien['piscine'],
                $bien['reference'], $bien['id_bien'], $id_utilisateur
            ]);
            
            $id_bien = $bien['id_bien'];
            $success = "Le bien a été modifié avec succès.";
        }
        
        // Traitement des images
        if (!empty($_FILES['images']['name'][0])) {
            $dossier_upload = 'uploads/';
            
            // Créer le dossier s'il n'existe pas
            if (!file_exists($dossier_upload)) {
                mkdir($dossier_upload, 0777, true);
            }
            
            // Parcourir les images téléchargées
            foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                    $nom_fichier = $_FILES['images']['name'][$key];
                    $extension = strtolower(pathinfo($nom_fichier, PATHINFO_EXTENSION));
                    
                    // Vérifier l'extension
                    $extensions_autorisees = ['jpg', 'jpeg', 'png', 'gif'];
                    if (in_array($extension, $extensions_autorisees)) {
                        // Générer un nom unique
                        $nouveau_nom = uniqid('bien_' . $id_bien . '_') . '.' . $extension;
                        $chemin_destination = $dossier_upload . $nouveau_nom;
                        
                        // Déplacer le fichier
                        if (move_uploaded_file($tmp_name, $chemin_destination)) {
                            // Déterminer si c'est l'image principale
                            $est_principale = isset($_POST['image_principale']) && $_POST['image_principale'] == $key ? 1 : 0;
                            
                            // Si c'est l'image principale, mettre à jour les autres images
                            if ($est_principale) {
                                $req = $bdd->prepare("UPDATE images_bien SET est_principale = 0 WHERE id_bien = ?");
                                $req->execute([$id_bien]);
                            }
                            
                            // Enregistrer l'image dans la base de données
                            $req = $bdd->prepare("INSERT INTO images_bien (id_bien, url_image, est_principale) VALUES (?, ?, ?)");
                            $req->execute([$id_bien, $chemin_destination, $est_principale]);
                        }
                    }
                }
            }
        }
        
        // Redirection vers la page de gestion avec un message de succès
        header('Location: gestion_biens.php?success=' . urlencode($success));
        exit();
    }
}

// Supprimer un bien
if (isset($_GET['supprimer']) && !empty($_GET['supprimer'])) {
    $id_bien_supprimer = intval($_GET['supprimer']);
    
    // Vérifier que le bien appartient à l'agent
    $req = $bdd->prepare("SELECT COUNT(*) FROM biens WHERE id_bien = ? AND id_agent = ?");
    $req->execute([$id_bien_supprimer, $id_utilisateur]);
    
    if ($req->fetchColumn() > 0) {
        // Supprimer les images associées
        $req = $bdd->prepare("DELETE FROM images_bien WHERE id_bien = ?");
        $req->execute([$id_bien_supprimer]);
        
        // Supprimer le bien
        $req = $bdd->prepare("DELETE FROM biens WHERE id_bien = ?");
        $req->execute([$id_bien_supprimer]);
        
        $success = "Le bien a été supprimé avec succès.";
        header('Location: gestion_biens.php?success=' . urlencode($success));
        exit();
    }
}

// Récupérer la liste des biens de l'agent
$req = $bdd->prepare("SELECT * FROM biens WHERE id_agent = ? ORDER BY date_publication DESC");
$req->execute([$id_utilisateur]);
$biens = $req->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid mt-4">
    <h1 class="mb-4">Gestion des biens immobiliers</h1>
    
    <?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">
        <?= htmlspecialchars($_GET['success']) ?>
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
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><?= isset($bien['id_bien']) && !empty($bien['id_bien']) ? 'Modifier un bien' : 'Ajouter un nouveau bien' ?></h5>
                    <?php if (isset($bien['id_bien']) && !empty($bien['id_bien'])): ?>
                    <a href="gestion_biens.php" class="btn btn-sm btn-light">Nouveau bien</a>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <form method="post" action="" enctype="multipart/form-data">
                        <?php if (isset($bien['id_bien']) && !empty($bien['id_bien'])): ?>
                        <input type="hidden" name="id_bien" value="<?= $bien['id_bien'] ?>">
                        <?php endif; ?>
                        
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label for="titre" class="form-label">Titre du bien *</label>
                                <input type="text" class="form-control" id="titre" name="titre" value="<?= htmlspecialchars($bien['titre']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="reference" class="form-label">Référence</label>
                                <input type="text" class="form-control" id="reference" name="reference" value="<?= htmlspecialchars($bien['reference']) ?>" placeholder="Laissez vide pour générer automatiquement">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description *</label>
                            <textarea class="form-control" id="description" name="description" rows="4" required><?= htmlspecialchars($bien['description']) ?></textarea>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4 mb-3 mb-md-0">
                                <label for="type" class="form-label">Type de bien *</label>
                                <select class="form-select" id="type" name="type" required>
                                    <option value="">Sélectionnez</option>
                                    <option value="Maison" <?= $bien['type'] == 'Maison' ? 'selected' : '' ?>>Maison</option>
                                    <option value="Appartement" <?= $bien['type'] == 'Appartement' ? 'selected' : '' ?>>Appartement</option>
                                    <option value="Terrain" <?= $bien['type'] == 'Terrain' ? 'selected' : '' ?>>Terrain</option>
                                    <option value="Commerce" <?= $bien['type'] == 'Commerce' ? 'selected' : '' ?>>Commerce</option>
                                    <option value="Immeuble" <?= $bien['type'] == 'Immeuble' ? 'selected' : '' ?>>Immeuble</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3 mb-md-0">
                                <label for="statut" class="form-label">Statut *</label>
                                <select class="form-select" id="statut" name="statut" required>
                                    <option value="">Sélectionnez</option>
                                    <option value="À vendre" <?= $bien['statut'] == 'À vendre' ? 'selected' : '' ?>>À vendre</option>
                                    <option value="À louer" <?= $bien['statut'] == 'À louer' ? 'selected' : '' ?>>À louer</option>
                                    <option value="Vendu" <?= $bien['statut'] == 'Vendu' ? 'selected' : '' ?>>Vendu</option>
                                    <option value="Loué" <?= $bien['statut'] == 'Loué' ? 'selected' : '' ?>>Loué</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="prix" class="form-label">Prix (€) *</label>
                                <input type="number" class="form-control" id="prix" name="prix" value="<?= $bien['prix'] ?>" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-sm-6 col-md-3 mb-3 mb-md-0">
                                <label for="surface" class="form-label">Surface (m²) *</label>
                                <input type="number" step="0.01" class="form-control" id="surface" name="surface" value="<?= $bien['surface'] ?>" required>
                            </div>
                            <div class="col-sm-6 col-md-3 mb-3 mb-md-0">
                                <label for="nb_pieces" class="form-label">Nombre de pièces *</label>
                                <input type="number" class="form-control" id="nb_pieces" name="nb_pieces" value="<?= $bien['nb_pieces'] ?>" required>
                            </div>
                            <div class="col-sm-6 col-md-3 mb-3 mb-sm-0">
                                <label for="nb_chambres" class="form-label">Nombre de chambres</label>
                                <input type="number" class="form-control" id="nb_chambres" name="nb_chambres" value="<?= $bien['nb_chambres'] ?>">
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <label for="nb_salles_bain" class="form-label">Nombre de SDB</label>
                                <input type="number" class="form-control" id="nb_salles_bain" name="nb_salles_bain" value="<?= $bien['nb_salles_bain'] ?>">
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label for="adresse" class="form-label">Adresse *</label>
                                <input type="text" class="form-control" id="adresse" name="adresse" value="<?= htmlspecialchars($bien['adresse']) ?>" required>
                            </div>
                            <div class="col-sm-6 col-md-3 mb-3 mb-md-0">
                                <label for="ville" class="form-label">Ville *</label>
                                <input type="text" class="form-control" id="ville" name="ville" value="<?= htmlspecialchars($bien['ville']) ?>" required>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <label for="code_postal" class="form-label">Code postal *</label>
                                <input type="text" class="form-control" id="code_postal" name="code_postal" value="<?= htmlspecialchars($bien['code_postal']) ?>" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label for="annee_construction" class="form-label">Année de construction</label>
                                <input type="number" class="form-control" id="annee_construction" name="annee_construction" value="<?= $bien['annee_construction'] ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="classe_energie" class="form-label">Classe énergie</label>
                                <select class="form-select" id="classe_energie" name="classe_energie">
                                    <option value="">Sélectionnez</option>
                                    <option value="A" <?= $bien['classe_energie'] == 'A' ? 'selected' : '' ?>>A</option>
                                    <option value="B" <?= $bien['classe_energie'] == 'B' ? 'selected' : '' ?>>B</option>
                                    <option value="C" <?= $bien['classe_energie'] == 'C' ? 'selected' : '' ?>>C</option>
                                    <option value="D" <?= $bien['classe_energie'] == 'D' ? 'selected' : '' ?>>D</option>
                                    <option value="E" <?= $bien['classe_energie'] == 'E' ? 'selected' : '' ?>>E</option>
                                    <option value="F" <?= $bien['classe_energie'] == 'F' ? 'selected' : '' ?>>F</option>
                                    <option value="G" <?= $bien['classe_energie'] == 'G' ? 'selected' : '' ?>>G</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="d-flex flex-wrap">
                                    <div class="me-4 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="garage" name="garage" <?= $bien['garage'] ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="garage">Garage</label>
                                        </div>
                                    </div>
                                    <div class="me-4 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="jardin" name="jardin" <?= $bien['jardin'] ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="jardin">Jardin</label>
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="piscine" name="piscine" <?= $bien['piscine'] ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="piscine">Piscine</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="images" class="form-label">Images</label>
                            <input type="file" class="form-control" id="images" name="images[]" multiple accept="image/*">
                            <div class="form-text">Vous pouvez sélectionner plusieurs images. La première image sera l'image principale.</div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" name="enregistrer_bien" class="btn btn-primary">Enregistrer le bien</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-12 mt-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Mes biens immobiliers</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($biens)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-home fa-4x text-muted mb-3"></i>
                        <h6>Vous n'avez pas encore ajouté de biens immobiliers</h6>
                        <p class="text-muted">Utilisez le formulaire ci-dessus pour ajouter votre premier bien.</p>
                    </div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>Référence</th>
                                    <th>Titre</th>
                                    <th>Type</th>
                                    <th>Statut</th>
                                    <th>Prix</th>
                                    <th>Ville</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($biens as $b): ?>
                                <tr>
                                    <td><?= htmlspecialchars($b['reference']) ?></td>
                                    <td><?= htmlspecialchars($b['titre']) ?></td>
                                    <td><?= htmlspecialchars($b['type']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $b['statut'] == 'À vendre' ? 'success' : ($b['statut'] == 'À louer' ? 'primary' : 'secondary') ?>">
                                            <?= htmlspecialchars($b['statut']) ?>
                                        </span>
                                    </td>
                                    <td><?= number_format($b['prix'], 0, ',', ' ') ?> €</td>
                                    <td><?= htmlspecialchars($b['ville']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($b['date_publication'])) ?></td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            <a href="bien.php?id=<?= $b['id_bien'] ?>" class="btn btn-sm btn-info" title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="gestion_biens.php?id=<?= $b['id_bien'] ?>" class="btn btn-sm btn-warning" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="gestion_biens.php?supprimer=<?= $b['id_bien'] ?>" class="btn btn-sm btn-danger" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce bien ?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Ajout de CSS personnalisé pour éviter les chevauchements -->
<style>
    @media (max-width: 767.98px) {
        .table-responsive {
            overflow-x: auto;
        }
        .table th, .table td {
            white-space: nowrap;
        }
    }
</style>

<?php include 'includes/footer.php'; ?>
