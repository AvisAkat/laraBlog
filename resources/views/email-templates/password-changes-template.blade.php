<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Password Changed</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #e2e2e2;
            font-family: Arial, Helvetica, sans-serif;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            margin: 20px 2px;
            border-radius: 8px;
        }

        .header {
            text-align: center;
            padding: 40px 0;
            background-color: #151348;
            color: #ffffff;
        }

        .content {
            font-size: 16px;
            color: #333333;
            line-height: 1.6;
        }

        .credentials {
            background-color: #f9f9f9;
            padding: 15px;
            margin: 20px 0;
            border-radius: 6px;
        }

        .credentials p {
            margin: 8px 0;
        }

        .footer {
            font-size: 12px;
            color: #777777;
            text-align: center;
            margin-top: 20px;
        }

        @media screen and (max-width: 600px) {
            .container {
                padding: 15px;
            }

            .content {
                font-size: 15px;
            }
        }
    </style>
</head>

<body>
    <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td align="center">
                <div class="container">
                    <div class="header">
                        <h2>Password <span style="color: rgb(81, 170, 81)">Successfully</span> Changed</h2>
                    </div>

                    <div class="content">
                        <p>Hello, <b>{{ $user->username }}</b></p>
                        <p>Your account password has been successfully updated. Below are your updated login details:
                        </p>

                        <div class="credentials">
                            <p><b> Username / Email:</b> {{ $user->username }} or {{ $user->email }}</p>
                            <p><b>New Password:</b> {{ $new_password }}</p>
                        </div>

                        <p>If you did not make this change, please contact our support team immediately.</p>
                        <p>For security reasons, we recommend keeping your login credentials confidential.</p>

                        <p>Best regards,<br />LaraBlog Team</p>
                    </div>

                    <div class="footer">
                        <p>&copy; {{ date('Y') }} LaraBlog. All rights reserved.</p>
                    </div>
                </div>
            </td>
        </tr>
    </table>
</body>

</html>