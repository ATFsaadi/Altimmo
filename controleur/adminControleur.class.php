<?php

class AdminControleur extends Controleur
{
    public function dashboard()
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifier_role'])) {
            $id = (int) ($_POST['id_u'] ?? 0);
            $role = (int) ($_POST['role'] ?? 1);

            if ($id === (int) utilisateurConnecte()['id_u']) {
                flash('Vous ne pouvez pas modifier votre propre role.', 'warning');
            } elseif (!in_array($role, [1, 2, 3], true)) {
                flash('Role invalide.', 'danger');
            } else {
                $this->utilisateurModele->changerRole($id, $role);
                flash('Role utilisateur mis a jour.', 'success');
            }

            redirectTo('admin', ['tab' => 'utilisateurs']);
        }

        $action = $_GET['action'] ?? '';
        $id = (int) ($_GET['id'] ?? 0);

        if ($action === 'supprimer_user' && $id > 0) {
            if ($id === (int) utilisateurConnecte()['id_u']) {
                flash('Vous ne pouvez pas supprimer votre propre compte.', 'warning');
            } else {
                $this->utilisateurModele->supprimer($id);
                flash('Utilisateur supprime.', 'success');
            }

            redirectTo('admin', ['tab' => 'utilisateurs']);
        }

        if ($action === 'supprimer_bien' && $id > 0) {
            $bien = $this->bienModele->trouver($id);
            if ($bien) {
                $this->supprimerImageSiLocale($bien['image']);
                $this->bienModele->supprimer($id);
                flash('Bien supprime.', 'success');
            }

            redirectTo('admin', ['tab' => 'biens']);
        }

        $this->render('admin/dashboard', [
            'stats' => [
                'utilisateurs' => $this->utilisateurModele->compter(),
                'biens' => $this->bienModele->compter(),
                'messages' => $this->messageModele->compter(),
                'agents' => $this->utilisateurModele->compterAgents(),
            ],
            'utilisateurs' => $this->utilisateurModele->tous(),
            'biens' => $this->bienModele->tousPourAdmin(),
            'messages' => $this->messageModele->recents(),
            'tabActive' => $_GET['tab'] ?? 'utilisateurs',
        ], 'Administration - Altimmo');
    }

    public function gestionBiens()
    {
        $this->requireAgent();

        $action = $_GET['action'] ?? 'liste';
        $id = (int) ($_GET['id'] ?? 0);
        $utilisateur = utilisateurConnecte();
        $erreurs = [];
        $bien = null;

        if ($action === 'supprimer' && $id > 0) {
            $this->verifierDroitBien($id);
            $bien = $this->bienModele->trouver($id);

            if ($bien) {
                $this->supprimerImageSiLocale($bien['image']);
                $this->bienModele->supprimer($id);
                flash('Le bien a ete supprime.', 'success');
            }

            redirectTo('gestion');
        }

        if ($action === 'modifier') {
            $this->verifierDroitBien($id);
            $bien = $this->bienModele->trouver($id);

            if (!$bien) {
                flash("Le bien demande n'existe pas.", 'warning');
                redirectTo('gestion');
            }
        }

        if (($action === 'ajouter' || $action === 'modifier') && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $imageActuelle = $bien['image'] ?? null;
            $image = $this->uploadImage('image');

            if (!$image) {
                $image = $imageActuelle;
            }

            $data = [
                'ville' => $this->nettoyer($_POST['ville'] ?? ''),
                'cp' => $this->nettoyer($_POST['cp'] ?? ''),
                'prix' => (float) ($_POST['prix'] ?? 0),
                'superficie' => (float) ($_POST['superficie'] ?? 0),
                'pieces' => (int) ($_POST['pieces'] ?? 0),
                'description' => $this->nettoyer($_POST['description'] ?? ''),
                'cat_id' => (int) ($_POST['cat_id'] ?? 0),
                'image' => $image,
                'u_id' => $utilisateur['id_u'],
                'vendu' => isset($_POST['vendu']) ? 1 : 0,
            ];

            $erreurs = $this->validerBien($data);

            if (empty($erreurs)) {
                if ($action === 'ajouter') {
                    $this->bienModele->creer($data);
                    flash('Le bien a ete ajoute.', 'success');
                } else {
                    if ($image && $image !== $imageActuelle) {
                        $this->supprimerImageSiLocale($imageActuelle);
                    }
                    $this->bienModele->mettreAJour($id, $data);
                    flash('Le bien a ete modifie.', 'success');
                }

                redirectTo('gestion');
            }

            $bien = array_merge($bien ?: [], $data);
        }

        $biens = estAdmin()
            ? $this->bienModele->tousPourAdmin()
            : $this->bienModele->parUtilisateur($utilisateur['id_u']);

        $this->render('admin/gestion_biens', [
            'action' => $action,
            'bien' => $bien,
            'biens' => $biens,
            'categories' => $this->categorieModele->toutes(),
            'erreurs' => $erreurs,
        ], 'Gestion des biens - Altimmo');
    }

    private function verifierDroitBien($id)
    {
        if ($id <= 0) {
            redirectTo('gestion');
        }

        if (!estAdmin() && !$this->bienModele->appartientA($id, utilisateurConnecte()['id_u'])) {
            flash("Vous ne pouvez pas modifier ce bien.", 'danger');
            redirectTo('gestion');
        }
    }

    private function validerBien($data)
    {
        $erreurs = [];

        if ($data['ville'] === '') {
            $erreurs[] = 'La ville est obligatoire.';
        }

        if ($data['cp'] === '') {
            $erreurs[] = 'Le code postal est obligatoire.';
        }

        if ($data['prix'] <= 0) {
            $erreurs[] = 'Le prix doit etre superieur a 0.';
        }

        if ($data['superficie'] <= 0) {
            $erreurs[] = 'La superficie doit etre superieure a 0.';
        }

        if ($data['pieces'] <= 0) {
            $erreurs[] = 'Le nombre de pieces doit etre superieur a 0.';
        }

        if ($data['description'] === '') {
            $erreurs[] = 'La description est obligatoire.';
        }

        if ($data['cat_id'] <= 0) {
            $erreurs[] = 'Le type de bien est obligatoire.';
        }

        return $erreurs;
    }

    private function uploadImage($champ)
    {
        if (!isset($_FILES[$champ]) || $_FILES[$champ]['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $extension = strtolower(pathinfo($_FILES[$champ]['name'], PATHINFO_EXTENSION));
        $extensionsAutorisees = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

        if (!in_array($extension, $extensionsAutorisees, true)) {
            flash('Format image non autorise. Utilisez JPG, PNG, WEBP ou GIF.', 'danger');
            return null;
        }

        $dossier = __DIR__ . '/../assets/img/biens';
        if (!is_dir($dossier)) {
            mkdir($dossier, 0775, true);
        }

        $nom = 'bien_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
        $destination = $dossier . '/' . $nom;

        if (!move_uploaded_file($_FILES[$champ]['tmp_name'], $destination)) {
            flash("L'image n'a pas pu etre enregistree.", 'danger');
            return null;
        }

        return 'assets/img/biens/' . $nom;
    }

    private function supprimerImageSiLocale($chemin)
    {
        if (!$chemin || strpos($chemin, 'assets/img/biens/') !== 0) {
            return;
        }

        $fichier = __DIR__ . '/../' . $chemin;
        if (is_file($fichier)) {
            unlink($fichier);
        }
    }
}

