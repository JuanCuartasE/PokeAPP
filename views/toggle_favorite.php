<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) $_POST['id'];
    $name = $_POST['name'];
    $sprite = $_POST['sprite'] ?? '';
    $redirect = $_POST['redirect'] ?? 'index.php';

    if ($db->isFavorite($id)) {
        $db->removeFavorite($id);
    } else {
        $db->addFavorite($id, $name, $sprite);
    }

    header("Location: " . $redirect);
    exit;
}
