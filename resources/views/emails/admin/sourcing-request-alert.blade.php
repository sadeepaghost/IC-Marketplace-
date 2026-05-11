<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sourcing Request Alert</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f1f5f9; margin: 0; padding: 24px; color: #1e293b; }
        .card { background: #fff; border-radius: 8px; max-width: 640px; margin: 0 auto; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,.1); }
        .header { background: #0f172a; color: #fff; padding: 24px 32px; }
        .header h1 { margin: 0 0 4px; font-size: 20px; }
        .ref-badge { display: inline-block; background: #f59e0b; color: #000; font-size: 12px; font-weight: 700; padding: 2px 10px; border-radius: 99px; letter-spacing: .05em; }
        .body { padding: 32px; }
        .section { margin-bottom: 28px; }
        .section-title { font-size: 11px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; color: #64748b; margin-bottom: 12px; }
        .part-number { font-size: 32px; font-weight: 800; color: #0f172a; font-family: 'Courier New', monospace; letter-spacing: .05em; }
        table.info { width: 100%; border-collapse: collapse; }
        table.info td { padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
        table.info td:first-child { color: #64748b; width: 40%; }
        table.info td:last-child { font-weight: 600; }
        .ai-box { background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 6px; padding: 16px; }
        .ai-box h4 { margin: 0 0 10px; color: #1e40af; font-size: 13px; }
        .suggestion-pill { display: inline-block; background: #dbeafe; color: #1e40af; font-size: 12px; font-weight: 700; padding: 3px 10px; border-radius: 99px; margin: 3px; font-family: monospace; }
        .cta { text-align: center; margin-top: 28px; }
        .btn { display: inline-block; background: #2563eb; color: #fff; text-decoration: none; padding: 12px 28px; border-radius: 6px; font-weight: 700; font-size: 15px; }
        .footer { background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 16px 32px; font-size: 12px; color: #94a3b8; text-align: center; }
    </style>
</head>
<body>
<div class="card">
    <div class="header">
        <div class="ref-badge">{{ $request->reference_number }}</div>
        <h1 style="margin-top: 10px;">New Sourcing Request</h1>
        <p style="margin: 0; color: #94a3b8; font-size: 14px;">
            Submitted {{ $request->created_at->diffForHumans() }} &bull; {{ $request->created_at->format('d M Y, H:i') }} UTC
        </p>
    </div>

    <div class="body">

        {{-- Part Number --}}
        <div class="section">
            <div class="section-title">Requested Part</div>
            <div class="part-number">{{ strtoupper($request->part_number) }}</div>
            @if($request->quantity_required)
                <p style="margin: 8px 0 0; color: #475569;">
                    Quantity: <strong>{{ number_format($request->quantity_required) }} units</strong>
                    @if($request->target_price_usd)
                        &bull; Target Price: <strong>${{ $request->target_price_usd }} / unit</strong>
                    @endif
                </p>
            @endif
        </div>

        {{-- Customer Info --}}
        <div class="section">
            <div class="section-title">Customer Details</div>
            <table class="info">
                <tr>
                    <td>Name</td>
                    <td>{{ $request->customer_name }}</td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td>
                        <a href="mailto:{{ $request->customer_email }}" style="color:#2563eb;">
                            {{ $request->customer_email }}
                        </a>
                    </td>
                </tr>
                @if($request->customer_phone)
                <tr>
                    <td>Phone</td>
                    <td>{{ $request->customer_phone }}</td>
                </tr>
                @endif
                @if($request->company_name)
                <tr>
                    <td>Company</td>
                    <td>{{ $request->company_name }}</td>
                </tr>
                @endif
                @if($request->country)
                <tr>
                    <td>Country</td>
                    <td>{{ $request->country }}</td>
                </tr>
                @endif
            </table>
        </div>

        {{-- Customer Notes --}}
        @if($request->customer_notes)
        <div class="section">
            <div class="section-title">Customer Notes</div>
            <p style="background:#f8fafc; border-left: 3px solid #cbd5e1; padding: 12px 16px; margin: 0; font-size: 14px; color: #334155; border-radius: 0 4px 4px 0;">
                {{ $request->customer_notes }}
            </p>
        </div>
        @endif

        {{-- AI Suggestions --}}
        @if($hasSuggestions)
        <div class="section">
            <div class="section-title">AI-Suggested Alternatives</div>
            <div class="ai-box">
                <h4>🤖 Our procurement AI identified these potential alternatives:</h4>
                @foreach($request->suggested_alternatives as $alt)
                    <span class="suggestion-pill">{{ $alt['part_number'] }}</span>
                @endforeach
                <p style="margin: 10px 0 0; font-size: 13px; color: #1e40af;">
                    These may be suitable cross-references. Review before quoting.
                </p>
            </div>
        </div>
        @endif

        {{-- CTA --}}
        <div class="cta">
            <a href="{{ config('app.url') }}/admin/sourcing-requests/{{ $request->id }}" class="btn">
                Open in Admin Panel →
            </a>
        </div>

    </div>

    <div class="footer">
        This is an automated alert from {{ config('app.name') }} &bull;
        IP: {{ $request->ip_address }}
    </div>
</div>
</body>
</html>
