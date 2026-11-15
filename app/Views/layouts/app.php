<?php
/*
 | El layout solamente se usa en páginas protegidas.
 | Aun así, verifico que exista una sesión.
 */
$layoutUser = auth_user();

if ($layoutUser === null) {
    redirect('login');
}

$roleInterfaces = [
    'admin' => [
        'title' => 'Panel administrativo',
        'portal' => 'Panel administrativo',
        'subtitle' => 'Control general de EcoGestión Ambiental',
    ],
    'coordinador' => [
        'title' => 'Panel del coordinador',
        'portal' => 'Panel del coordinador',
        'subtitle' => 'Operación y seguimiento ambiental',
    ],
    'ciudadano' => [
        'title' => 'Mi panel ciudadano',
        'portal' => 'Portal ciudadano',
        'subtitle' => 'Servicios ambientales de EcoGestión',
    ],
];

$roleInterface = $roleInterfaces[auth_role()]
    ?? [
        'title' => 'EcoGestión Ambiental',
        'portal' => 'Plataforma ambiental',
        'subtitle' => 'Gestión ambiental',
    ];

$navigation = menu_items();
?>

<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="theme-color" content="#16803c">

    <title>
        <?= e($pageTitle ?? $roleInterface['title']) ?>
        | EcoGestión Ambiental
    </title>

    <link rel="icon" href="<?= e(asset('images/eco-mark.svg')) ?>" type="image/svg+xml">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <?php if (!empty($useLeaflet)): ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css">
    <?php endif; ?>

    <link rel="stylesheet" href="<?= e(asset('css/ecogestion.css')) ?>">

    <?php if (!empty($useGames)): ?>
    <link rel="stylesheet" href="<?= e(asset('css/games.css')) ?>">
    <?php endif; ?>

</head>

<body>
    <div class="app-shell">
        <?php require ROOT_PATH
            . '/app/Views/partials/sidebar.php'; ?>

        <div class="sidebar-overlay" data-sidebar-overlay></div>

        <div class="main-area">
            <?php require ROOT_PATH
                . '/app/Views/partials/topbar.php'; ?>

            <main class="main-content">
                <?php require ROOT_PATH
                    . '/app/Views/partials/alerts.php'; ?>

                <?= $content ?>

                <p class="mt-5 mb-2 text-center
                           text-muted-eco small">
                    EcoGestión Ambiental · UPEMOR ·
                    <?= date('Y') ?>
                </p>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <?php if (!empty($useLeaflet)): ?>
    <script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>

    <script src="<?= e(asset('js/map.js')) ?>"></script>
    <?php endif; ?>
    <?php if (!empty($useQuoteCalculator)): ?>
    <script src="<?= e(
            asset('js/quote-calculator.js')
        ) ?>"></script>
    <?php endif; ?>

<?php if (!empty($useGames)): ?>
<script src="<?= e(asset('js/games.js')) ?>"></script>
<?php endif; ?>

<?php if (!empty($useAppointments)): ?>
<script src="<?= e(
    asset('js/appointments.js')
) ?>"></script>
<?php endif; ?>

<?php if (!empty($useCalendar)): ?>
<script src="<?= e(
    asset('js/calendar.js')
) ?>"></script>
<?php endif; ?>



<script src="<?= e(asset('js/ecogestion.js')) ?>"></script>







</body>

</html>