<?php

class Modele
{
    protected $bdd;

    public function __construct($bdd)
    {
        $this->bdd = $bdd;
    }

    protected function fetchAll($sql, $params = [])
    {
        $stmt = $this->bdd->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    protected function fetch($sql, $params = [])
    {
        $stmt = $this->bdd->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    protected function execute($sql, $params = [])
    {
        $stmt = $this->bdd->prepare($sql);
        return $stmt->execute($params);
    }
}

