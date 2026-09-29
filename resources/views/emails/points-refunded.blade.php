@php
    $customerName = $booking->user->name ?? $booking->contact_name;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pointsRefunded }} Points Refunded to Your Wallet</title>
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

                            <p style="margin:0 0 4px; font-size:12px; font-weight:700; letter-spacing:.08em; color:#15803d; text-transform:uppercase;">
                                ✓ Points Refunded
                            </p>

                            <p style="margin:0 0 16px; font-size:15px; color:#202b3e;">
                                Hi {{ $customerName ?: 'there' }},
                            </p>

                            <p style="margin:0 0 20px; font-size:14.5px; line-height:1.6; color:#4b5666;">
                                Your booking for <strong>{{ $booking->tripName() }}</strong> has been refunded. As
                                part of this refund, the points you redeemed on this booking have been credited
                                back to your {{ config('travels.brand.name', 'Travels') }} wallet.
                            </p>

                            <div style="margin:0 0 20px; padding:18px; background:#f3f5f8; border-radius:10px; text-align:center;">
                                <span style="font-size:32px; font-weight:700; color:#15803d;">
                                    +{{ number_format($pointsRefunded) }}
                                </span>
                                <br>
                                <span style="font-size:12px; color:#77817f; text-transform:uppercase; letter-spacing:.06em;">
                                    Points Refunded
                                </span>
                            </div>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 20px; font-size:14px; color:#202b3e;">
                                <tr>
                                    <td style="padding:4px 0; color:#77817f;">Booking reference</td>
                                    <td style="padding:4px 0; text-align:right; font-weight:700;">{{ $booking->booking_number }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:4px 0; color:#77817f;">Tour</td>
                                    <td style="padding:4px 0; text-align:right; font-weight:700;">{{ $booking->tripName() }}</td>
                                </tr>
                                @if($booking->tripDepartureDate())
                                    <tr>
                                        <td style="padding:4px 0; color:#77817f;">Departure date</td>
                                        <td style="padding:4px 0; text-align:right; font-weight:700;">{{ $booking->tripDepartureDate()->format('d M Y') }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td style="padding:10px 0 0; border-top:1px solid #eef1f5; font-weight:700;">New wallet balance</td>
                                    <td style="padding:10px 0 0; border-top:1px solid #eef1f5; text-align:right; font-weight:700;">{{ number_format($newBalance) }} points</td>
                                </tr>
                            </table>

                            <p style="margin:0; font-size:13px; line-height:1.6; color:#9aa2ae;">
                                Any reward points you already earned from this booking are not affected. These
                                refunded points are available to use on your next trip right away.
                            </p>

                        </td>
                    </tr>

                    <tr>
                        <td style="padding:22px 32px; background:#fafbfc; border-top:1px solid #eef1f5;">
                            <p style="margin:0 0 10px; font-size:13px; line-height:1.6; color:#4b5666;">
                                Thank you for choosing {{ config('travels.brand.name', 'Travels') }}.
                                We look forward to helping you plan your next journey.
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
