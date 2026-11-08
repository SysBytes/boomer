<!DOCTYPE html>
<html>

<head>
    <title>Horas Extras Pendientes por Aprobar</title>
</head>

<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">

    <div style="text-align: center; margin-bottom: 20px;">
        <img src="https://boomer-elecomm.com/img/logo/logo_boomer-elecomm-h.png" alt="Boomer Elecomm" style="max-height: 80px;">
    </div>

    <h2 style="color: #0056b3;">Estimado(a) {{ $recipientName }},</h2>

    @if($recipientType === 'supervisor')
        <p>Existen horas extras pendientes de aprobación para técnicos y/o auxiliares en los proyectos que usted supervisa. Por favor revise y apruebe o rechace cada registro.</p>
    @else
        <p>Existen horas extras pendientes de aprobación en el sistema. Como administrador, le solicitamos revisar y gestionar los siguientes registros.</p>
    @endif

    <div style="background-color: #f9f9f9; padding: 15px; border-left: 4px solid #e67e22; margin-bottom: 20px;">
        <strong>Resumen:</strong><br>
        <strong>Total de registros pendientes:</strong> {{ $overtimes->count() }}<br>
        <strong>Horas totales pendientes:</strong> {{ number_format($overtimes->sum('hours'), 2) }}h
    </div>

    <h3>Detalle de Horas Extras Pendientes:</h3>
    <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
        <thead>
            <tr>
                <th style="border: 1px solid #ddd; padding: 8px; background-color: #f2f2f2; text-align: left;">Fecha</th>
                <th style="border: 1px solid #ddd; padding: 8px; background-color: #f2f2f2; text-align: left;">Usuario</th>
                <th style="border: 1px solid #ddd; padding: 8px; background-color: #f2f2f2; text-align: left;">Proyecto</th>
                <th style="border: 1px solid #ddd; padding: 8px; background-color: #f2f2f2; text-align: center;">Horas</th>
                <th style="border: 1px solid #ddd; padding: 8px; background-color: #f2f2f2; text-align: left;">Motivo</th>
            </tr>
        </thead>
        <tbody>
            @foreach($overtimes->sortBy('date') as $ot)
                <tr>
                    <td style="border: 1px solid #ddd; padding: 8px;">{{ \Carbon\Carbon::parse($ot->date)->format('d/m/Y') }}</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">{{ $ot->user->name ?? 'N/A' }}</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">{{ $ot->work->work_name ?? 'N/A' }}</td>
                    <td style="border: 1px solid #ddd; padding: 8px; text-align: center; font-weight: bold; color: #e67e22;">+{{ number_format($ot->hours, 2) }}</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">{{ $ot->type_formatted }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p style="margin-top: 30px;">
        Por favor diríjase al sistema Backoffice para revisar, aprobar o rechazar estas horas extras.
    </p>
    <p>
        <a href="{{ route('overtimes.index', ['status' => 'pending']) }}" style="background-color: #e67e22; color: #fff; padding: 10px 15px; text-decoration: none; border-radius: 5px;">Ver Horas Extras Pendientes</a>
    </p>

    <hr style="border: 0; height: 1px; background: #ddd; margin: 30px 0;">
    <p style="font-size: 12px; color: #777;">
        Este es un mensaje automático generado por Boomer Elecomm, por favor no responda a este correo electrónico.
    </p>
</body>

</html>