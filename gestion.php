<?php
// Inclusion du fichier d'en-tête
require_once 'includes/header.php';

// Redirection si l'utilisateur n'est pas connecté
if (!estConnecte()) {
    header('Location: login.php');
    exit();
}

// Traitement des actions
$action = isset($_GET['action']) ? $_GET['action'] : 'liste';
$id_bien = isset($_GET['id']) ? intval($_GET['id']) : 0;
$message = '';

// Fonction pour vérifier si l'utilisateur est propriétaire du bien
function estProprietaireBien($bdd, $id_bien, $id_utilisateur) {
    $req = $bdd->prepare("SELECT u_id FROM biens WHERE id_b = ?");
    $req->execute([$id_bien]);
    $bien = $req->fetch();
    return $bien && $bien['u_id'] == $id_utilisateur;
}

// Traitement des actions
switch ($action) {
    case 'ajouter':
        // Traitement du formulaire d'ajout
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupération des données du formulaire
            $ville = htmlspecialchars($_POST['ville']);
            $cp = htmlspecialchars($_POST['cp']);
            $superficie = floatval($_POST['superficie']);
            $pieces = intval($_POST['pieces']);
            $prix = floatval($_POST['prix']);
            $description = htmlspecialchars($_POST['description']);
            $cat_id = intval($_POST['cat_id']);
            
            // Validation des données
            if (empty($ville) || empty($cp) || $prix <= 0 || $superficie <= 0 || $pieces <= 0) {
                $message = '<div class="alert alert-danger">Veuillez remplir tous les champs obligatoires.</div>';
            } else {
                // Traitement de l'image
                $chemin = null;
                if (isset($_FILES['img']) && $_FILES['img']['error'] == 0) {
                    $path = 'img/';
                    $img = $_FILES['img']['tmp_name'];
                    $name = uniqid();
                    
                    // Récupérer l'extension
                    $info = new SplFileInfo($_FILES['img']['name']);
                    $extension = strtolower($info->getExtension());
                    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                    
                    if (in_array($extension, $allowed)) {
                        $chemin = $path . $name . '.' . $extension;
                        move_uploaded_file($img, $chemin);
                        // Debug
                        var_dump($chemin);
                    }
                }
                
                // Insertion dans la base de données
                $req = $bdd->prepare("INSERT INTO biens (ville, cp, superficie, pieces, prix, description, cat_id, u_id, image, vendu) 
                                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0)");
                $req->execute([$ville, $cp, $superficie, $pieces, $prix, $description, $cat_id, $_SESSION['id_utilisateur'], $chemin]);
                
                $message = '<div class="alert alert-success">Le bien a été ajouté avec succès.</div>';
                header('Location: gestion.php');
                exit();
            }
        }
        break;
        
    case 'modifier':
        // Vérification que l'utilisateur est propriétaire du bien
        if (!estProprietaireBien($bdd, $id_bien, $_SESSION['id_utilisateur']) && !estAdmin()) {
            header('Location: gestion.php');
            exit();
        }
        
        // Traitement du formulaire de modification
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupération des données du formulaire
            $ville = htmlspecialchars($_POST['ville']);
            $cp = htmlspecialchars($_POST['cp']);
            $superficie = floatval($_POST['superficie']);
            $pieces = intval($_POST['pieces']);
            $prix = floatval($_POST['prix']);
            $description = htmlspecialchars($_POST['description']);
            $cat_id = intval($_POST['cat_id']);
            $vendu = isset($_POST['vendu']) ? 1 : 0;
            
            // Validation des données
            if (empty($ville) || empty($cp) || $prix <= 0 || $superficie <= 0 || $pieces <= 0) {
                $message = '<div class="alert alert-danger">Veuillez remplir tous les champs obligatoires.</div>';
            } else {
                // Traitement de l'image si une nouvelle est fournie
                if (isset($_FILES['img']) && $_FILES['img']['error'] == 0) {
                    $path = 'img/';
                    $img = $_FILES['img']['tmp_name'];
                    $name = uniqid();
                    
                    // Récupérer l'extension
                    $info = new SplFileInfo($_FILES['img']['name']);
                    $extension = strtolower($info->getExtension());
                    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                    
                    if (in_array($extension, $allowed)) {
                        $chemin = $path . $name . '.' . $extension;
                        move_uploaded_file($img, $chemin);
                        
                        // Mise à jour de l'image dans la base de données
                        $req = $bdd->prepare("UPDATE biens SET ville = ?, cp = ?, superficie = ?, pieces = ?, prix = ?, description = ?, cat_id = ?, vendu = ?, image = ? WHERE id_b = ?");
                        $req->execute([$ville, $cp, $superficie, $pieces, $prix, $description, $cat_id, $vendu, $chemin, $id_bien]);
                        
                        // Debug
                        var_dump($chemin);
                    }
                } else {
                    // Mise à jour sans changer l'image
                    $req = $bdd->prepare("UPDATE biens SET ville = ?, cp = ?, superficie = ?, pieces = ?, prix = ?, description = ?, cat_id = ?, vendu = ? WHERE id_b = ?");
                    $req->execute([$ville, $cp, $superficie, $pieces, $prix, $description, $cat_id, $vendu, $id_bien]);
                }
                
                $message = '<div class="alert alert-success">Le bien a été modifié avec succès.</div>';
                header('Location: gestion.php');
                exit();
            }
        }
        
        // Récupération des données du bien
        $req = $bdd->prepare("SELECT * FROM biens WHERE id_b = ?");
        $req->execute([$id_bien]);
        $bien = $req->fetch(PDO::FETCH_ASSOC);
        break;
        
    case 'supprimer':
        // Vérification que l'utilisateur est propriétaire du bien
        if (!estProprietaireBien($bdd, $id_bien, $_SESSION['id_utilisateur']) && !estAdmin()) {
            header('Location: gestion.php');
            exit();
        }
        
        // Confirmation de suppression
        if (isset($_GET['confirm']) && $_GET['confirm'] == 1) {
            // Récupération de l'image du bien pour la supprimer
            $req = $bdd->prepare("SELECT image FROM biens WHERE id_b = ?");
            $req->execute([$id_bien]);
            $bien = $req->fetch(PDO::FETCH_ASSOC);
            
            // Suppression de l'image si elle existe
            if (!empty($bien['image']) && file_exists($bien['image'])) {
                unlink($bien['image']);
            }
            
            // Suppression du bien
            $req = $bdd->prepare("DELETE FROM biens WHERE id_b = ?");
            $req->execute([$id_bien]);
            
            $message = '<div class="alert alert-success">Le bien a été supprimé avec succès.</div>';
            header('Location: gestion.php');
            exit();
        }
        break;
        
    case 'images':
        // Vérification que l'utilisateur est propriétaire du bien
        if (!estProprietaireBien($bdd, $id_bien, $_SESSION['id_utilisateur']) && !estAdmin()) {
            header('Location: gestion.php');
            exit();
        }
        
        // Traitement de l'ajout d'image
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['img'])) {
            if ($_FILES['img']['error'] == 0) {
                $path = 'img/';
                $img = $_FILES['img']['tmp_name'];
                $name = uniqid();
                
                // Récupérer l'extension
                $info = new SplFileInfo($_FILES['img']['name']);
                $extension = strtolower($info->getExtension());
                $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                
                if (in_array($extension, $allowed)) {
                    $chemin = $path . $name . '.' . $extension;
                    
                    if (move_uploaded_file($img, $chemin)) {
                        // Mise à jour de l'image dans la base de données
                        $req = $bdd->prepare("UPDATE biens SET image = ? WHERE id_b = ?");
                        $req->execute([$chemin, $id_bien]);
                        
                        // Debug
                        var_dump($chemin);
                        
                        $message = '<div class="alert alert-success">L\'image a été ajoutée avec succès.</div>';
                    }
                }
            }
        }
        
        // Récupération des informations du bien, y compris l'image
        $req = $bdd->prepare("SELECT * FROM biens WHERE id_b = ?");
        $req->execute([$id_bien]);
        $bien = $req->fetch(PDO::FETCH_ASSOC);
        
        // Initialiser le tableau d'images (pour compatibilité avec le code existant)
        $images = [];
        if (!empty($bien['image'])) {
            $images[] = ['id_image' => $bien['id_b'], 'fichier' => $bien['image']];
        }
        break;
        
    case 'supprimer_image':
        // Vérification que l'utilisateur est propriétaire du bien
        if (!estProprietaireBien($bdd, $id_bien, $_SESSION['id_utilisateur']) && !estAdmin()) {
            header('Location: gestion.php');
            exit();
        }
        
        // Récupération de l'image du bien
        $req = $bdd->prepare("SELECT image FROM biens WHERE id_b = ?");
        $req->execute([$id_bien]);
        $bien = $req->fetch(PDO::FETCH_ASSOC);
        
        if ($bien && !empty($bien['image'])) {
            // Suppression du fichier image si existant
            if (file_exists($bien['image'])) {
                unlink($bien['image']);
            }
            
            // Mise à jour de la base de données pour supprimer la référence à l'image
            $req = $bdd->prepare("UPDATE biens SET image = NULL WHERE id_b = ?");
            $req->execute([$id_bien]);
            
            $message = '<div class="alert alert-success">L\'image a été supprimée avec succès.</div>';
        }
        
        header('Location: gestion.php?action=images&id=' . $id_bien);
        exit();
        break;
        
    default:
        // Liste des biens de l'utilisateur
        $req = $bdd->prepare("SELECT * FROM biens WHERE u_id = ? ORDER BY id_b DESC");
        $req->execute([$_SESSION['id_utilisateur']]);
        $biens = $req->fetchAll(PDO::FETCH_ASSOC);
        break;
}
?>

<div class="container">
    <?php if (!empty($message)): ?>
        <?= $message ?>
    <?php endif; ?>
    
    <?php if ($action === 'liste'): ?>
    <!-- Liste des biens -->
    <div class="admin-container">
        <div class="admin-header d-flex justify-content-between align-items-center">
            <h2><i class="bi bi-house-gear"></i> Gestion des biens</h2>
            <a href="gestion.php?action=ajouter" class="admin-btn btn"><i class="bi bi-plus-circle"></i> Ajouter un bien</a>
        </div>
        
        <?php if (empty($biens)): ?>
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> Vous n'avez pas encore de biens. 
            <a href="gestion.php?action=ajouter" class="alert-link">Ajouter un bien</a>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table admin-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Ville</th>
                        <th>Prix</th>
                        <th>Superficie</th>
                        <th>Pièces</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($biens as $bien): ?>
                    <tr>
                        <td>
                            <?php if (!empty($bien['image'])): ?>
                            <img src="<?= $bien['image'] ?>" alt="Bien à <?= htmlspecialchars($bien['ville']) ?>" width="50" height="50" style="object-fit: cover;">
                            <?php else: ?>
                            <div class="bg-light text-center" style="width: 50px; height: 50px; line-height: 50px;">
                                <i class="bi bi-house"></i>
                            </div>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($bien['ville']) ?></td>
                        <td><?= number_format($bien['prix'], 0, ',', ' ') ?> €</td>
                        <td><?= $bien['superficie'] ?> m²</td>
                        <td><?= $bien['pieces'] ?></td>
                        <td>
                            <?php if (isset($bien['vendu']) && $bien['vendu'] == 1): ?>
                            <span class="badge bg-danger">Vendu</span>
                            <?php else: ?>
                            <span class="badge bg-success">Disponible</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="btn-group">
                                <a href="gestion.php?action=modifier&id=<?= $bien['id_b'] ?>" class="btn btn-sm admin-btn-secondary" title="Modifier">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="gestion.php?action=images&id=<?= $bien['id_b'] ?>" class="btn btn-sm admin-btn" title="Gérer les images">
                                    <i class="bi bi-images"></i>
                                </a>
                                <a href="gestion.php?action=supprimer&id=<?= $bien['id_b'] ?>" class="btn btn-sm btn-danger" title="Supprimer" 
                                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce bien ?');">
                                    <i class="bi bi-trash"></i>
                                </a>
                                <a href="bien.php?id=<?= $bien['id_b'] ?>" class="btn btn-sm btn-info" title="Voir" target="_blank">
                                    <i class="bi bi-eye"></i>
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
    
    <?php elseif ($action === 'ajouter' || $action === 'modifier'): ?>
    <!-- Formulaire d'ajout/modification -->
    <div class="admin-container">
        <div class="admin-header">
            <h2>
                <?php if ($action === 'ajouter'): ?>
                <i class="bi bi-plus-circle"></i> Ajouter un bien
                <?php else: ?>
                <i class="bi bi-pencil"></i> Modifier un bien
                <?php endif; ?>
            </h2>
        </div>
        
        <form method="post" enctype="multipart/form-data" class="admin-form">
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="ville" class="form-label"><i class="bi bi-building"></i> Ville</label>
                        <input type="text" class="form-control" id="ville" name="ville" required
                               value="<?= isset($bien['ville']) ? htmlspecialchars($bien['ville']) : '' ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="cp" class="form-label"><i class="bi bi-geo"></i> Code postal</label>
                        <input type="text" class="form-control" id="cp" name="cp" required
                               value="<?= isset($bien['cp']) ? htmlspecialchars($bien['cp']) : '' ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label"><i class="bi bi-file-text"></i> Description</label>
                        <textarea class="form-control" id="description" name="description" rows="5" required><?= isset($bien['description']) ? htmlspecialchars($bien['description']) : '' ?></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="prix" class="form-label"><i class="bi bi-currency-euro"></i> Prix</label>
                                <input type="number" class="form-control" id="prix" name="prix" min="0" step="1000" required
                                       value="<?= isset($bien['prix']) ? $bien['prix'] : '' ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="superficie" class="form-label"><i class="bi bi-rulers"></i> Superficie (m²)</label>
                                <input type="number" class="form-control" id="superficie" name="superficie" min="0" step="0.01" required
                                       value="<?= isset($bien['superficie']) ? $bien['superficie'] : '' ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="pieces" class="form-label"><i class="bi bi-door-open"></i> Nombre de pièces</label>
                                <input type="number" class="form-control" id="pieces" name="pieces" min="0" required
                                       value="<?= isset($bien['pieces']) ? $bien['pieces'] : '' ?>">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="cat_id" class="form-label"><i class="bi bi-house-door"></i> Catégorie</label>
                        <select class="form-control" id="cat_id" name="cat_id" required>
                            <option value="">Sélectionnez une catégorie</option>
                            <option value="1" <?= (isset($bien['cat_id']) && $bien['cat_id'] == 1) ? 'selected' : '' ?>>Appartement</option>
                            <option value="2" <?= (isset($bien['cat_id']) && $bien['cat_id'] == 2) ? 'selected' : '' ?>>Maison</option>
                            <option value="3" <?= (isset($bien['cat_id']) && $bien['cat_id'] == 3) ? 'selected' : '' ?>>Terrain</option>
                            <option value="4" <?= (isset($bien['cat_id']) && $bien['cat_id'] == 4) ? 'selected' : '' ?>>Commerce</option>
                        </select>
                    </div>
                    
                    <?php if ($action === 'modifier'): ?>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="vendu" name="vendu" <?= (isset($bien['vendu']) && $bien['vendu'] == 1) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="vendu">
                                <i class="bi bi-tag"></i> Marquer comme vendu
                            </label>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label for="img" class="form-label"><i class="bi bi-image"></i> Image du bien</label>
                        <?php if (isset($bien['image']) && !empty($bien['image'])): ?>
                        <div class="mb-2">
                            <img src="<?= $bien['image'] ?>" alt="Image du bien" class="img-fluid image-preview">
                        </div>
                        <?php endif; ?>
                        <input type="file" class="form-control" id="img" name="img" accept="image/*">
                        <small class="form-text text-muted">Format recommandé : JPG, PNG ou GIF</small>
                    </div>
                </div>
            </div>
            
            <div class="mt-4">
                <button type="submit" class="admin-btn btn">
                    <?php if ($action === 'ajouter'): ?>
                    <i class="bi bi-plus-circle"></i> Ajouter le bien
                    <?php else: ?>
                    <i class="bi bi-check-circle"></i> Enregistrer les modifications
                    <?php endif; ?>
                </button>
                <a href="gestion.php" class="admin-btn-secondary btn ms-2"><i class="bi bi-arrow-left"></i> Retour à la liste</a>
            </div>
        </form>
    </div>
    
    <?php elseif ($action === 'images'): ?>
    <!-- Gestion des images -->
    <div class="admin-container">
        <div class="admin-header d-flex justify-content-between align-items-center">
            <h2><i class="bi bi-images"></i> Gestion des images</h2>
            <a href="gestion.php" class="admin-btn-secondary btn"><i class="bi bi-arrow-left"></i> Retour à la liste</a>
        </div>
        
        <h4 class="mb-4"><?= htmlspecialchars($bien['titre']) ?></h4>
        
        <!-- Formulaire d'ajout d'images -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-upload"></i> Ajouter des images</h5>
            </div>
            <div class="card-body">
                <form method="post" enctype="multipart/form-data" class="admin-form">
                    <div class="mb-3">
                        <label for="images" class="form-label">Sélectionner des images</label>
                        <input type="file" class="form-control" id="images" name="images[]" accept="image/*" multiple required>
                        <small class="form-text text-muted">Vous pouvez sélectionner plusieurs images (JPG, PNG ou GIF)</small>
                    </div>
                    <button type="submit" class="admin-btn btn"><i class="bi bi-upload"></i> Télécharger les images</button>
                </form>
            </div>
        </div>
        
        <!-- Liste des images -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-images"></i> Images du bien</h5>
            </div>
            <div class="card-body">
                <?php if (empty($images)): ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Aucune image supplémentaire n'a été ajoutée pour ce bien.
                </div>
                <?php else: ?>
                <div class="row">
                    <?php foreach ($images as $image): ?>
                    <div class="col-md-3 mb-4">
                        <div class="card admin-card h-100">
                            <img src="uploads/biens/<?= $image['fichier'] ?>" class="card-img-top" alt="Image" style="height: 150px; object-fit: cover;">
                            <div class="card-body text-center">
                                <a href="gestion.php?action=supprimer_image&id_image=<?= $image['id_image'] ?>" class="btn btn-danger btn-sm" 
                                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette image ?');">
                                    <i class="bi bi-trash"></i> Supprimer
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php
// Inclusion du pied de page
require_once 'includes/footer.php';
?>
