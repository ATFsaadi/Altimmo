<?php

class ClientControleur extends Controleur
{
    public function accueil()
    {
        $resultat = $this->bienModele->rechercher($_GET, 1, 6);
        $categories = $this->categorieModele->toutes();

        $this->render('client/accueil', [
            'biens' => $resultat['biens'],
            'categories' => $categories,
            'filtres' => $_GET,
        ], 'Accueil - Altimmo');
    }

    public function biens()
    {
        $page = max(1, (int) ($_GET['pagination'] ?? 1));
        $resultat = $this->bienModele->rechercher($_GET, $page, 9);

        $this->render('client/biens', [
            'biens' => $resultat['biens'],
            'total' => $resultat['total'],
            'totalPages' => $resultat['total_pages'],
            'pageCourante' => $page,
            'categories' => $this->categorieModele->toutes(),
            'filtres' => $_GET,
        ], 'Nos biens - Altimmo');
    }

    public function bien()
    {
        $id = (int) ($_GET['id'] ?? 0);
        $bien = $this->bienModele->trouver($id);

        if (!$bien) {
            flash("Le bien demande n'existe pas.", 'warning');
            redirectTo('biens');
        }

        $erreurs = [];
        $messageEnvoye = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = $this->nettoyer($_POST['nom'] ?? '');
            $email = $this->nettoyer($_POST['email'] ?? '');
            $message = $this->nettoyer($_POST['message'] ?? '');

            if ($nom === '') {
                $erreurs[] = 'Le nom est obligatoire.';
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $erreurs[] = "L'email est invalide.";
            }

            if ($message === '') {
                $erreurs[] = 'Le message est obligatoire.';
            }

            if (empty($erreurs)) {
                $agent = $bien['u_id'] ? $this->utilisateurModele->trouverParId($bien['u_id']) : null;

                if (!$agent || (int) $agent['role'] < 2) {
                    $agent = $this->utilisateurModele->premierAgentDisponible();
                }

                if (!$agent) {
                    $erreurs[] = "Aucun agent n'est disponible pour recevoir votre demande.";
                } else {
                    $expediteurId = estConnecte()
                        ? utilisateurConnecte()['id_u']
                        : $this->utilisateurModele->trouverOuCreerContact($nom, $email);

                    $texte = "Demande pour le bien #" . $bien['id_b'] . " a " . $bien['ville'] . "\n";
                    $texte .= "Nom : " . $nom . "\n";
                    $texte .= "Email : " . $email . "\n\n";
                    $texte .= $message;

                    $this->messageModele->envoyer($expediteurId, $agent['id_u'], $texte);
                    $messageEnvoye = true;
                }
            }
        }

        $this->render('client/bien', [
            'bien' => $bien,
            'erreurs' => $erreurs,
            'messageEnvoye' => $messageEnvoye,
        ], 'Detail du bien - Altimmo');
    }

    public function contact()
    {
        $erreurs = [];
        $success = false;
        $form = [
            'nom' => '',
            'email' => '',
            'sujet' => '',
            'message' => '',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            foreach ($form as $champ => $valeur) {
                $form[$champ] = $this->nettoyer($_POST[$champ] ?? '');
            }

            if ($form['nom'] === '') {
                $erreurs[] = 'Le nom est obligatoire.';
            }

            if (!filter_var($form['email'], FILTER_VALIDATE_EMAIL)) {
                $erreurs[] = "L'email est invalide.";
            }

            if ($form['sujet'] === '') {
                $erreurs[] = 'Le sujet est obligatoire.';
            }

            if ($form['message'] === '') {
                $erreurs[] = 'Le message est obligatoire.';
            }

            if (empty($erreurs)) {
                $agent = $this->utilisateurModele->premierAgentDisponible();

                if (!$agent) {
                    $erreurs[] = "Aucun agent ou administrateur n'est disponible.";
                } else {
                    $expediteurId = estConnecte()
                        ? utilisateurConnecte()['id_u']
                        : $this->utilisateurModele->trouverOuCreerContact($form['nom'], $form['email']);

                    $texte = "Message de contact\n";
                    $texte .= "Nom : " . $form['nom'] . "\n";
                    $texte .= "Email : " . $form['email'] . "\n";
                    $texte .= "Sujet : " . $form['sujet'] . "\n\n";
                    $texte .= $form['message'];

                    $this->messageModele->envoyer($expediteurId, $agent['id_u'], $texte);
                    $success = true;
                    $form = ['nom' => '', 'email' => '', 'sujet' => '', 'message' => ''];
                }
            }
        }

        $this->render('client/contact', [
            'erreurs' => $erreurs,
            'success' => $success,
            'form' => $form,
        ], 'Contact - Altimmo');
    }

    public function login()
    {
        if (estConnecte()) {
            redirectTo('accueil');
        }

        $erreur = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $this->nettoyer($_POST['email'] ?? '');
            $motDePasse = $_POST['mot_de_passe'] ?? '';
            $utilisateur = $this->utilisateurModele->trouverParEmail($email);
            $hash = $utilisateur['password'] ?? ($utilisateur['mot_de_passe'] ?? '');

            if ($utilisateur && $hash && (password_verify($motDePasse, $hash) || $hash === sha1($motDePasse))) {
                if ($hash === sha1($motDePasse)) {
                    $this->utilisateurModele->changerMotDePasse($utilisateur['id_u'], password_hash($motDePasse, PASSWORD_DEFAULT));
                }

                session_regenerate_id(true);
                $_SESSION['utilisateur'] = [
                    'id_u' => $utilisateur['id_u'],
                    'nom' => $utilisateur['nom'],
                    'prenom' => $utilisateur['prenom'],
                    'email' => $utilisateur['email'],
                    'role' => (int) $utilisateur['role'],
                ];

                $redirect = $_GET['redirect'] ?? 'accueil';
                if (!preg_match('/^[a-z_]+$/', $redirect)) {
                    $redirect = 'accueil';
                }

                redirectTo($redirect);
            }

            $erreur = 'Email ou mot de passe incorrect.';
        }

        $this->render('client/login', ['erreur' => $erreur], 'Connexion - Altimmo');
    }

    public function register()
    {
        if (estConnecte()) {
            redirectTo('accueil');
        }

        $erreurs = [];
        $form = [
            'nom' => '',
            'prenom' => '',
            'email' => '',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            foreach ($form as $champ => $valeur) {
                $form[$champ] = $this->nettoyer($_POST[$champ] ?? '');
            }

            $motDePasse = $_POST['mot_de_passe'] ?? '';
            $confirmation = $_POST['confirm_mot_de_passe'] ?? '';

            if (strlen($form['nom']) < 2) {
                $erreurs[] = 'Le nom doit contenir au moins 2 caracteres.';
            }

            if (strlen($form['prenom']) < 2) {
                $erreurs[] = 'Le prenom doit contenir au moins 2 caracteres.';
            }

            if (!filter_var($form['email'], FILTER_VALIDATE_EMAIL)) {
                $erreurs[] = "L'email est invalide.";
            } elseif ($this->utilisateurModele->emailExiste($form['email'])) {
                $erreurs[] = 'Cette adresse email est deja utilisee.';
            }

            if (strlen($motDePasse) < 6) {
                $erreurs[] = 'Le mot de passe doit contenir au moins 6 caracteres.';
            }

            if ($motDePasse !== $confirmation) {
                $erreurs[] = 'Les mots de passe ne correspondent pas.';
            }

            if (empty($erreurs)) {
                $this->utilisateurModele->creer(
                    $form['nom'],
                    $form['prenom'],
                    $form['email'],
                    $motDePasse,
                    $_SERVER['REMOTE_ADDR'] ?? null
                );

                flash('Votre compte a ete cree. Vous pouvez maintenant vous connecter.', 'success');
                redirectTo('login');
            }
        }

        $this->render('client/register', [
            'erreurs' => $erreurs,
            'form' => $form,
        ], 'Inscription - Altimmo');
    }

    public function logout()
    {
        $_SESSION = [];
        session_destroy();
        redirectTo('accueil');
    }

    public function profile()
    {
        $this->requireLogin();

        $utilisateurSession = utilisateurConnecte();
        $utilisateur = $this->utilisateurModele->trouverParId($utilisateurSession['id_u']);
        $erreurs = [];

        if (!$utilisateur) {
            $this->logout();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['changer_mdp'])) {
                $ancien = $_POST['ancien_mdp'] ?? '';
                $nouveau = $_POST['nouveau_mdp'] ?? '';
                $confirmation = $_POST['confirmer_mdp'] ?? '';

                $hashActuel = $utilisateur['password'] ?? ($utilisateur['mot_de_passe'] ?? '');

                if (!$hashActuel || !password_verify($ancien, $hashActuel)) {
                    $erreurs[] = "L'ancien mot de passe est incorrect.";
                }

                if (strlen($nouveau) < 6) {
                    $erreurs[] = 'Le nouveau mot de passe doit contenir au moins 6 caracteres.';
                }

                if ($nouveau !== $confirmation) {
                    $erreurs[] = 'Les nouveaux mots de passe ne correspondent pas.';
                }

                if (empty($erreurs)) {
                    $this->utilisateurModele->changerMotDePasse($utilisateur['id_u'], password_hash($nouveau, PASSWORD_DEFAULT));
                    flash('Votre mot de passe a ete modifie.', 'success');
                    redirectTo('profile');
                }
            } else {
                $nom = $this->nettoyer($_POST['nom'] ?? '');
                $prenom = $this->nettoyer($_POST['prenom'] ?? '');
                $email = $this->nettoyer($_POST['email'] ?? '');

                if ($nom === '' || $prenom === '') {
                    $erreurs[] = 'Le nom et le prenom sont obligatoires.';
                }

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $erreurs[] = "L'email est invalide.";
                } elseif ($this->utilisateurModele->emailExiste($email, $utilisateur['id_u'])) {
                    $erreurs[] = 'Cette adresse email est deja utilisee.';
                }

                if (empty($erreurs)) {
                    $this->utilisateurModele->mettreAJourProfil($utilisateur['id_u'], $nom, $prenom, $email);
                    $_SESSION['utilisateur']['nom'] = $nom;
                    $_SESSION['utilisateur']['prenom'] = $prenom;
                    $_SESSION['utilisateur']['email'] = $email;

                    flash('Votre profil a ete mis a jour.', 'success');
                    redirectTo('profile');
                }
            }
        }

        $this->render('client/profile', [
            'utilisateur' => $utilisateur,
            'erreurs' => $erreurs,
        ], 'Mon profil - Altimmo');
    }

    public function messages()
    {
        $this->requireLogin();

        $utilisateur = utilisateurConnecte();
        $erreurs = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $destinataire = (int) ($_POST['destinataire'] ?? 0);
            $message = $this->nettoyer($_POST['message'] ?? '');

            if ($destinataire <= 0 || $destinataire === (int) $utilisateur['id_u']) {
                $erreurs[] = 'Le destinataire est invalide.';
            }

            if ($message === '') {
                $erreurs[] = 'Le message ne peut pas etre vide.';
            }

            if (empty($erreurs)) {
                $this->messageModele->envoyer($utilisateur['id_u'], $destinataire, $message);
                flash('Message envoye avec succes.', 'success');
                redirectTo('messages');
            }
        }

        $messagesRecus = $this->messageModele->recus($utilisateur['id_u']);
        $messagesEnvoyes = $this->messageModele->envoyes($utilisateur['id_u']);
        $this->messageModele->marquerCommeLus($utilisateur['id_u']);

        $this->render('client/messages', [
            'erreurs' => $erreurs,
            'messagesRecus' => $messagesRecus,
            'messagesEnvoyes' => $messagesEnvoyes,
            'destinataires' => $this->utilisateurModele->destinataires($utilisateur['id_u']),
        ], 'Messages - Altimmo');
    }

    public function pageTexte($type)
    {
        $this->render('client/static', ['type' => $type], ucfirst($type) . ' - Altimmo');
    }

    public function erreur404()
    {
        $this->render('client/erreur', [
            'titreErreur' => 'Page introuvable',
            'messageErreur' => "La page demandee n'existe pas.",
        ], 'Erreur 404 - Altimmo');
    }

    public function erreur500()
    {
        $this->render('client/erreur', [
            'titreErreur' => 'Erreur serveur',
            'messageErreur' => 'Une erreur est survenue.',
        ], 'Erreur - Altimmo');
    }
}
