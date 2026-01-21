<?php

namespace App;

use \PDO;

class Database
{
    private PDO $pdo;

    public function __construct(string $dbPath)
    {
        $this->pdo = new PDO("sqlite:" . $dbPath);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $this->init();
    }

    private function init(): void
    {
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS favorites (
            pokemon_id INTEGER PRIMARY KEY,
            name TEXT NOT NULL,
            sprite TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");
    }

    public function addFavorite(int $id, string $name, ?string $sprite): bool
    {
        $stmt = $this->pdo->prepare("INSERT OR IGNORE INTO favorites (pokemon_id, name, sprite) VALUES (?, ?, ?)");
        return $stmt->execute([$id, $name, $sprite]);
    }

    public function removeFavorite(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM favorites WHERE pokemon_id = ?");
        return $stmt->execute([$id]);
    }

    public function getFavorites(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM favorites ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    public function isFavorite(int $id): bool
    {
        $stmt = $this->pdo->prepare("SELECT 1 FROM favorites WHERE pokemon_id = ? LIMIT 1");
        $stmt->execute([$id]);
        return (bool) $stmt->fetch();
    }
}
