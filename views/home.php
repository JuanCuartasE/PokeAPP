<?php
$limit = 20;
$offset = isset($_GET['offset']) ? (int) $_GET['offset'] : 0;
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
$typeFilter = isset($_GET['type']) ? trim($_GET['type']) : '';

try {
    // Obtener lista de tipos para el filtro
    $typesData = $http->get("type");
    $allTypes = $typesData['results'];

    if (!empty($searchTerm)) {
        // Búsqueda por nombre o ID
        $pokemonList = ['results' => [['name' => strtolower($searchTerm), 'url' => "https://pokeapi.co/api/v2/pokemon/" . strtolower($searchTerm)]]];
        $count = 1;
    } elseif (!empty($typeFilter)) {
        // Filtrado por tipo
        $typeData = $http->get("type/{$typeFilter}");
        $pokemonFromType = $typeData['pokemon'];
        $count = count($pokemonFromType);

        // Paginación manual para el filtro por tipo
        $slicedPokemon = array_slice($pokemonFromType, $offset, $limit);
        $pokemonList = ['results' => array_map(function ($p) {
            return $p['pokemon']; }, $slicedPokemon)];
    } else {
        // Listado normal paginado
        $data = $http->get("pokemon?limit={$limit}&offset={$offset}");
        $pokemonList = $data;
        $count = $data['count'];
    }
} catch (\Exception $e) {
    echo "<div style='background:#fee; color:#c00; padding:1rem; border-radius:8px; margin-bottom:2rem;'>
            <strong>[TECHNICAL_ERROR]</strong>: " . htmlspecialchars($e->getMessage()) . "
          </div>";
    return;
}
?>

<section class="hero">
    <h1>Explorador de la Región</h1>
    <form action="index.php" method="GET" class="form-group">
        <input type="hidden" name="page" value="home">

        <input type="text" name="search" class="input-main" placeholder="ID o Nombre..."
            value="<?= htmlspecialchars($searchTerm) ?>" style="width: 200px;">

        <select name="type" class="input-main" style="width: 200px; padding-right: 2rem;">
            <option value="">Todos los tipos</option>
            <?php foreach ($allTypes as $type): ?>
                <option value="<?= $type['name'] ?>" <?= $typeFilter === $type['name'] ? 'selected' : '' ?>>
                    <?= ucfirst($type['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" class="btn">Aplicar Filtros</button>
        <?php if (!empty($searchTerm) || !empty($typeFilter)): ?>
            <a href="?page=home" class="btn secondary">Limpiar</a>
        <?php endif; ?>
    </form>
</section>

<div class="pokemon-grid">
    <?php foreach ($pokemonList['results'] as $pokemon):
        try {
            $name = $pokemon['name'];
            $details = $http->get("pokemon/{$name}");
            $id = $details['id'];
            $sprite = $details['sprites']['other']['official-artwork']['front_default'] ?? $details['sprites']['front_default'];
            $types = $details['types'];
            $isFav = $db->isFavorite($id);
        } catch (\Exception $e) {
            // Error individual de carga
            echo "<div class='pokemon-card error' style='font-size:0.7rem;'>ERR_DATA: $name</div>";
            continue;
        }
        ?>
        <div class="pokemon-card-wrapper" style="display: flex; flex-direction: column;">
            <a href="?page=detalle&id=<?= $id ?>" class="pokemon-card" style="flex: 1;">
                <img src="<?= $sprite ?>" alt="<?= $name ?>" loading="lazy">
                <h3>#<?= str_pad($id, 3, '0', STR_PAD_LEFT) ?>     <?= ucfirst($name) ?></h3>
                <div class="types">
                    <?php foreach ($types as $type): ?>
                        <span class="pokemon-type type-<?= $type['type']['name'] ?>">
                            <?= $type['type']['name'] ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            </a>
            <div class="card-actions" style="margin-top: 1rem; text-align: center;">
                <form action="?page=toggle_favorite" method="POST" style="display:inline;">
                    <input type="hidden" name="id" value="<?= $id ?>">
                    <input type="hidden" name="name" value="<?= $name ?>">
                    <input type="hidden" name="sprite" value="<?= $sprite ?>">
                    <input type="hidden" name="redirect" value="<?= $_SERVER['REQUEST_URI'] ?>">
                    <button type="submit" class="btn <?= $isFav ? '' : 'secondary' ?>"
                        style="padding: 0.5rem 1rem; font-size: 0.8rem; width: 100%;">
                        <?= $isFav ? '❤️ Quitar' : '🤍 Favorito' ?>
                    </button>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="pagination">
    <?php
    $queryString = "";
    if ($searchTerm)
        $queryString .= "&search=" . urlencode($searchTerm);
    if ($typeFilter)
        $queryString .= "&type=" . urlencode($typeFilter);
    ?>

    <?php if ($offset > 0): ?>
        <a href="?page=home&offset=<?= max(0, $offset - $limit) ?><?= $queryString ?>" class="btn">Anterior</a>
    <?php endif; ?>

    <span class="page-info">Mostrando <?= $offset + 1 ?> - <?= min($offset + $limit, $count) ?> de <?= $count ?></span>

    <?php if ($offset + $limit < $count): ?>
        <a href="?page=home&offset=<?= $offset + $limit ?><?= $queryString ?>" class="btn">Siguiente</a>
    <?php endif; ?>
</div>

<style>
    .hero {
        text-align: center;
        margin-bottom: 3rem;
    }

    .hero h1 {
        font-size: 2.5rem;
        margin-bottom: 1.5rem;
    }

    .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 2rem;
        margin-top: 3rem;
    }

    select.input-main {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='white' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 1rem center;
    }
</style>