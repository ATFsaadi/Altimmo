<div class="container py-5">
    <div class="auth-panel overflow-hidden">
        <div class="row g-0">
            <div class="col-lg-5 d-none d-lg-block">
                <img src="<?= asset('img/register.webp') ?>" alt="Inscription Altimmo" class="w-100 auth-image">
            </div>
            <div class="col-lg-7">
                <div class="p-4 p-lg-5">
                    <h1 class="section-title h2">Inscription</h1>
                    <p class="text-muted">Creez votre compte pour contacter les agents et suivre vos messages.</p>

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
                                <label class="form-label" for="nom">Nom</label>
                                <input class="form-control" id="nom" name="nom" required value="<?= e($form['nom']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="prenom">Prenom</label>
                                <input class="form-control" id="prenom" name="prenom" required value="<?= e($form['prenom']) ?>">
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required value="<?= e($form['email']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="mot_de_passe">Mot de passe</label>
                                <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="confirm_mot_de_passe">Confirmation</label>
                                <input type="password" class="form-control" id="confirm_mot_de_passe" name="confirm_mot_de_passe" required>
                            </div>
                        </div>
                        <button class="btn btn-primary w-100 mt-4" type="submit">Creer le compte</button>
                    </form>

                    <p class="mt-4 mb-0">Deja inscrit ? <a href="<?= url('login') ?>">Se connecter</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

