<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($titrePage) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= asset('css/style.css') ?>" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= url('accueil') ?>">
            <img src="<?= asset('img/logo.png') ?>" alt="Altimmo" width="34" height="34" class="me-2">
            Altimmo
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="<?= url('accueil') ?>"><i class="bi bi-house"></i>Accueil</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= url('biens') ?>"><i class="bi bi-buildings"></i>Biens</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= url('contact') ?>"><i class="bi bi-envelope"></i>Contact</a></li>
                <?php if (estAgent()): ?>
                    <li class="nav-item"><a class="nav-link" href="<?= url('gestion') ?>"><i class="bi bi-house-gear"></i>Gestion</a></li>
                <?php endif; ?>
                <?php if (estAdmin()): ?>
                    <li class="nav-item"><a class="nav-link" href="<?= url('admin') ?>"><i class="bi bi-shield-lock"></i>Admin</a></li>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav mb-2 mb-lg-0">
                <?php if (estConnecte()): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= url('messages') ?>">
                            <i class="bi bi-chat-dots"></i>Messages
                            <?php if ($nbMessagesNonLus > 0): ?>
                                <span class="badge text-bg-danger"><?= $nbMessagesNonLus ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="<?= url('profile') ?>"><i class="bi bi-person-circle"></i><?= e(utilisateurConnecte()['prenom'] ?: utilisateurConnecte()['nom']) ?></a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= url('logout') ?>"><i class="bi bi-box-arrow-right"></i>Deconnexion</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="<?= url('login') ?>"><i class="bi bi-box-arrow-in-right"></i>Connexion</a></li>
                    <li class="nav-item"><a class="btn btn-primary btn-sm ms-lg-2" href="<?= url('register') ?>">Inscription</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<?php if (estConnecte()): ?>
    <div class="user-strip">
        <div class="container d-flex flex-wrap justify-content-between gap-2">
            <span><?= e(utilisateurConnecte()['prenom'] . ' ' . utilisateurConnecte()['nom']) ?></span>
            <span class="badge rounded-pill text-bg-light"><?= e(roleLibelle(utilisateurConnecte()['role'])) ?></span>
        </div>
    </div>
<?php endif; ?>

<?php $messageFlash = flash(); ?>
<?php if ($messageFlash): ?>
    <div class="container mt-3">
        <div class="alert alert-<?= e($messageFlash['type']) ?> alert-dismissible fade show" role="alert">
            <?= e($messageFlash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
<?php endif; ?>

<main>

