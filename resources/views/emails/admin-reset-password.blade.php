<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Your Password</title>
</head>
<body style="margin:0; padding:0; background-color:#F1F5F9; font-family: Arial, Helvetica, sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#F1F5F9; padding:40px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" border="0" style="max-width:600px; width:100%; background-color:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 4px 12px rgba(0,0,0,.06);">
                    <!-- Header -->
                    <tr>
                        <td style="background-color:#3c50e0; padding:24px 32px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td>
                                        <span style="color:#ffffff; font-size:18px; font-weight:bold;">{{ $company['name'] ?? 'Admin Panel' }}</span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <!-- Body -->
                    <tr>
                        <td style="padding:32px;">
                            <h2 style="margin:0 0 16px; font-size:20px; color:#1e293b;">Reset Your Password</h2>
                            <p style="margin:0 0 16px; font-size:14px; color:#475569; line-height:1.6;">
                                Hello {{ $admin->name }},
                            </p>
                            <p style="margin:0 0 16px; font-size:14px; color:#475569; line-height:1.6;">
                                We received a request to reset the password for your admin account
                                (<strong>{{ $admin->email }}</strong>). Click the button below to choose a new password.
                            </p>
                            <p style="margin:0 0 24px; text-align:center;">
                                <a href="{{ $resetUrl }}"
                                   style="display:inline-block; background-color:#3c50e0; color:#ffffff; padding:12px 28px; border-radius:8px; text-decoration:none; font-size:14px; font-weight:bold;">
                                    Reset Password
                                </a>
                            </p>
                            <p style="margin:0 0 16px; font-size:14px; color:#475569; line-height:1.6;">
                                If the button above does not work, copy and paste this link into your browser:
                            </p>
                            <p style="margin:0 0 24px; font-size:12px; color:#64748b; word-break:break-all; background-color:#f8fafc; padding:12px; border-radius:6px;">
                                {{ $resetUrl }}
                            </p>
                            <p style="margin:0 0 8px; font-size:13px; color:#64748b;">
                                This link will expire in {{ $expiryMinutes }} minutes.
                            </p>
                            <p style="margin:0; font-size:13px; color:#64748b;">
                                If you did not request a password reset, you can safely ignore this email.
                            </p>
                        </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                        <td style="background-color:#f8fafc; padding:16px 32px; border-top:1px solid #e2e8f0;">
                            <p style="margin:0; font-size:12px; color:#94a3b8;">
                                &copy; {{ now()->year }} {{ $company['name'] ?? 'ABRL' }}. All rights reserved.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
