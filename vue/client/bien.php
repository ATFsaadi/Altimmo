<div class="container py-5">
    <nav class="mb-3">
        <a href="<?= url('biens') ?>" class="text-decoration-none"><i class="bi bi-arrow-left"></i>Retour aux biens</a>
    </nav>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="page-panel overflow-hidden">
                <img src="<?= e(imageBien($bien)) ?>" alt="Bien a <?= e($bien['ville']) ?>" class="w-100" style="height:420px;object-fit:cover;">
                <div class="p-4">
                    <div class="d-flex justify-content-between gap-3 align-items-start">
                        <div>
                            <h1 class="section-title h2 mb-1">Bien a <?= e($bien['ville']) ?></h1>
                            <p class="meta mb-0"><?= e($bien['cp']) ?> <?= e($bien['ville']) ?></p>
                        </div>
                        <span class="badge <?= !empty($bien['vendu']) ? 'text-bg-secondary' : 'text-bg-success' ?>">
                            <?= !empty($bien['vendu']) ? 'Vendu' : 'Disponible' ?>
                        </span>
                    </div>
                    <div class="price my-3"><?= formatPrix($bien['prix']) ?></div>
                    <div class="row g-3 mb-4">
                        <div class="col-4"><div class="border rounded p-3 text-center"><strong><?= e($bien['superficie']) ?></strong><br><span class="meta">m2</span></div></div>
                        <div class="col-4"><div class="border rounded p-3 text-center"><strong><?= e($bien['pieces']) ?></strong><br><span class="meta">pieces</span></div></div>
                        <div class="col-4"><div class="border rounded p-3 text-center"><strong><?= e($bien['type'] ?? 'N/A') ?></strong><br><span class="meta">type</span></div></div>
                    </div>
                    <h2 class="h4">Description</h2>
                    <p><?= nl2br(e($bien['description'])) ?></p>
                    <p class="meta mb-0">Publie le <?= !empty($bien['date_v']) ? date('d/m/Y', strtotime($bien['date_v'])) : 'date inconnue' ?></p>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="page-panel p-4">
                <h2 class="h4">Contacter l'agent</h2>
                <?php if (!empty($bien['agent_nom'])): ?>
                    <p class="meta">Agent : <?= e(trim(($bien['agent_prenom'] ?? '') . ' ' . ($bien['agent_nom'] ?? ''))) ?></p>
                <?php endif; ?>

                <?php if ($messageEnvoye): ?>
                    <div class="alert alert-success">Votre message a bien ete envoye.</div>
                <?php endif; ?>

                <?php if (!empty($erreurs)): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($erreurs as $erreur): ?>
                            <div><?= e($erreur) ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <form method="post">
                    <div class="mb-3">
                        <label class="form-label" for="nom">Nom complet</label>
                        <input class="form-control" id="nom" name="nom" required value="<?= estConnecte() ? e(utilisateurConnecte()['prenom'] . ' ' . utilisateurConnecte()['nom']) : e($_POST['nom'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required value="<?= estConnecte() ? e(utilisateurConnecte()['email']) : e($_POST['email'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="message">Message</label>
                        <textarea class="form-control" id="message" name="message" rows="5" required><?= e($_POST['message'] ?? "Bonjour, je suis interesse par ce bien.") ?></textarea>
                    </div>
                    <button class="btn btn-primary w-100" type="submit">Envoyer</button>
                </form>
            </div>
        </div>
    </div>
</div>

