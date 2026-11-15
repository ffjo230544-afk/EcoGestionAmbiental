<header class="public-nav">
    <div class="container py-3 d-flex align-items-center justify-content-between"><a class="brand-logo"
            href="<?= e(url()) ?>"><img src="<?= e(asset('images/eco-mark.svg')) ?>" alt="EcoGestión"><span><span
                    class="brand-name">EcoGestión Ambiental</span><span class="brand-note">Jiutepec,
                    Morelos</span></span></a>
        <nav class="d-none d-lg-flex gap-4"><a class="text-reset" href="#servicios">Servicios</a><a class="text-reset"
                href="#reciclaje">Reciclaje</a><a class="text-reset" href="#comunidad">Comunidad</a></nav><a
            class="btn btn-eco" href="<?= e(url('login')) ?>">Iniciar sesión <i class="bi bi-arrow-right ms-1"></i></a>
    </div>
</header>

<main>
    <section class="hero">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <?php require ROOT_PATH . '/app/Views/partials/alerts.php'; ?>
                    <span class="hero-eyebrow"><i class="bi bi-geo-alt-fill"></i> Jiutepec, Morelos · Comunidad
                        UPEMOR</span>
                    <h1 class="hero-title">Unimos personas, <span>reciclaje</span> y acción ambiental.</h1>
                    <p class="hero-copy">Encuentra centros de acopio, conoce el valor de tus materiales reciclables y
                        aprende a cuidar nuestro entorno desde una sola plataforma.</p>
                    <div class="d-flex flex-wrap gap-3 mt-4"><a class="btn btn-eco btn-lg"
                            href="<?= e(url('login')) ?>">Iniciar sesión <i class="bi bi-arrow-right ms-1"></i></a><a
                            class="btn btn-eco-outline btn-lg" href="#servicios">Conocer servicios</a></div>
                    <div class="hero-proof"><span><i class="bi bi-check-circle-fill"></i>Centros de
                            acopio</span><span><i class="bi bi-check-circle-fill"></i>Educación ambiental</span><span><i
                                class="bi bi-check-circle-fill"></i>Citas en línea</span></div>
                </div>
                <div class="col-lg-6"><img class="hero-art" src="<?= e(asset('images/eco-landscape.svg')) ?>"
                        alt="Centro comunitario de reciclaje rodeado de vegetación"></div>
            </div>
        </div>
    </section>

    <?php $features = [['geo-alt-fill', 'Centros de acopio', 'Localiza puntos de recepción, revisa sus horarios y consulta los materiales aceptados.'], ['calculator-fill', 'Cotiza tus residuos', 'Consulta una estimación para plástico, cartón, vidrio, metal y otros reciclables.'], ['calendar2-check-fill', 'Agenda tu visita', 'Solicita una cita en un centro de acopio disponible.'], ['journal-richtext', 'Aprende a reciclar', 'Consulta información sobre separación, reutilización y residuos especiales.'], ['controller', 'Actividades ambientales', 'Conoce juegos y dinámicas para fortalecer la cultura ambiental.'], ['megaphone-fill', 'Noticias y campañas', 'Mantente informado sobre campañas universitarias y acciones comunitarias.']]; ?>
    <section id="servicios" class="public-section alt">
        <div class="container">
            <div class="text-center mb-5">
                <div class="section-label">Todo en un mismo lugar</div>
                <h2 class="section-title mt-2">Servicios que impulsan el cambio</h2>
                <p class="text-muted-eco">Herramientas para la comunidad de Jiutepec, Morelos.</p>
            </div>
            <div class="row g-4"><?php foreach ($features as [$icon, $name, $description]): ?><div
                    class="col-sm-6 col-xl-4">
                    <article class="feature-card">
                        <div class="feature-icon"><i class="bi bi-<?= e($icon) ?>"></i></div>
                        <h3 class="h5 fw-bold mt-4"><?= e($name) ?></h3>
                        <p class="mb-0 text-muted-eco"><?= e($description) ?></p>
                    </article>
                </div><?php endforeach; ?></div>
        </div>
    </section>

    <section class="impact-strip">
        <div class="container">
            <div class="row text-center gy-4">
                <div class="col-6 col-lg-3">
                    <div class="impact-number">6</div><span>Tipos de residuos</span>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="impact-number">3</div><span>Perfiles de usuarios</span>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="impact-number">18</div><span>Servicios proyectados</span>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="impact-number">1</div><span>Comunidad ambiental</span>
                </div>
            </div>
        </div>
    </section>

    <?php $categories = [['Vidrio', '#16a34a', 'cup-straw'], ['Cartón', '#a16207', 'box-seam'], ['Plástico', '#0284c7', 'droplet-half'], ['Metal', '#64748b', 'nut'], ['Electrónicos', '#7c3aed', 'cpu'], ['Residuos tóxicos', '#dc2626', 'exclamation-triangle']]; ?>
    <section id="reciclaje" class="public-section">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-5">
                    <div class="section-label">Separar también transforma</div>
                    <h2 class="section-title mt-2">Cada material tiene un lugar correcto.</h2>
                    <p class="text-muted-eco fs-6">Una clasificación adecuada permite recuperar materiales, reducir la
                        contaminación y proteger a quienes los manejan.</p><a class="btn btn-eco mt-3"
                        href="<?= e(url('login')) ?>">Conocer la plataforma</a>
                </div>
                <div class="col-lg-7">
                    <div class="row g-3"><?php foreach ($categories as [$name, $color, $icon]): ?><div class="col-6">
                            <div class="eco-card p-3 d-flex align-items-center gap-3"><span class="category-icon"
                                    style="color: <?= e($color) ?>; background: <?= e($color) ?>18"><i
                                        class="bi bi-<?= e($icon) ?>"></i></span><strong><?= e($name) ?></strong></div>
                        </div><?php endforeach; ?></div>
                </div>
            </div>
        </div>
    </section>

    <section id="comunidad" class="public-section pt-0">
        <div class="container">
            <div class="cta-panel text-center">
                <div class="section-label text-white opacity-75">Comunidad universitaria y ciudadanía</div>
                <h2 class="section-title mt-3">Construyamos juntos un Jiutepec más limpio.</h2>
                <p class="mx-auto mt-3 mb-4 opacity-75" style="max-width:610px">EcoGestión Ambiental conecta
                    información, educación y servicios para facilitar decisiones responsables con nuestros residuos.</p>
                <a href="<?= e(url('login')) ?>" class="btn btn-light btn-lg px-4 fw-semibold">Iniciar sesión <i
                        class="bi bi-arrow-right ms-2"></i></a>
            </div>
        </div>
    </section>
</main>

<footer class="public-footer">
    <div class="container d-flex flex-column flex-md-row gap-3 justify-content-between align-items-center"><a
            class="brand-logo" href="<?= e(url()) ?>"><img src="<?= e(asset('images/eco-mark.svg')) ?>"
                alt="EcoGestión"><span><span class="brand-name">EcoGestión Ambiental</span><span
                    class="brand-note">Universidad Politécnica del Estado de Morelos</span></span></a><span
            class="text-muted-eco"><?= date('Y') ?> · Jiutepec, Morelos · Educación ambiental</span></div>
</footer>