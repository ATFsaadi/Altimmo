<div class="container py-5">
    <div class="page-panel p-4 mb-4">
        <h1 class="section-title h2">Nos biens immobiliers</h1>
        <p class="text-muted mb-0">Utilisez les filtres pour retrouver rapidement les annonces disponibles.</p>
    </div>

    <div class="search-panel p-4 mb-5">
        <form class="row g-3" method="get" action="index.php">
            <input type="hidden" name="page" value="biens">
            <div class="col-md-4">
                <label class="form-label" for="ville_cp">Ville ou code postal</label>
                <input class="form-control" id="ville_cp" name="ville_cp" value="<?= e($filtres['ville_cp'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label" for="type">Type de bien</label>
                <select class="form-select" id="type" name="type">
                    <option value="tous">Tous</option>
                    <?php foreach ($categories as $categorie): ?>
                        <option value="<?= e($categorie['nom_cat']) ?>" <?= (($filtres['type'] ?? '') === $categorie['nom_cat']) ? 'selected' : '' ?>>
                            <?= e($categorie['nom_cat']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label" for="nb_pieces">Pieces</label>
                <select class="form-select" id="nb_pieces" name="nb_pieces">
                    <option value="tous">Toutes</option>
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <option value="<?= $i ?>" <?= (($filtres['nb_pieces'] ?? '') == $i) ? 'selected' : '' ?>><?= $i ?><?= $i === 5 ? ' +' : '' ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label" for="prix_max">Prix maximum</label>
                <input type="number" class="form-control" id="prix_max" name="prix_max" min="0" value="<?= e($filtres['prix_max'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label" for="prix_min">Prix minimum</label>
                <input type="number" class="form-control" id="prix_min" name="prix_min" min="0" value="<?= e($filtres['prix_min'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label" for="surface_min">Surface minimum</label>
                <input type="number" class="form-control" id="surface_min" name="surface_min" min="0" value="<?= e($filtres['surface_min'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label" for="surface_max">Surface maximum</label>
                <input type="number" class="form-control" id="surface_max" name="surface_max" min="0" value="<?= e($filtres['surface_max'] ?? '') ?>">
            </div>
            <div class="col-md-3 d-flex align-items-end gap-2">
                <button class="btn btn-primary flex-fill" type="submit"><i class="bi bi-search"></i>Rechercher</button>
                <a class="btn btn-outline-secondary" href="<?= url('biens') ?>">Reset</a>
            </div>
        </form>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h4 mb-0"><?= (int) $total ?> bien(s) trouve(s)</h2>
        <?php if (estAgent()): ?>
            <a class="btn btn-primary" href="<?= url('gestion', ['action' => 'ajouter']) ?>"><i class="bi bi-plus-circle"></i>Ajouter</a>
        <?php endif; ?>
    </div>

    <div class="row g-4">
        <?php foreach ($biens as $bien): ?>
            <?php require __DIR__ . '/partials/carte_bien.php'; ?>
        <?php endforeach; ?>

        <?php if (empty($biens)): ?>
            <div class="col-12">
                <div class="alert alert-info">Aucun bien ne correspond a votre recherche.</div>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($totalPages > 1): ?>
        <?php $paramsPagination = $filtres; unset($paramsPagination['page'], $paramsPagination['pagination']); ?>
        <nav class="mt-5">
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i === $pageCourante ? 'active' : '' ?>">
                        <a class="page-link" href="<?= url('biens', array_merge($paramsPagination, ['pagination' => $i])) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>

