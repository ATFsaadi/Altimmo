<section class="hero">
    <div class="container">
        <div class="col-lg-8">
            <h1 class="display-5 mb-3">Trouvez un bien immobilier clair, fiable et adapte a votre projet.</h1>
            <p class="lead mb-4">Altimmo accompagne clients et agents avec une gestion simple des annonces, contacts et messages.</p>
            <a href="<?= url('biens') ?>" class="btn btn-primary btn-lg">Voir les biens</a>
        </div>
    </div>
</section>

<div class="container">
    <div class="search-panel p-4 mb-5">
        <form class="row g-3 align-items-end" method="get" action="index.php">
            <input type="hidden" name="page" value="biens">
            <div class="col-lg-4">
                <label class="form-label" for="ville_cp">Ville ou code postal</label>
                <input class="form-control" id="ville_cp" name="ville_cp" value="<?= e($filtres['ville_cp'] ?? '') ?>">
            </div>
            <div class="col-lg-3">
                <label class="form-label" for="type">Type</label>
                <select class="form-select" id="type" name="type">
                    <option value="tous">Tous</option>
                    <?php foreach ($categories as $categorie): ?>
                        <option value="<?= e($categorie['nom_cat']) ?>"><?= e($categorie['nom_cat']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-lg-3">
                <label class="form-label" for="prix_max">Budget maximum</label>
                <input type="number" class="form-control" id="prix_max" name="prix_max" min="0">
            </div>
            <div class="col-lg-2 d-grid">
                <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i>Rechercher</button>
            </div>
        </form>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="section-title h3 mb-0">Derniers biens</h2>
        <a href="<?= url('biens') ?>" class="btn btn-outline-primary">Tout voir</a>
    </div>

    <div class="row g-4">
        <?php foreach ($biens as $bien): ?>
            <?php require __DIR__ . '/partials/carte_bien.php'; ?>
        <?php endforeach; ?>

        <?php if (empty($biens)): ?>
            <div class="col-12">
                <div class="alert alert-info">Aucun bien n'est disponible pour le moment.</div>
            </div>
        <?php endif; ?>
    </div>
</div>

