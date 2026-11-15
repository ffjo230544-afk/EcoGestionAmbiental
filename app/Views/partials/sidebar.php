<aside
    class="eco-sidebar"
    data-sidebar
    aria-label="Menú principal"
>
    <div class="sidebar-brand">
        <a
            class="brand-logo"
            href="<?= e(url(role_home())) ?>"
        >
            <img
                src="<?= e(asset('images/eco-mark.svg')) ?>"
                alt="EcoGestión"
            >

            <span>
                <span class="brand-name">
                    EcoGestión Ambiental
                </span>

                <span class="brand-note">
                    <?= e($roleInterface['portal']) ?>
                </span>
            </span>
        </a>
    </div>

    <nav class="sidebar-scroll">
        <?php $lastSection = ''; ?>

        <?php foreach ($navigation as $item): ?>

            <?php
            if ($lastSection !== $item['section']):
                $lastSection = $item['section'];
            ?>

                <div class="sidebar-section">
                    <?= e($lastSection) ?>
                </div>

            <?php endif; ?>

            <?php $active = menu_item_active($item); ?>

            <a
                class="nav-eco-link<?= $active ? ' active' : '' ?>"
                href="<?= e(menu_url($item)) ?>"
                <?= $active ? 'aria-current="page"' : '' ?>
            >
                <i class="bi <?= e($item['icon']) ?>"></i>

                <span>
                    <?= e($item['label']) ?>
                </span>
            </a>

        <?php endforeach; ?>
    </nav>

    <div class="sidebar-foot">
        <div class="sidebar-account">
            <span class="user-avatar">
                <?= e(initials($layoutUser['nombre_completo'])) ?>
            </span>

            <span class="overflow-hidden">
                <strong class="d-block text-truncate">
                    <?= e($layoutUser['nombre_completo']) ?>
                </strong>

                <small class="text-muted-eco">
                    <?= e($layoutUser['nombre_rol']) ?>
                </small>
            </span>
        </div>
    </div>
</aside>