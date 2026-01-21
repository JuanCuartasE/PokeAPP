<?php
$favorites = $db->getFavorites();
?>

<section class="hero">
    <h1>Tus Pokémon Favoritos</h1>
    <p>Tienes <?= count($favorites) ?> Pokémon guardados.</p>
</section>

<?php if (empty($favorites)): ?>
    <div style="text-align: center; margin-top: 5rem;">
        <p style="color: var(--text-muted); font-size: 1.2rem;">Aún no tienes favoritos. ¡Explora y añade algunos!</p>
        <br>
        <a href="?page=home" class="btn">Ir al Inicio</a>
    </div>
<?php else: ?>
    <div class="pokemon-grid">
        <?php foreach ($favorites as $pokemon):
            $id = $pokemon['pokemon_id'];
            $name = $pokemon['name'];
            $sprite = $pokemon['sprite'];
            ?>
            <div class="pokemon-card-wrapper" style="display: flex; flex-direction: column;">
                <a href="?page=detalle&id=<?= $id ?>" class="pokemon-card" style="flex: 1;">
                    <img src="<?= $sprite ?>" alt="<?= $name ?>" loading="lazy">
                    <h3>#<?= str_pad($id, 3, '0', STR_PAD_LEFT) ?>         <?= ucfirst($name) ?></h3>
                </a>
                <div class="card-actions" style="margin-top: 1rem; text-align: center;">
                    <form action="?page=toggle_favorite" method="POST" style="display:inline;">
                        <input type="hidden" name="id" value="<?= $id ?>">
                        <input type="hidden" name="name" value="<?= $name ?>">
                        <input type="hidden" name="sprite" value="<?= $sprite ?>">
                        <input type="hidden" name="redirect" value="<?= $_SERVER['REQUEST_URI'] ?>">
                        <button type="submit" class="btn" style="padding: 0.5rem 1rem; font-size: 0.8rem; width: 100%;">
                            ❤️ Quitar
                        </button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>