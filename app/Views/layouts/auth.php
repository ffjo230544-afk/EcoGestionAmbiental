<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#16803c">
    <title><?= e($pageTitle ?? 'Acceso') ?> | EcoGestión Ambiental</title>
    <link rel="icon" href="<?= e(asset('images/eco-mark.svg')) ?>" type="image/svg+xml">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(asset('css/ecogestion.css')) ?>">
</head>
<body class="auth-page">
    <div class="container-fluid p-0"><div class="row g-0">
        <div class="col-lg-6 col-xl-7 auth-brand-panel">
            <a class="brand-logo position-relative z-1" href="<?= e(url()) ?>"><img src="<?= e(asset('images/eco-mark.svg')) ?>" alt="EcoGestión"><span><span class="brand-name">EcoGestión Ambiental</span><span class="brand-note">Jiutepec, Morelos</span></span></a>
            <img src="<?= e(asset('images/eco-landscape.svg')) ?>" alt="Ilustración ambiental" class="auth-illustration position-relative z-1 my-4">
            <div class="auth-message"><span class="small text-uppercase fw-semibold opacity-75">Comunidad UPEMOR</span><h2 class="mt-2">Tu participación también transforma el entorno.</h2><p class="opacity-75 mb-0">Separa residuos, encuentra centros de acopio y participa en una comunidad más sostenible.</p></div>
        </div>
        <div class="col-lg-6 col-xl-5 auth-content"><div class="auth-card"><?php require ROOT_PATH . '/app/Views/partials/alerts.php'; ?><?= $content ?></div></div>
    </div></div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= e(asset('js/ecogestion.js')) ?>"></script>
</body>
</html>
