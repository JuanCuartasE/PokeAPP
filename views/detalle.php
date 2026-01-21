<?php
if (!$id && !$name) {
    header("Location: index.php");
    exit;
}

try {
    $pokemon = $http->get("pokemon/" . ($id ?? $name));
    $id = $pokemon['id'];
    $name = $pokemon['name'];
    $sprite = $pokemon['sprites']['other']['official-artwork']['front_default'] ?? $pokemon['sprites']['front_default'];
    $isFav = $db->isFavorite($id);
} catch (\Exception $e) {
    echo "<div class='error'>Pokémon no encontrado.</div>";
    return;
}
?>

<div class="detail-container">
    <div class="detail-header">
        <a href="javascript:history.back()" class="btn secondary">← Volver</a>
        <form action="?page=toggle_favorite" method="POST">
            <input type="hidden" name="id" value="<?= $id ?>">
            <input type="hidden" name="name" value="<?= $name ?>">
            <input type="hidden" name="sprite" value="<?= $sprite ?>">
            <input type="hidden" name="redirect" value="<?= $_SERVER['REQUEST_URI'] ?>">
            <button type="submit" class="btn <?= $isFav ? '' : 'secondary' ?>">
                <?= $isFav ? '❤️ Quitar de Favoritos' : '🤍 Agregar a Favoritos' ?>
            </button>
        </form>
    </div>

    <div class="detail-card">
        <div class="detail-image">
            <img src="<?= $sprite ?>" alt="<?= $name ?>">
        </div>
        <div class="detail-info">
            <h1>#<?= str_pad($id, 3, '0', STR_PAD_LEFT) ?> <?= ucfirst($name) ?></h1>

            <div class="detail-section">
                <h3>Tipos</h3>
                <div class="types">
                    <?php foreach ($pokemon['types'] as $type): ?>
                        <span class="pokemon-type type-<?= $type['type']['name'] ?>">
                            <?= $type['type']['name'] ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="detail-section">
                <h3>Estadísticas Base</h3>
                <div class="stats-grid">
                    <?php foreach ($pokemon['stats'] as $stat): ?>
                        <div class="stat-item">
                            <span class="stat-name"><?= str_replace('-', ' ', ucfirst($stat['stat']['name'])) ?></span>
                            <div class="stat-bar-bg">
                                <div class="stat-bar-fill"
                                    style="width: <?= min(100, ($stat['base_stat'] / 255) * 100) ?>%; background: var(--accent);">
                                </div>
                            </div>
                            <span class="stat-value"><?= $stat['base_stat'] ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="detail-section">
                <h3>Habilidades</h3>
                <div class="abilities">
                    <?php foreach ($pokemon['abilities'] as $ability): ?>
                        <span class="ability-tag"><?= ucfirst(str_replace('-', ' ', $ability['ability']['name'])) ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .detail-container {
        max-width: 900px;
        margin: 0 auto;
    }

    .detail-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .detail-card {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 3rem;
        background: var(--bg-card);
        padding: 3rem;
        border-radius: 24px;
        border: 1px solid var(--glass-border);
    }

    .detail-image img {
        width: 100%;
        height: auto;
        filter: drop-shadow(0 10px 30px rgba(0, 0, 0, 0.5));
    }

    .detail-info h1 {
        font-size: 3rem;
        margin-bottom: 1.5rem;
        text-transform: capitalize;
    }

    .detail-section {
        margin-bottom: 2rem;
    }

    .detail-section h3 {
        margin-bottom: 1rem;
        color: var(--text-muted);
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .stats-grid {
        display: flex;
        flex-direction: column;
        gap: 0.8rem;
    }

    .stat-item {
        display: grid;
        grid-template-columns: 100px 1fr 40px;
        align-items: center;
        gap: 1rem;
    }

    .stat-bar-bg {
        background: rgba(255, 255, 255, 0.1);
        height: 8px;
        border-radius: 4px;
        overflow: hidden;
    }

    .stat-bar-fill {
        height: 100%;
        transition: width 1s ease-out;
    }

    .stat-name {
        font-size: 0.85rem;
    }

    .stat-value {
        font-weight: 800;
        text-align: right;
    }

    .ability-tag {
        background: rgba(255, 255, 255, 0.1);
        padding: 0.5rem 1rem;
        border-radius: 8px;
        display: inline-block;
        margin-right: 0.5rem;
    }

    @media (max-width: 768px) {
        .detail-card {
            grid-template-columns: 1fr;
            gap: 2rem;
            padding: 2rem;
        }

        .detail-info h1 {
            font-size: 2rem;
        }
    }
</style>