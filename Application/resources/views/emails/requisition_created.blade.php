<!DOCTYPE html>
<html>

<head>
    <title>Nueva Requisición de Materiales</title>
</head>

<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">

    <div style="text-align: center; margin-bottom: 20px;">
        <img src="https://boomer-elecomm.com/img/logo/logo_boomer-elecomm-h.png" alt="Boomer Elecomm" style="max-height: 80px;">
    </div>

    <h2 style="color: #0056b3;">Estimado(a) Supervisor(a) {{ $requisition->work->supervisor->name }},</h2>

    <p>Se ha generado una nueva requisición de materiales en el sistema para una de sus obras asignadas.</p>

    <div style="background-color: #f9f9f9; padding: 15px; border-left: 4px solid #0056b3; margin-bottom: 20px;">
        <strong>Detalles Generales:</strong><br>
        <strong>N° de Requisición:</strong> {{ str_pad($requisition->requisition_id, 4, '0', STR_PAD_LEFT) }}<br>
        <strong>Cliente:</strong> {{ $requisition->work->client->client_name ?? 'N/A' }}<br>
        <strong>Obra:</strong> {{ $requisition->work->work_name }} ({{ $requisition->work->work_reference }})<br>
        <strong>Técnico Solicitante:</strong> {{ $requisition->user->name }}<br>
        <strong>Fecha de Solicitud:</strong> {{ $requisition->created_at->format('d/m/Y H:i A') }}<br>
        <strong>Prioridad:</strong> {{ ucfirst($requisition->priority) }}<br>
        <strong>Comentarios:</strong>
        @if($requisition->comments->count() > 0)
            <br>
            <ul>
                @foreach($requisition->comments as $comment)
                    <li>{{ $comment->comment }}</li>
                @endforeach
            </ul>
        @else
            <em>(Sin comentarios adicionales)</em>
        @endif
    </div>

    <h3>Materiales Solicitados:</h3>
    <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
        <thead>
            <tr>
                <th style="border: 1px solid #ddd; padding: 8px; background-color: #f2f2f2; text-align: left;">Categoría</th>
                <th style="border: 1px solid #ddd; padding: 8px; background-color: #f2f2f2; text-align: left;">Material</th>
                <th style="border: 1px solid #ddd; padding: 8px; background-color: #f2f2f2; text-align: center;">Cantidad</th>
            </tr>
        </thead>
        <tbody>
            @foreach($requisition->materials as $mat)
                <tr>
                    <td style="border: 1px solid #ddd; padding: 8px;">{{ $mat->category->material_category_name ?? 'N/A' }}</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">{{ $mat->material_name }}</td>
                    <td style="border: 1px solid #ddd; padding: 8px; text-align: center; font-weight: bold;">{{ $mat->pivot->quantity }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p style="margin-top: 30px;">
        Por favor diríjase al sistema Backoffice para revisar, aprobar o rechazar esta solicitud.
    </p>
    <p>
        <a href="{{ route('requisitions.index', ['status' => 'pending']) }}" style="background-color: #0056b3; color: #fff; padding: 10px 15px; text-decoration: none; border-radius: 5px;">Ir al Sistema</a>
    </p>

    <hr style="border: 0; height: 1px; background: #ddd; margin: 30px 0;">
    <p style="font-size: 12px; color: #777;">
        Este es un mensaje automático generado por Boomer Elecomm, por favor no responda a este correo electrónico.
    </p>
</body>

</html>