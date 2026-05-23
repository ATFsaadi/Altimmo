<?php

class Categorie extends Modele
{
    public function toutes()
    {
        return $this->fetchAll('SELECT * FROM categories ORDER BY nom_cat ASC');
    }
}

