<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Requisición Aprobada</title>
</head>

<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;">
        <div style="text-align: center; margin-bottom: 20px;">
            <img src="https://boomer-elecomm.com/img/logo/logo_boomer-elecomm-h.png" alt="Boomer Elecomm" style="max-height: 80px;">
        </div>
        <h2 style="color: #4CAF50;">Requisición Aprobada Exitosamente</h2>

        <p>Hola, equipo de compras.</p>
        <p>Se ha <strong>aprobado</strong> una nueva solicitud de materiales. Por favor, revisa la información para gestionar las compras correspondientes.</p>

        <div style="background: #f9f9f9; padding: 15px; border-radius: 5px; margin: 15px 0;">
            <p style="margin: 0 0 10px;"><strong>N° de Requisición:</strong> {{ str_pad($requisition->requisition_id, 4, '0', STR_PAD_LEFT) }}</p>
            <p style="margin: 0 0 10px;"><strong>Obra / Proyecto:</strong> {{ $requisition->work->work_name }}</p>
            <p style="margin: 0 0 10px;"><strong>Técnico Solicitante:</strong> {{ $requisition->user->name }}</p>
            <p style="margin: 0;"><strong>Aprobada por:</strong> {{ \App\Models\User::find($requisition->approved_by)->name ?? 'Administración' }}</p>
        </div>

        <p>Para ver el listado detallado de herramientas / materiales y procesar esta requisición, por favor ingresa al sistema administrativo.</p>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ url('backoffice/requisitions?status=approved') }}" style="background-color: #2c3e50; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;">Ver Requisiciones Aprobadas</a>
        </div>

        <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">
        <p style="font-size: 12px; color: #999;">Generado automáticamente por el Sistema Boomer Elecomm.</p>
    </div>
</body>

</html>