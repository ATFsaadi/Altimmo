<?php

class Utilisateur extends Modele
{
    public function tous()
    {
        return $this->fetchAll('SELECT * FROM users ORDER BY role DESC, nom ASC, prenom ASC');
    }

    public function destinataires($idUtilisateur)
    {
        return $this->fetchAll(
            'SELECT id_u, nom, prenom, email, role FROM users WHERE id_u != ? ORDER BY nom, prenom',
            [$idUtilisateur]
        );
    }

    public function trouverParId($id)
    {
        return $this->fetch('SELECT * FROM users WHERE id_u = ?', [$id]);
    }

    public function trouverParEmail($email)
    {
        return $this->fetch('SELECT * FROM users WHERE email = ?', [$email]);
    }

    public function emailExiste($email, $idIgnore = null)
    {
        if ($idIgnore) {
            $result = $this->fetch('SELECT COUNT(*) AS nb FROM users WHERE email = ? AND id_u != ?', [$email, $idIgnore]);
        } else {
            $result = $this->fetch('SELECT COUNT(*) AS nb FROM users WHERE email = ?', [$email]);
        }

        return (int) $result['nb'] > 0;
    }

    public function creer($nom, $prenom, $email, $motDePasse, $ip = null, $role = 1)
    {
        $this->execute(
            'INSERT INTO users (nom, prenom, email, password, ip, role) VALUES (?, ?, ?, ?, ?, ?)',
            [$nom, $prenom, $email, password_hash($motDePasse, PASSWORD_DEFAULT), $ip, $role]
        );

        return $this->bdd->lastInsertId();
    }

    public function trouverOuCreerContact($nom, $email)
    {
        $utilisateur = $this->trouverParEmail($email);

        if ($utilisateur) {
            return $utilisateur['id_u'];
        }

        $this->execute(
            'INSERT INTO users (nom, prenom, email, password, role) VALUES (?, ?, ?, NULL, 1)',
            [$nom, '', $email]
        );

        return $this->bdd->lastInsertId();
    }

    public function mettreAJourProfil($id, $nom, $prenom, $email)
    {
        return $this->execute(
            'UPDATE users SET nom = ?, prenom = ?, email = ? WHERE id_u = ?',
            [$nom, $prenom, $email, $id]
        );
    }

    public function changerMotDePasse($id, $hash)
    {
        return $this->execute('UPDATE users SET password = ? WHERE id_u = ?', [$hash, $id]);
    }

    public function changerRole($id, $role)
    {
        return $this->execute('UPDATE users SET role = ? WHERE id_u = ?', [$role, $id]);
    }

    public function supprimer($id)
    {
        return $this->execute('DELETE FROM users WHERE id_u = ?', [$id]);
    }

    public function premierAgentDisponible()
    {
        return $this->fetch('SELECT * FROM users WHERE role >= 2 ORDER BY role DESC, id_u ASC LIMIT 1');
    }

    public function compter()
    {
        $result = $this->fetch('SELECT COUNT(*) AS nb FROM users');
        return (int) $result['nb'];
    }

    public function compterAgents()
    {
        $result = $this->fetch('SELECT COUNT(*) AS nb FROM users WHERE role >= 2');
        return (int) $result['nb'];
    }
}

