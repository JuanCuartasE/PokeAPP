<?php
$p1_name = isset($_GET['p1']) ? $_GET['p1'] : null;
$p2_name = isset($_GET['p2']) ? $_GET['p2'] : null;

$p1 = null;
$p2 = null;

try {
    if ($p1_name)
        $p1 = $http->get("pokemon/" . strtolower($p1_name));
    if ($p2_name)
        $p2 = $http->get("pokemon/" . strtolower($p2_name));
} catch (\Exception $e) {
    echo "<div class='error'>Error al cargar pokémon para comparar.</div>";
}
?>

<section class="hero">
    <h1>Comparador Pokémon</h1>
    <form action="index.php" method="GET" class="form-group" style="margin-top: 2rem;">
        <input type="hidden" name="page" value="comparador">
        <input type="text" name="p1" class="input-main" placeholder="Pokémon 1 (ej: Pikachu)"
            value="<?= htmlspecialchars($p1_name ?: '') ?>">
        <div class="vs-circle">VS</div>
        <input type="text" name="p2" class="input-main" placeholder="Pokémon 2 (ej: Charizard)"
            value="<?= htmlspecialchars($p2_name ?: '') ?>">
        <button type="submit" class="btn">Comparar Ahora</button>
    </form>
</section>

<?php if ($p1 && $p2): ?>
    <div class="comparison-grid">
        <!-- Pokemon 1 -->
        <div class="compare-card">
            <img src="<?= $p1['sprites']['other']['official-artwork']['front_default'] ?>" alt="<?= $p1['name'] ?>">
            <h2><?= ucfirst($p1['name']) ?></h2>
        </div>

        <!-- Stats Comparison -->
        <div class="stats-comparison">
            <?php foreach ($p1['stats'] as $index => $stat):
                $s1 = $stat['base_stat'];
                $s2 = $p2['stats'][$index]['base_stat'];
                $stat_name = str_replace('-', ' ', $stat['stat']['name']);
                ?>
                <div class="stat-compare-row">
                    <span class="val <?= $s1 > $s2 ? 'winner' : '' ?>"><?= $s1 ?></span>
                    <span class="label"><?= strtoupper($stat_name) ?></span>
                    <span class="val <?= $s2 > $s1 ? 'winner' : '' ?>"><?= $s2 ?></span>
                </div>
                <div class="stat-bars">
                    <div class="bar-left">
                        <div style="width: <?= ($s1 / 255) * 100 ?>%; background: <?= $s1 > $s2 ? 'var(--accent)' : 'gray' ?>;">
                        </div>
                    </div>
                    <div class="bar-right">
                        <div style="width: <?= ($s2 / 255) * 100 ?>%; background: <?= $s2 > $s1 ? 'var(--accent)' : 'gray' ?>;">
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pokemon 2 -->
        <div class="compare-card">
            <img src="<?= $p2['sprites']['other']['official-artwork']['front_default'] ?>" alt="<?= $p2['name'] ?>">
            <h2><?= ucfirst($p2['name']) ?></h2>
        </div>
    </div>
<?php elseif ($p1_name || $p2_name): ?>
    <div style="text-align: center; color: var(--text-muted); margin-top: 3rem;">Introduce dos nombres de Pokémon para
        comparar.</div>
<?php endif; ?>

<style>
    .vs-circle {
        background: var(--accent);
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
    }

    .comparison-grid {
        display: grid;
        grid-template-columns: 1fr 2fr 1fr;
        gap: 2rem;
        margin-top: 4rem;
        align-items: center;
    }

    .compare-card {
        text-align: center;
        background: var(--bg-card);
        padding: 2rem;
        border-radius: 20px;
        border: 1px solid var(--glass-border);
    }

    .compare-card img {
        width: 100%;
        max-width: 200px;
        filter: drop-shadow(0 5px 15px rgba(0, 0, 0, 0.5));
    }

    .stat-compare-row {
        display: flex;
        justify-content: space-between;
        font-weight: 800;
        margin-bottom: 0.5rem;
    }

    .stat-compare-row .label {
        color: var(--text-muted);
        font-size: 0.8rem;
    }

    .winner {
        color: #00ff88;
        text-shadow: 0 0 10px rgba(0, 255, 136, 0.5);
    }

    .stat-bars {
        display: flex;
        gap: 2px;
        height: 6px;
        margin-bottom: 1.5rem;
    }

    .bar-left,
    .bar-right {
        flex: 1;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 3px;
        position: relative;
        overflow: hidden;
    }

    .bar-left div {
        position: absolute;
        right: 0;
        height: 100%;
        border-radius: 3px;
    }

    .bar-right div {
        position: absolute;
        left: 0;
        height: 100%;
        border-radius: 3px;
    }

    @media (max-width: 900px) {
        .comparison-grid {
            grid-template-columns: 1fr;
        }

        .stats-comparison {
            order: 3;
        }
    }
</style>