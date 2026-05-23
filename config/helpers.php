<?php

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function url($page = 'accueil', $params = [])
{
    $params = array_merge(['page' => $page], $params);
    return BASE_URL . '?' . http_build_query($params);
}

function asset($path)
{
    return 'assets/' . ltrim($path, '/');
}

function redirectTo($page = 'accueil', $params = [])
{
    header('Location: ' . url($page, $params));
    exit();
}

function flash($message = null, $type = 'info')
{
    if ($message !== null) {
        $_SESSION['flash'] = [
            'message' => $message,
            'type' => $type,
        ];
        return null;
    }

    if (!isset($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

function estConnecte()
{
    return isset($_SESSION['utilisateur']);
}

function utilisateurConnecte()
{
    return $_SESSION['utilisateur'] ?? null;
}

function estAgent()
{
    return estConnecte() && (int) $_SESSION['utilisateur']['role'] >= 2;
}

function estAdmin()
{
    return estConnecte() && (int) $_SESSION['utilisateur']['role'] === 3;
}

function roleLibelle($role)
{
    switch ((int) $role) {
        case 3:
            return 'Administrateur';
        case 2:
            return 'Agent';
        default:
            return 'Client';
    }
}

function formatPrix($prix)
{
    return number_format((float) $prix, 0, ',', ' ') . ' EUR';
}

function imageBien($bien)
{
    if (!empty($bien['image'])) {
        return $bien['image'];
    }

    $surface = isset($bien['superficie']) ? (float) $bien['superficie'] : 0;

    if ($surface >= 180) {
        return asset('img/biens/exc2.webp');
    }

    if ($surface >= 120) {
        return asset('img/biens/exc1.jpg');
    }

    if ($surface >= 80) {
        return asset('img/biens/exc3.webp');
    }

    return asset('img/biens/neuf.jpg');
}

