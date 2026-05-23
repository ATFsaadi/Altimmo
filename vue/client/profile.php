<div class="container py-5">
    <div class="page-panel p-4">
        <h1 class="section-title h2">Mon profil</h1>

        <?php if (!empty($erreurs)): ?>
            <div class="alert alert-danger">
                <?php foreach ($erreurs as $erreur): ?>
                    <div><?= e($erreur) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-lg-6">
                <h2 class="h4">Informations</h2>
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label" for="nom">Nom</label>
                        <input class="form-control" id="nom" name="nom" required value="<?= e($utilisateur['nom']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="prenom">Prenom</label>
                        <input class="form-control" id="prenom" name="prenom" required value="<?= e($utilisateur['prenom']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required value="<?= e($utilisateur['email']) ?>">
                    </div>
                    <button class="btn btn-primary" type="submit">Mettre a jour</button>
                </form>
            </div>

            <div class="col-lg-6">
                <h2 class="h4">Mot de passe</h2>
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label" for="ancien_mdp">Ancien mot de passe</label>
                        <input type="password" class="form-control" id="ancien_mdp" name="ancien_mdp" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="nouveau_mdp">Nouveau mot de passe</label>
                        <input type="password" class="form-control" id="nouveau_mdp" name="nouveau_mdp" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="confirmer_mdp">Confirmation</label>
                        <input type="password" class="form-control" id="confirmer_mdp" name="confirmer_mdp" required>
                    </div>
                    <button class="btn btn-outline-primary" type="submit" name="changer_mdp">Changer le mot de passe</button>
                </form>
            </div>
        </div>
    </div>
</div>

