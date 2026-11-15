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
            font-size: 8.5px;
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
            color: #124e2a;
            font-size: 17px;
            font-weight: bold;
        }

        .brand-subtitle {
            margin-top: 3px;
            color: #718076;
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
            margin-right: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            display: table-header-group;
        }

        th {
            padding: 7px 4px;
            color: #ffffff;
            background: #16803c;
            font-size: 7.5px;
            text-align: left;
        }

        td {
            padding: 6px 4px;
            border-bottom: 1px solid #dfe8e2;
            vertical-align: top;
        }

        tbody tr:nth-child(even) {
            background: #f7faf8;
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
        Reporte de centros de atención
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
                <th style="width: 15%;">Centro</th>
                <th style="width: 10%;">Tipo</th>
                <th style="width: 21%;">Dirección</th>
                <th style="width: 10%;">Municipio</th>
                <th style="width: 14%;">Servicio</th>
                <th style="width: 16%;">Coordinador</th>
                <th style="width: 7%;">Materiales</th>
                <th style="width: 7%;">Estado</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach (
                $records as $record
            ): ?>
            <tr>
                <td>
                    <strong>
                        <?= e($record['nombre']) ?>
                    </strong>
                </td>

                <td>
                    <?= e(
                        $typeLabels[
                            $record['tipo_punto']
                        ] ?? 'Punto de atención'
                    ) ?>
                </td>

                <td>
                    <?= e($record['direccion']) ?>
                </td>

                <td>
                    <?= e(
                        $record['municipio']
                        ?: 'No especificado'
                    ) ?>
                </td>

                <td>
                    <?= e(
                        $serviceLabels[
                            $record['tipo_servicio']
                        ] ?? 'Servicio ambiental'
                    ) ?>
                </td>

                <td>
                    <?= e(
                        $record['coordinadores']
                        ?: 'Sin coordinador activo'
                    ) ?>
                </td>

                <td>
                    <?= (int) (
                        $record['total_materiales']
                    ) ?>
                </td>

                <td>
                    <?= $record['estado']
                        === 'activo'
                        ? 'Activo'
                        : 'Inactivo' ?>
                </td>
            </tr>
            <?php endforeach; ?>

            <?php if ($records === []): ?>
            <tr>
                <td colspan="8">
                    No se encontraron centros con
                    los filtros seleccionados.
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="summary">
        <strong>Total de centros:</strong>
        <?= count($records) ?>
    </div>

    <div class="footer">
        EcoGestión Ambiental · UPEMOR ·
        <?= date('Y') ?>
    </div>
</body>
</html>