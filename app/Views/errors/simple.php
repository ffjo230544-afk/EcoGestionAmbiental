<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?= e($title) ?> | EcoGestión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(asset('css/ecogestion.css')) ?>">
</head>

<body class="auth-page">
    <main class="container py-5" style="max-width:720px">
        <section class="eco-card p-5 text-center">
            <h1 class="h3"><?= e($title) ?></h1>
            <p class="text-muted-eco mt-3"><?= e($message) ?></p><?php if (!empty($technical)): ?>
            <pre class="text-start bg-light p-3 rounded"><?= e($technical) ?></pre><?php endif; ?><a
                href="<?= e(url()) ?>" class="btn btn-eco mt-3">Volver al inicio</a>
        </section>
    </main>
</body>

</html>