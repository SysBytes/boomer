<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Requisición Rechazada</title>
</head>

<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;">
        <div style="text-align: center; margin-bottom: 20px;">
            <img src="https://boomer-elecomm.com/img/logo/logo_boomer-elecomm-h.png" alt="Boomer Elecomm" style="max-height: 80px;">
        </div>
        <h2 style="color: #e74c3c;">Requisición de Materiales Rechazada</h2>

        <p>Hola, {{ $requisition->user->name }}.</p>
        <p>Tu solicitud de materiales <strong>#{{ str_pad($requisition->requisition_id, 4, '0', STR_PAD_LEFT) }}</strong> para la obra <strong>{{ $requisition->work->work_name }}</strong> ha sido denegada por tu supervisor o la administración.</p>

        <div style="background: #fdf2f2; border-left: 4px solid #e74c3c; padding: 15px; border-radius: 4px; margin: 15px 0;">
            <p style="margin: 0; color: #c0392b;"><strong>Motivo del rechazo:</strong></p>
            <p style="margin: 10px 0 0; font-style: italic;">"{{ $requisition->reject_reason ?? 'No se especificó un motivo adicional.' }}"</p>
        </div>

        <p>Puedes ingresar al Área de Trabajo para ver los detalles originales de tu requisición y el historial de observaciones.</p>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ url('dashboard/requisition/show/' . $requisition->requisition_id) }}" style="background-color: #2c3e50; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;">Ver Detalles de la
                Solicitud</a>
        </div>

        <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">
        <p style="font-size: 12px; color: #999;">Este es un correo automático. Por favor no responder a esta dirección.</p>
    </div>
</body>

</html>