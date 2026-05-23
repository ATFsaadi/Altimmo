<?php

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
