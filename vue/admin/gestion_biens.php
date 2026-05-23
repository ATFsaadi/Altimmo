<div class="container py-5">
    <?php if ($action === 'ajouter' || $action === 'modifier'): ?>
        <?php
        $bienForm = $bien ?: [
            'ville' => '',
            'cp' => '',
            'prix' => '',
            'superficie' => '',
            'pieces' => '',
            'description' => '',
            'cat_id' => '',
            'image' => '',
            'vendu' => 0,
        ];
        ?>
        <div class="page-panel p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="section-title h2 mb-0"><?= $action === 'ajouter' ? 'Ajouter un bien' : 'Modifier un bien' ?></h1>
                <a href="<?= url('gestion') ?>" class="btn btn-outline-secondary">Retour</a>
            </div>

            <?php if (!empty($erreurs)): ?>
                <div class="alert alert-danger">
                    <?php foreach ($erreurs as $erreur): ?>
                        <div><?= e($erreur) ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data">
                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="ville">Ville</label>
                                <input class="form-control" id="ville" name="ville" required value="<?= e($bienForm['ville']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="cp">Code postal</label>
                                <input class="form-control" id="cp" name="cp" required value="<?= e($bienForm['cp']) ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="prix">Prix</label>
                                <input type="number" class="form-control" id="prix" name="prix" min="1" required value="<?= e($bienForm['prix']) ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="superficie">Superficie</label>
                                <input type="number" class="form-control" id="superficie" name="superficie" min="1" step="0.01" required value="<?= e($bienForm['superficie']) ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="pieces">Pieces</label>
                                <input type="number" class="form-control" id="pieces" name="pieces" min="1" required value="<?= e($bienForm['pieces']) ?>">
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="description">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="7" required><?= e($bienForm['description']) ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="mb-3">
                            <label class="form-label" for="cat_id">Type</label>
                            <select class="form-select" id="cat_id" name="cat_id" required>
                                <option value="">Choisir</option>
                                <?php foreach ($categories as $categorie): ?>
                                    <option value="<?= $categorie['id_cat'] ?>" <?= (int) $bienForm['cat_id'] === (int) $categorie['id_cat'] ? 'selected' : '' ?>>
                                        <?= e($categorie['nom_cat']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="image">Image</label>
                            <?php if (!empty($bienForm['image'])): ?>
                                <img src="<?= e(imageBien($bienForm)) ?>" alt="" class="img-fluid rounded mb-2">
                            <?php endif; ?>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        </div>

                        <?php if ($action === 'modifier'): ?>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="vendu" name="vendu" <?= !empty($bienForm['vendu']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="vendu">Marquer comme vendu</label>
                            </div>
                        <?php endif; ?>

                        <button class="btn btn-primary w-100" type="submit">
                            <?= $action === 'ajouter' ? 'Ajouter le bien' : 'Enregistrer' ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    <?php else: ?>
        <div class="page-panel p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center gap-3">
                <div>
                    <h1 class="section-title h2 mb-1">Gestion des biens</h1>
                    <p class="text-muted mb-0">Ajoutez, modifiez ou supprimez vos annonces.</p>
                </div>
                <a href="<?= url('gestion', ['action' => 'ajouter']) ?>" class="btn btn-primary"><i class="bi bi-plus-circle"></i>Ajouter</a>
            </div>
        </div>

        <div class="page-panel p-4">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                    <tr>
                        <th>Image</th>
                        <th>Bien</th>
                        <th>Prix</th>
                        <th>Surface</th>
                        <th>Statut</th>
                        <th class="text-end">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($biens as $bien): ?>
                        <tr>
                            <td><img src="<?= e(imageBien($bien)) ?>" alt=""></td>
                            <td>
                                <strong><?= e($bien['ville']) ?></strong><br>
                                <span class="text-muted"><?= e($bien['type'] ?? 'Type non precise') ?> - <?= e($bien['cp']) ?></span>
                            </td>
                            <td><?= formatPrix($bien['prix']) ?></td>
                            <td><?= e($bien['superficie']) ?> m2</td>
                            <td>
                                <span class="badge <?= !empty($bien['vendu']) ? 'text-bg-secondary' : 'text-bg-success' ?>">
                                    <?= !empty($bien['vendu']) ? 'Vendu' : 'Disponible' ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-secondary" href="<?= url('bien', ['id' => $bien['id_b']]) ?>">Voir</a>
                                <a class="btn btn-sm btn-outline-primary" href="<?= url('gestion', ['action' => 'modifier', 'id' => $bien['id_b']]) ?>">Modifier</a>
                                <a class="btn btn-sm btn-outline-danger" data-confirm="Supprimer ce bien ?" href="<?= url('gestion', ['action' => 'supprimer', 'id' => $bien['id_b']]) ?>">Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if (empty($biens)): ?>
                <div class="alert alert-info mb-0">Aucun bien pour le moment.</div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

