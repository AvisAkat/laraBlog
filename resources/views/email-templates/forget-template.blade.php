<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
</head>

<body style="margin:0; padding:0; background-color:#f4f6f8; font-family: Arial, sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f6f8; padding:20px 0;">
        <tr>
            <td align="center">

                <!-- Main Container -->
                <table width="100%" cellpadding="0" cellspacing="0"
                    style="max-width:600px; background:#ffffff; border-radius:8px; overflow:hidden;">

                    <!-- Header -->
                    <tr>
                        <td align="center" style="background-color:#151348; padding:30px 20px;">
                            <h1 style="color:#ffffff; margin:0; font-size:24px;">Reset Your Password</h1>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:30px 20px; color:#333333; font-size:16px; line-height:1.5;">
                            <p style="margin-top:0;">Hi {{ $user->name }},</p>

                            <p>
                                We received a request to reset your password. Click the button below to set a new
                                password.
                            </p>

                            <!-- Button -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin:30px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $actionlink }}" style="background-color:#151348; color:#ffffff; text-decoration:none; 
                              padding:14px 28px; border-radius:6px; display:inline-block; 
                              font-weight:bold;">
                                            Reset Password
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p>
                                If you did not request a password reset, you can safely ignore this email. This link
                                will expire in 15 minutes.
                            </p>

                            <p style="margin-bottom:0;">
                                Thanks,<br>
                                The LaraBlog Team
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td
                            style="background-color:#f4f6f8; padding:20px; text-align:center; font-size:12px; color:#888888;">
                            <p style="margin:0;">
                                &copy; {{ date('Y') }} LaraBlog. All rights reserved.
                            </p>
                            <p style="margin:5px 0 0;">
                                If the button does not work, copy and paste this link into your browser:
                            </p>
                            <p style="word-break:break-all; margin:5px 0 0;">
                                {{ $actionlink }}
                            </p>
                        </td>
                    </tr>

                </table>
                <!-- End Main Container -->

            </td>
        </tr>
    </table>

</body>

</html>