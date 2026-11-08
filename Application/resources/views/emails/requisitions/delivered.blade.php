<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Requisición Entregada</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;">
        <div style="text-align: center; margin-bottom: 20px;">
            <img src="https://boomer-elecomm.com/img/logo/logo_boomer-elecomm-h.png" alt="Boomer Elecomm" style="max-height: 80px;">
        </div>
        <h2 style="color: #2e86c1;">¡Tus materiales han sido entregados!</h2>
        
        <p>Hola, {{ $requisition->user->name }}.</p>
        <p>Te informamos que tu solicitud de materiales para el proyecto ha sido marcada oficialmente como <strong>entregada / despachada</strong>.</p>
        
        <div style="background: #ebf5fa; padding: 15px; border-radius: 5px; margin: 15px 0;">
            <p style="margin: 0 0 10px;"><strong>N° de Requisición:</strong> {{ str_pad($requisition->requisition_id, 4, '0', STR_PAD_LEFT) }}</p>
            <p style="margin: 0 0 10px;"><strong>Obra / Proyecto:</strong> {{ $requisition->work->work_name }}</p>
            <p style="margin: 0;"><strong>Operación validada por tu supervisor.</strong></p>
        </div>

        <p>Puedes ingresar al Área de Trabajo si deseas ver el detalle histórico de esta requisición.</p>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ url('dashboard/requisition/show/' . $requisition->requisition_id) }}" style="background-color: #2e86c1; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;">Ver Detalles</a>
        </div>

        <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">
        <p style="font-size: 12px; color: #999;">Generado automáticamente por el Sistema Boomer Elecomm.</p>
    </div>
</body>
</html>
