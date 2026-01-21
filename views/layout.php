<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PokéApp - La mejor Pokedex</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>

<body>
    <header>
        <nav>
            <a href="?page=home" class="logo">POKÉAPP</a>
            <div class="nav-links">
                <a href="?page=home">Inicio</a>
                <a href="?page=favoritos">Favoritos</a>
                <a href="?page=comparador">Comparador</a>
            </div>
        </nav>
    </header>

    <main>
        <?php
        $viewFile = ROOT_PATH . "/views/{$page}.php";
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            include ROOT_PATH . "/views/404.php";
        }
        ?>
    </main>

    <footer>
        <div
            style="text-align: center; padding: 2rem; border-top: 1px solid var(--glass-border); margin-top: 2rem; color: var(--text-muted);">
            <p>&copy; 2026 PokéApp - Desarrollado Por Juan Cuartas</p>
        </div>
    </footer>
</body>

</html>