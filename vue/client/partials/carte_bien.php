<div class="col-md-6 col-lg-4">
    <article class="property-card">
        <img src="<?= e(imageBien($bien)) ?>" alt="Bien a <?= e($bien['ville'] ?? 'consulter') ?>">
        <div class="p-3">
            <div class="d-flex justify-content-between align-items-start gap-2">
                <h3 class="h5 mb-1"><?= e($bien['ville'] ?? 'Bien immobilier') ?></h3>
                <span class="badge <?= !empty($bien['vendu']) ? 'text-bg-secondary' : 'text-bg-success' ?>">
                    <?= !empty($bien['vendu']) ? 'Vendu' : 'Disponible' ?>
                </span>
            </div>
            <div class="price mb-2"><?= formatPrix($bien['prix'] ?? 0) ?></div>
            <p class="meta mb-2">
                <i class="bi bi-geo-alt"></i><?= e($bien['cp'] ?? '') ?> <?= e($bien['ville'] ?? '') ?>
            </p>
            <p class="mb-3">
                <span class="me-3"><i class="bi bi-rulers"></i><?= e($bien['superficie'] ?? 0) ?> m2</span>
                <span class="me-3"><i class="bi bi-door-open"></i><?= e($bien['pieces'] ?? 0) ?> pieces</span>
                <span><?= e($bien['type'] ?? 'Type non precise') ?></span>
            </p>
            <a class="btn btn-outline-primary w-100" href="<?= url('bien', ['id' => $bien['id_b']]) ?>">Voir le detail</a>
        </div>
    </article>
</div>

