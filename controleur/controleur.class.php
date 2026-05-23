<?php

class Controleur
{
    protected $bdd;
    protected $utilisateurModele;
    protected $bienModele;
    protected $messageModele;
    protected $categorieModele;

    public function __construct($bdd)
    {
        $this->bdd = $bdd;
        $this->utilisateurModele = new Utilisateur($bdd);
        $this->bienModele = new Bien($bdd);
        $this->messageModele = new Message($bdd);
        $this->categorieModele = new Categorie($bdd);
    }

    protected function render($vue, $donnees = [], $titre = null)
    {
        $titrePage = $titre ?: APP_NAME;
        $nbMessagesNonLus = estConnecte()
            ? $this->messageModele->compterNonLus(utilisateurConnecte()['id_u'])
            : 0;
        extract($donnees);

        require __DIR__ . '/../vue/layouts/header.php';
        require __DIR__ . '/../vue/' . $vue . '.php';
        require __DIR__ . '/../vue/layouts/footer.php';
    }

    protected function requireLogin()
    {
        if (!estConnecte()) {
            flash('Vous devez vous connecter pour acceder a cette page.', 'warning');
            redirectTo('login');
        }
    }

    protected function requireAgent()
    {
        $this->requireLogin();

        if (!estAgent()) {
            flash("Vous n'avez pas les droits necessaires.", 'danger');
            redirectTo('accueil');
        }
    }

    protected function requireAdmin()
    {
        $this->requireLogin();

        if (!estAdmin()) {
            flash("L'espace demande est reserve aux administrateurs.", 'danger');
            redirectTo('accueil');
        }
    }

    protected function nettoyer($value)
    {
        return trim((string) $value);
    }
}
