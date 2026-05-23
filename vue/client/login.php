<div class="container py-5">
    <div class="auth-panel overflow-hidden">
        <div class="row g-0">
            <div class="col-lg-5 d-none d-lg-block">
                <img src="<?= asset('img/login.webp') ?>" alt="Connexion Altimmo" class="w-100 auth-image">
            </div>
            <div class="col-lg-7">
                <div class="p-4 p-lg-5">
                    <h1 class="section-title h2">Connexion</h1>
                    <p class="text-muted">Accedez a votre espace client ou agent.</p>

                    <?php if ($erreur): ?>
                        <div class="alert alert-danger"><?= e($erreur) ?></div>
                    <?php endif; ?>

                    <form method="post">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="mot_de_passe" class="form-label">Mot de passe</label>
                            <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" required>
                        </div>
                        <button class="btn btn-primary w-100" type="submit">Se connecter</button>
                    </form>

                    <p class="mt-4 mb-0">Pas encore de compte ? <a href="<?= url('register') ?>">Creer un compte</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

