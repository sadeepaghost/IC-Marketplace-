<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sourcing request received</title>
</head>
<body style="font-family: Arial, sans-serif; color: #172033;">
    <h1>We received your sourcing request</h1>

    <p>Hello {{ $request->customer_name }},</p>

    <p>
        Your request for
        <strong>{{ $request->part_number }}</strong>
        has been received.
    </p>

    <table cellpadding="8" cellspacing="0" style="border-collapse: collapse;">
        <tr>
            <th align="left">Reference</th>
            <td>{{ $request->reference_number }}</td>
        </tr>
        <tr>
            <th align="left">Part number</th>
            <td>{{ $request->part_number }}</td>
        </tr>
        <tr>
            <th align="left">Quantity</th>
            <td>{{ number_format($request->quantity_required) }}</td>
        </tr>
    </table>

    <p>A team member will review your request and contact you.</p>

    <p>Regards,<br>SadeepaElectronics</p>
</body>
</html>