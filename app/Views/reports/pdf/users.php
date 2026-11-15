<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">

    <style>
        @page {
            margin: 24px 28px 34px;
        }

        body {
            color: #18392a;
            font-family: "DejaVu Sans", sans-serif;
            font-size: 9px;
        }

        .header {
            padding-bottom: 12px;
            border-bottom: 3px solid #16803c;
        }

        .logo {
            display: inline-block;
            width: 42px;
            vertical-align: middle;
        }

        .brand {
            display: inline-block;
            margin-left: 10px;
            vertical-align: middle;
        }

        .brand-title {
            margin: 0;
            color: #124e2a;
            font-size: 17px;
            font-weight: bold;
        }

        .brand-subtitle {
            margin-top: 3px;
            color: #718076;
            font-size: 9px;
        }

        h1 {
            margin: 18px 0 5px;
            font-size: 20px;
        }

        .meta {
            margin-bottom: 14px;
            color: #65766b;
        }

        .filters {
            width: 100%;
            margin-bottom: 14px;
            padding: 8px;
            background: #eff8f1;
            border: 1px solid #d7eadb;
        }

        .filters span {
            display: inline-block;
            margin-right: 18px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            display: table-header-group;
        }

        th {
            padding: 7px 5px;
            color: #ffffff;
            background: #16803c;
            font-size: 8px;
            text-align: left;
        }

        td {
            padding: 6px 5px;
            border-bottom: 1px solid #dfe8e2;
            vertical-align: top;
        }

        tbody tr:nth-child(even) {
            background: #f7faf8;
        }

        .status {
            font-weight: bold;
            text-transform: capitalize;
        }

        .summary {
            margin-top: 14px;
            padding: 8px;
            background: #f4f7f5;
            border-left: 4px solid #16803c;
        }

        .footer {
            position: fixed;
            right: 0;
            bottom: -20px;
            left: 0;
            color: #75827a;
            font-size: 8px;
            text-align: center;
        }
    </style>
</head>

<body>
    <header class="header">
        <?php if ($logoData !== ''): ?>
        <img
            class="logo"
            src="<?= e($logoData) ?>"
            alt="EcoGestión"
        >
        <?php endif; ?>

        <div class="brand">
            <div class="brand-title">
                EcoGestión Ambiental
            </div>

            <div class="brand-subtitle">
                Plataforma de gestión ambiental
            </div>
        </div>
    </header>

    <h1>
        Reporte de usuarios por rol
    </h1>

    <div class="meta">
        Generado por:
        <strong><?= e($generatedBy) ?></strong>

        · Fecha:
        <strong><?= e($generatedAt) ?></strong>
    </div>

    <div class="filters">
        <?php foreach (
            $filters as $label => $value
        ): ?>
        <span>
            <strong><?= e($label) ?>:</strong>
            <?= e($value) ?>
        </span>
        <?php endforeach; ?>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">ID</th>
                <th style="width: 19%;">Nombre</th>
                <th style="width: 19%;">Correo</th>
                <th style="width: 11%;">Teléfono</th>
                <th style="width: 11%;">Rol</th>
                <th style="width: 18%;">Centro asignado</th>
                <th style="width: 8%;">Estado</th>
                <th style="width: 9%;">Registro</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach (
                $records as $record
            ): ?>
            <tr>
                <td>
                    <?= (int) $record['id_usuario'] ?>
                </td>

                <td>
                    <?= e(
                        $record['nombre_completo']
                    ) ?>
                </td>

                <td>
                    <?= e($record['correo']) ?>
                </td>

                <td>
                    <?= e(
                        $record['telefono']
                        ?: 'No disponible'
                    ) ?>
                </td>

                <td>
                    <?= e($record['nombre_rol']) ?>
                </td>

                <td>
                    <?= e(
                        $record['centro_asignado']
                        ?: 'No aplica'
                    ) ?>
                </td>

                <td class="status">
                    <?= e($record['estado']) ?>
                </td>

                <td>
                    <?= e(date_mx(
                        $record['fecha_registro'],
                        'd/m/Y'
                    )) ?>
                </td>
            </tr>
            <?php endforeach; ?>

            <?php if ($records === []): ?>
            <tr>
                <td colspan="8">
                    No se encontraron usuarios
                    con los filtros seleccionados.
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="summary">
        <strong>Total de usuarios:</strong>
        <?= count($records) ?>
    </div>

    <div class="footer">
        EcoGestión Ambiental · UPEMOR ·
        <?= date('Y') ?>
    </div>
</body>
</html>