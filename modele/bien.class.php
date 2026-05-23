<?php

class Bien extends Modele
{
    private function conditionsRecherche($filtres, &$params)
    {
        $conditions = [];

        if (!empty($filtres['ville_cp'])) {
            $conditions[] = '(b.ville LIKE :ville OR b.cp LIKE :cp)';
            $params[':ville'] = '%' . $filtres['ville_cp'] . '%';
            $params[':cp'] = '%' . $filtres['ville_cp'] . '%';
        }

        if (!empty($filtres['type']) && $filtres['type'] !== 'tous') {
            $conditions[] = 'c.nom_cat = :type';
            $params[':type'] = $filtres['type'];
        }

        if (!empty($filtres['nb_pieces']) && $filtres['nb_pieces'] !== 'tous') {
            if ((int) $filtres['nb_pieces'] >= 5) {
                $conditions[] = 'b.pieces >= :pieces';
            } else {
                $conditions[] = 'b.pieces = :pieces';
            }
            $params[':pieces'] = (int) $filtres['nb_pieces'];
        }

        if (!empty($filtres['prix_min'])) {
            $conditions[] = 'b.prix >= :prix_min';
            $params[':prix_min'] = (float) $filtres['prix_min'];
        }

        if (!empty($filtres['prix_max'])) {
            $conditions[] = 'b.prix <= :prix_max';
            $params[':prix_max'] = (float) $filtres['prix_max'];
        }

        if (!empty($filtres['surface_min'])) {
            $conditions[] = 'b.superficie >= :surface_min';
            $params[':surface_min'] = (float) $filtres['surface_min'];
        }

        if (!empty($filtres['surface_max'])) {
            $conditions[] = 'b.superficie <= :surface_max';
            $params[':surface_max'] = (float) $filtres['surface_max'];
        }

        return $conditions ? ' WHERE ' . implode(' AND ', $conditions) : '';
    }

    public function rechercher($filtres = [], $page = 1, $parPage = 9)
    {
        $params = [];
        $where = $this->conditionsRecherche($filtres, $params);
        $offset = max(0, ((int) $page - 1) * (int) $parPage);

        $sqlBase = ' FROM biens b
            LEFT JOIN categories c ON b.cat_id = c.id_cat
            LEFT JOIN users u ON b.u_id = u.id_u';

        $stmtCount = $this->bdd->prepare('SELECT COUNT(*) AS nb' . $sqlBase . $where);
        foreach ($params as $cle => $valeur) {
            $stmtCount->bindValue($cle, $valeur);
        }
        $stmtCount->execute();
        $total = (int) $stmtCount->fetch()['nb'];

        $sql = 'SELECT b.*, c.nom_cat AS type, u.nom AS agent_nom, u.prenom AS agent_prenom'
            . $sqlBase . $where . ' ORDER BY b.id_b DESC LIMIT :limit OFFSET :offset';

        $stmt = $this->bdd->prepare($sql);
        foreach ($params as $cle => $valeur) {
            $stmt->bindValue($cle, $valeur);
        }
        $stmt->bindValue(':limit', (int) $parPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'biens' => $stmt->fetchAll(),
            'total' => $total,
            'total_pages' => (int) ceil($total / $parPage),
        ];
    }

    public function trouver($id)
    {
        return $this->fetch(
            'SELECT b.*, c.nom_cat AS type, u.nom AS agent_nom, u.prenom AS agent_prenom, u.email AS agent_email
             FROM biens b
             LEFT JOIN categories c ON b.cat_id = c.id_cat
             LEFT JOIN users u ON b.u_id = u.id_u
             WHERE b.id_b = ?',
            [$id]
        );
    }

    public function parUtilisateur($idUtilisateur)
    {
        return $this->fetchAll(
            'SELECT b.*, c.nom_cat AS type
             FROM biens b
             LEFT JOIN categories c ON b.cat_id = c.id_cat
             WHERE b.u_id = ?
             ORDER BY b.id_b DESC',
            [$idUtilisateur]
        );
    }

    public function tousPourAdmin()
    {
        return $this->fetchAll(
            'SELECT b.*, c.nom_cat AS type, u.nom AS agent_nom, u.prenom AS agent_prenom
             FROM biens b
             LEFT JOIN categories c ON b.cat_id = c.id_cat
             LEFT JOIN users u ON b.u_id = u.id_u
             ORDER BY b.id_b DESC'
        );
    }

    public function appartientA($idBien, $idUtilisateur)
    {
        $bien = $this->fetch('SELECT id_b FROM biens WHERE id_b = ? AND u_id = ?', [$idBien, $idUtilisateur]);
        return (bool) $bien;
    }

    public function creer($data)
    {
        $this->execute(
            'INSERT INTO biens (ville, cp, prix, superficie, pieces, description, cat_id, image, u_id, vendu, date_v)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())',
            [
                $data['ville'],
                $data['cp'],
                $data['prix'],
                $data['superficie'],
                $data['pieces'],
                $data['description'],
                $data['cat_id'],
                $data['image'],
                $data['u_id'],
                $data['vendu'],
            ]
        );

        return $this->bdd->lastInsertId();
    }

    public function mettreAJour($id, $data)
    {
        return $this->execute(
            'UPDATE biens
             SET ville = ?, cp = ?, prix = ?, superficie = ?, pieces = ?, description = ?, cat_id = ?, image = ?, vendu = ?
             WHERE id_b = ?',
            [
                $data['ville'],
                $data['cp'],
                $data['prix'],
                $data['superficie'],
                $data['pieces'],
                $data['description'],
                $data['cat_id'],
                $data['image'],
                $data['vendu'],
                $id,
            ]
        );
    }

    public function supprimer($id)
    {
        return $this->execute('DELETE FROM biens WHERE id_b = ?', [$id]);
    }

    public function compter()
    {
        $result = $this->fetch('SELECT COUNT(*) AS nb FROM biens');
        return (int) $result['nb'];
    }
}

