<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Confirm Your {{ $points }} Points Redemption for Your Travel Booking</title>
</head>
<body style="margin:0; padding:0; background:#f3f5f8; font-family: Arial, Helvetica, sans-serif;">

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f3f5f8; padding:32px 16px;">
        <tr>
            <td align="center">

                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:480px; background:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 4px 18px rgba(20,30,50,0.08);">

                    <tr>
                        <td style="background:#0f3d2e; padding:24px 32px;">
                            <span style="color:#ffffff; font-size:18px; font-weight:700; letter-spacing:.02em;">
                                {{ config('travels.brand.name', 'Travels') }}
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:32px;">

                            <p style="margin:0 0 16px; font-size:15px; color:#202b3e;">
                                Hi {{ $customerName ?: 'there' }},
                            </p>

                            <p style="margin:0 0 20px; font-size:14.5px; line-height:1.6; color:#4b5666;">
                                You have requested to redeem <strong>{{ number_format($points) }} reward {{ Str::plural('point', $points) }}</strong>
                                from your {{ config('travels.brand.name', 'Travels') }} account to receive a discount on your
                                upcoming trip booking.
                            </p>

                            <p style="margin:0 0 20px; font-size:14.5px; line-height:1.6; color:#4b5666;">
                                To authorize the points redemption, please share the OTP below with our admin/team
                                member handling your booking:
                            </p>

                            <div style="margin:0 0 20px; padding:18px; background:#f3f5f8; border-radius:10px; text-align:center;">
                                <span style="font-size:13px; color:#4b5666;">🔐 OTP</span><br>
                                <span style="font-size:32px; font-weight:700; letter-spacing:.3em; color:#0f3d2e;">
                                    {{ $otp }}
                                </span>
                            </div>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 20px; font-size:14px; color:#202b3e;">
                                <tr>
                                    <td style="padding:4px 0; color:#77817f;">Points to Redeem</td>
                                    <td style="padding:4px 0; text-align:right; font-weight:700;">{{ number_format($points) }} Points</td>
                                </tr>
                                <tr>
                                    <td style="padding:4px 0; color:#77817f;">Purpose</td>
                                    <td style="padding:4px 0; text-align:right; font-weight:700;">Booking Discount</td>
                                </tr>
                                <tr>
                                    <td style="padding:4px 0; color:#77817f;">Customer</td>
                                    <td style="padding:4px 0; text-align:right; font-weight:700;">{{ $customerName ?: '—' }}</td>
                                </tr>
                            </table>

                            @if($summary)

                                <p style="margin:0 0 8px; font-size:11px; font-weight:700; letter-spacing:.08em; color:#d97706; text-transform:uppercase;">
                                    Departure
                                </p>

                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 20px; font-size:14px; color:#202b3e;">
                                    <tr>
                                        <td style="padding:4px 0; color:#77817f;">Tour</td>
                                        <td style="padding:4px 0; text-align:right; font-weight:700;">{{ $summary['tour_name'] ?? '—' }}</td>
                                    </tr>
                                    @if(!empty($summary['departure_date']))
                                        <tr>
                                            <td style="padding:4px 0; color:#77817f;">Departure date</td>
                                            <td style="padding:4px 0; text-align:right; font-weight:700;">{{ $summary['departure_date']->format('d M Y') }}</td>
                                        </tr>
                                    @endif
                                    @if(!empty($summary['return_date']))
                                        <tr>
                                            <td style="padding:4px 0; color:#77817f;">Return date</td>
                                            <td style="padding:4px 0; text-align:right; font-weight:700;">{{ $summary['return_date']->format('d M Y') }}</td>
                                        </tr>
                                    @endif
                                </table>

                                <p style="margin:0 0 8px; font-size:11px; font-weight:700; letter-spacing:.08em; color:#d97706; text-transform:uppercase;">
                                    Amount summary
                                </p>

                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 20px; font-size:14px; color:#202b3e; border-top:1px solid #eef1f5;">
                                    <tr>
                                        <td style="padding:8px 0 4px; color:#77817f;">Traveller count</td>
                                        <td style="padding:8px 0 4px; text-align:right; font-weight:700;">{{ $summary['traveller_count'] }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:4px 0; color:#77817f;">Subtotal</td>
                                        <td style="padding:4px 0; text-align:right; font-weight:700;">{{ $summary['currency'] }} {{ number_format($summary['subtotal'], 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:4px 0; color:#77817f;">Taxes</td>
                                        <td style="padding:4px 0; text-align:right; font-weight:700;">{{ $summary['currency'] }} {{ number_format($summary['tax'], 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:4px 0; color:#77817f;">Booking total</td>
                                        <td style="padding:4px 0; text-align:right; font-weight:700;">{{ $summary['currency'] }} {{ number_format($summary['total'], 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:4px 0; color:#77817f;">Points discount</td>
                                        <td style="padding:4px 0; text-align:right; font-weight:700; color:#b42318;">&minus;{{ $summary['currency'] }} {{ number_format($summary['points_discount'], 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:10px 0 0; border-top:1px solid #eef1f5; font-weight:700;">Payable amount</td>
                                        <td style="padding:10px 0 0; border-top:1px solid #eef1f5; text-align:right; font-weight:700;">{{ $summary['currency'] }} {{ number_format($summary['payable_amount'], 2) }}</td>
                                    </tr>
                                </table>

                            @endif

                            <p style="margin:0 0 20px; font-size:14.5px; line-height:1.6; color:#4b5666;">
                                Once the OTP is verified by our admin team, your {{ number_format($points) }} points
                                will be redeemed and the applicable discount will be applied to your booking. This
                                code is valid for {{ $expiresInMinutes }} minutes.
                            </p>

                            <p style="margin:0; font-size:13px; line-height:1.6; color:#9aa2ae;">
                                If you did not request this points redemption, please do not share the OTP and
                                contact our support team. No points will be redeemed without OTP verification.
                            </p>

                        </td>
                    </tr>

                    <tr>
                        <td style="padding:22px 32px; background:#fafbfc; border-top:1px solid #eef1f5;">
                            <p style="margin:0 0 10px; font-size:13px; line-height:1.6; color:#4b5666;">
                                Thank you for choosing {{ config('travels.brand.name', 'Travels') }}.
                                We look forward to helping you plan your journey.
                            </p>

                            <p style="margin:0 0 10px; font-size:13px; color:#202b3e;">
                                Warm regards,<br>
                                {{ config('travels.brand.name', 'Travels') }} Support Team
                            </p>

                            <p style="margin:0 0 10px; font-size:12px; color:#9aa2ae;">
                                📧 {{ config('travels.contact.email', 'support@travels.com') }}
                                &nbsp;·&nbsp;
                                📞 {{ config('travels.contact.phone_primary', '') }}
                            </p>

                            <p style="margin:0; font-size:12px;">
                                <a href="{{ url('/') }}" style="color:#0f766e; font-weight:700; text-decoration:none;">
                                    Visit {{ config('travels.brand.name', 'Travels') }} →
                                </a>
                            </p>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>
</html>
