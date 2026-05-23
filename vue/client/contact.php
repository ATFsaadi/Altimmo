<div class="container py-5">
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="page-panel p-4">
                <h1 class="section-title h2">Contactez-nous</h1>
                <p class="text-muted">Votre message sera transmis a un agent ou administrateur disponible.</p>

                <?php if ($success): ?>
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
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="nom">Nom complet</label>
                            <input class="form-control" id="nom" name="nom" required value="<?= e($form['nom']) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required value="<?= e($form['email']) ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="sujet">Sujet</label>
                            <select class="form-select" id="sujet" name="sujet" required>
                                <?php foreach (['Demande d information', 'Estimation de bien', 'Vendre un bien', 'Louer un bien', 'Autre'] as $sujet): ?>
                                    <option value="<?= e($sujet) ?>" <?= $form['sujet'] === $sujet ? 'selected' : '' ?>><?= e($sujet) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="message">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="6" required><?= e($form['message']) ?></textarea>
                        </div>
                    </div>
                    <button class="btn btn-primary mt-4" type="submit">Envoyer</button>
                </form>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="page-panel p-4">
                <h2 class="h4">Agence Altimmo</h2>
                <p class="mb-1">123 Avenue des Champs-Elysees</p>
                <p class="mb-1">75008 Paris</p>
                <p class="mb-1">01 23 45 67 89</p>
                <p>contact@altimmo.fr</p>
                <hr>
                <h3 class="h5">Horaires</h3>
                <p class="mb-1">Lundi - vendredi : 9h00 - 19h00</p>
                <p>Samedi : 10h00 - 17h00</p>
                <h3 class="h5">Notre approche</h3>
                <p class="text-muted mb-0">Une equipe disponible, des annonces bien suivies, et une relation claire entre clients et agents.</p>
            </div>
        </div>
    </div>
</div>

