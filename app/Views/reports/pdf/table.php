<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <title>
        <?= e($title) ?>
    </title>

    <style>
        @page {
            margin: 22px 24px 34px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            color: #17382b;
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
        }

        .report-header {
            width: 100%;
            margin-bottom: 14px;
            border-bottom: 2px solid #168a46;
            padding-bottom: 10px;
        }

        .report-header td {
            vertical-align: middle;
        }

        .logo-cell {
            width: 52px;
        }

        .logo {
            width: 42px;
            height: 42px;
        }

        .brand {
            margin: 0;
            color: #168a46;
            font-size: 11px;
            font-weight: bold;
        }

        h1 {
            margin: 2px 0 3px;
            color: #17382b;
            font-size: 20px;
        }

        .subtitle {
            margin: 0;
            color: #667c72;
            font-size: 9px;
        }
.date-cell {
    width: 230px;
    color: #667c72;
    line-height: 1.6;
    text-align: right;
}

        .filters {
            width: 100%;
            margin-bottom: 13px;
            border-collapse: collapse;
        }

        .filters td {
            border: 1px solid #dce8e1;
            padding: 6px 8px;
        }

        .filter-label {
            width: 130px;
            background: #eff8f2;
            color: #168a46;
            font-weight: bold;
        }

        .summary {
            width: 100%;
            margin-bottom: 13px;
            border-collapse: separate;
            border-spacing: 6px 0;
        }

        .summary td {
            background: #eff8f2;
            border: 1px solid #d8e9df;
            border-radius: 5px;
            padding: 8px;
            text-align: center;
        }

        .summary-label {
            display: block;
            margin-bottom: 3px;
            color: #667c72;
            font-size: 8px;
        }

        .summary-value {
            color: #168a46;
            font-size: 12px;
            font-weight: bold;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: auto;
        }

        .data-table thead {
            display: table-header-group;
        }

        .data-table tr {
            page-break-inside: avoid;
        }

        .data-table th {
            padding: 7px 5px;
            border: 1px solid #13773d;
            background: #168a46;
            color: #ffffff;
            font-size: 8px;
            text-align: left;
        }

        .data-table td {
            padding: 6px 5px;
            border: 1px solid #dce8e1;
            color: #29483b;
            font-size: 8px;
            vertical-align: top;
            overflow-wrap: anywhere;
        }

        .data-table tbody tr:nth-child(even) {
            background: #f7fbf8;
        }

        .empty {
            padding: 24px;
            border: 1px solid #dce8e1;
            color: #667c72;
            text-align: center;
        }

        .footer {
            position: fixed;
            right: 0;
            bottom: -22px;
            left: 0;
            color: #71847b;
            font-size: 8px;
            text-align: center;
        }
    </style>
</head>

<body>
    <table class="report-header">
        <tr>
            <td class="logo-cell">
                <?php if ($logoData !== ''): ?>
                <img
                    class="logo"
                    src="<?= e($logoData) ?>"
                    alt="EcoGestión"
                >
                <?php endif; ?>
            </td>

            <td>
                <div class="brand">
                    EcoGestión Ambiental
                </div>

                <h1>
                    <?= e($title) ?>
                </h1>

                <p class="subtitle">
                    <?= e($subtitle) ?>
                </p>
            </td>

         <td class="date-cell">
    Generado por:
    <strong>
        <?= e($generatedBy) ?>
    </strong>

    <br>

    Fecha:
    <strong>
        <?= e($generatedAt) ?>
    </strong>
</td>
        </tr>
    </table>

    <?php if ($filters !== []): ?>
    <table class="filters">
        <?php foreach (
            $filters as $label => $value
        ): ?>
        <tr>
            <td class="filter-label">
                <?= e($label) ?>
            </td>

            <td>
                <?= e($value) ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php endif; ?>

    <?php if ($summary !== []): ?>
    <table class="summary">
        <tr>
            <?php foreach (
                $summary as $label => $value
            ): ?>
            <td>
                <span class="summary-label">
                    <?= e($label) ?>
                </span>

                <span class="summary-value">
                    <?= e($value) ?>
                </span>
            </td>
            <?php endforeach; ?>
        </tr>
    </table>
    <?php endif; ?>

    <?php if ($rows !== []): ?>

    <table class="data-table">
        <thead>
            <tr>
                <?php foreach (
                    $columns as $label
                ): ?>
                <th>
                    <?= e($label) ?>
                </th>
                <?php endforeach; ?>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($rows as $row): ?>
            <tr>
                <?php foreach (
                    $columns as $key => $label
                ): ?>
                <td>
                    <?= nl2br(
                        e($row[$key] ?? '')
                    ) ?>
                </td>
                <?php endforeach; ?>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php else: ?>

    <div class="empty">
        No se encontraron registros con los
        filtros seleccionados.
    </div>

    <?php endif; ?>

    <div class="footer">
        EcoGestión Ambiental · UPEMOR ·
        Documento generado por el sistema
    </div>
</body>
</html>