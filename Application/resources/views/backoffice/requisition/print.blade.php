<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Orden de Materiales - N° {{ str_pad($requisition->requisition_id, 4, '0', STR_PAD_LEFT) }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; color: #333; }
        .header { display: flex; align-items: center; justify-content: space-between; border-bottom: 2px solid #ddd; padding-bottom: 20px; margin-bottom: 20px; }
        .logo { max-height: 80px; }
        .company-info { text-align: right; }
        .company-name { font-size: 24px; font-weight: bold; color: #2c3e50; }
        .company-website { color: #7f8c8d; font-size: 14px; }
        .doc-title { font-size: 20px; text-transform: uppercase; margin-bottom: 20px; font-weight: bold; color: #2c3e50; text-align: center; background: #f9f9f9; padding: 10px; border: 1px solid #eee; }
        .info-table { width: 100%; margin-bottom: 30px; border-collapse: collapse; }
        .info-table td { padding: 8px; border: 1px solid #eee; }
        .info-table strong { color: #2c3e50; }
        .materials-table { width: 100%; border-collapse: collapse; margin-bottom: 40px; }
        .materials-table th, .materials-table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        .materials-table th { background-color: #f1f5f8; color: #2c3e50; }
        .signature-section { margin-top: 50px; display: flex; justify-content: space-between; }
        .signature-box { width: 45%; text-align: center; }
        .signature-line { border-top: 1px solid #000; margin-top: 80px; padding-top: 10px; font-weight: bold; }
        
        /* Inputs adaptables a la impresión */
        .input-qty { width: 60px; padding: 5px; text-align: center; font-size: 16px; border: 1px solid #008CBA; border-radius: 4px; color: #000; font-weight: bold; background: #ebf5fa; }
        
        .no-print-btn { display: block; margin: 0 auto 30px; padding: 12px 25px; background-color: #008CBA; color: white; border: none; border-radius: 4px; cursor: pointer; text-align: center; text-decoration: none; font-size: 16px; font-weight: bold; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .no-print-alert { background: #fff3cd; color: #856404; padding: 10px; text-align: center; margin-bottom: 20px; border: 1px solid #ffeeba; border-radius: 4px; }
        .btn-remove-row { background: #e74c3c; color: white; border: none; border-radius: 4px; padding: 4px 10px; cursor: pointer; font-size: 13px; font-weight: bold; }
        .btn-remove-row:hover { background: #c0392b; }
        
        @media print {
            .no-print-btn, .no-print-alert, .col-action { display: none !important; }
            .input-qty { border: none !important; padding: 0 !important; font-weight: bold; background: transparent !important; outline: none; -webkit-appearance: none; appearance: none; color: #000; font-size: 16px; }
            body { margin: 0; }
            @page { margin: 1.5cm; }
        }
    </style>
</head>
<body>

    <div class="no-print-alert">
        <strong>💡 Tips:</strong>
        Puedes modificar las cantidades en los recuadros azules antes de imprimir. |
        El botón <strong style="color:#e74c3c;">✕</strong> permite eliminar materiales cuyo stock supera la cantidad solicitada (ya cubiertos por inventario). Al imprimir, los controles desaparecerán.
    </div>
    <button class="no-print-btn" onclick="window.print()">🖨️ Imprimir Pedido</button>

    <div class="header">
        <div>
            <img src="https://boomer-elecomm.com/img/logo/logo_boomer-elecomm-h.png" alt="Boomer Elecomm" class="logo">
        </div>
        <div class="company-info">
            <div class="company-name">Boomer Elecomm</div>
            <div class="company-website">https://boomer-elecomm.com</div>
        </div>
    </div>

    <div class="doc-title">
        Orden de Solicitud de Materiales N° {{ str_pad($requisition->requisition_id, 4, '0', STR_PAD_LEFT) }}
    </div>

    <table class="info-table">
        <tr>
            <td style="width: 50%;"><strong>Cliente:</strong> {{ $requisition->work?->location?->client?->client_name ?? 'N/A' }}</td>
            <td style="width: 50%;"><strong>Fecha de Impresión:</strong> {{ now()->format('d/m/Y h:i A') }}</td>
        </tr>
        <tr>
            <td><strong>Obra:</strong> {{ $requisition->work?->work_name ?? 'N/A' }}</td>
            <td><strong>Fecha Aprobación:</strong> {{ \Carbon\Carbon::parse($requisition->date_approved)->format('d/m/Y h:i A') }}</td>
        </tr>
    </table>

    <table class="materials-table" id="materials-table">
        <thead>
            <tr>
                <th style="width: 50px; text-align: center;">#</th>
                <th>Descripción del Material</th>
                <th>Categoría</th>
                <th style="width: 100px; text-align: center;">Cantidad</th>
                <th class="col-action" style="width: 50px; text-align: center;">-</th>
            </tr>
        </thead>
        <tbody>
            @foreach($requisition->materials as $index => $mat)
                <tr>
                    <td class="row-num" style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $mat->material_name }}</td>
                    <td>{{ $mat->category->material_category_name ?? 'N/A' }}</td>
                    <td style="text-align: center;">
                        <input type="number" class="input-qty" value="{{ $mat->pivot->quantity }}" min="0" step="any">
                    </td>
                    <td class="col-action" style="text-align: center;">
                        @if($mat->stock > $mat->pivot->quantity)
                            <button class="btn-remove-row" onclick="removeRow(this)" title="Eliminar (stock suficiente en almacén)">✕</button>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <script>
        function removeRow(btn) {
            const row = btn.closest('tr');
            row.remove();
            renumberRows();
        }

        function renumberRows() {
            const tbody = document.querySelector('#materials-table tbody');
            const rows  = tbody.querySelectorAll('tr');
            rows.forEach(function(row, i) {
                row.querySelector('.row-num').textContent = i + 1;
            });
        }
    </script>

    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line">
                Autorizado Administrativo<br>
                {{ \App\Models\User::find($requisition->approved_by)->name ?? 'Administración' }}
            </div>
        </div>
        <div class="signature-box">
            <div class="signature-line">
                Procesado por (Compras)<br>
                {{ $user->name }}<br>
                <span style="font-size: 12px; font-weight: normal;">{{ $user->email }}</span>
            </div>
        </div>
    </div>

</body>
</html>
