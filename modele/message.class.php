<?php

class Message extends Modele
{
    public function envoyer($idExpediteur, $idRecepteur, $message)
    {
        return $this->execute(
            'INSERT INTO envoyer (id_exp, id_recept, message, date_env, lu) VALUES (?, ?, ?, NOW(), 0)',
            [$idExpediteur, $idRecepteur, $message]
        );
    }

    public function recus($idUtilisateur)
    {
        return $this->fetchAll(
            'SELECT e.*, u.nom AS exp_nom, u.prenom AS exp_prenom
             FROM envoyer e
             LEFT JOIN users u ON e.id_exp = u.id_u
             WHERE e.id_recept = ?
             ORDER BY e.date_env DESC',
            [$idUtilisateur]
        );
    }

    public function envoyes($idUtilisateur)
    {
        return $this->fetchAll(
            'SELECT e.*, u.nom AS dest_nom, u.prenom AS dest_prenom
             FROM envoyer e
             LEFT JOIN users u ON e.id_recept = u.id_u
             WHERE e.id_exp = ?
             ORDER BY e.date_env DESC',
            [$idUtilisateur]
        );
    }

    public function marquerCommeLus($idUtilisateur)
    {
        return $this->execute('UPDATE envoyer SET lu = 1 WHERE id_recept = ?', [$idUtilisateur]);
    }

    public function compterNonLus($idUtilisateur)
    {
        $result = $this->fetch('SELECT COUNT(*) AS nb FROM envoyer WHERE id_recept = ? AND lu = 0', [$idUtilisateur]);
        return (int) $result['nb'];
    }

    public function compter()
    {
        $result = $this->fetch('SELECT COUNT(*) AS nb FROM envoyer');
        return (int) $result['nb'];
    }

    public function recents($limite = 10)
    {
        $stmt = $this->bdd->prepare(
            'SELECT e.*, exp.nom AS exp_nom, exp.prenom AS exp_prenom, dest.nom AS dest_nom, dest.prenom AS dest_prenom
             FROM envoyer e
             LEFT JOIN users exp ON e.id_exp = exp.id_u
             LEFT JOIN users dest ON e.id_recept = dest.id_u
             ORDER BY e.date_env DESC
             LIMIT :limite'
        );
        $stmt->bindValue(':limite', (int) $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}

