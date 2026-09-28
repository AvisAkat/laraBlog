<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="x-apple-disable-message-reformatting">
  <meta name="color-scheme" content="light">
  <meta name="supported-color-schemes" content="light">
  <title>New contact form message</title>
  <style>
    /* Client resets */
    body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
    table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
    img { -ms-interpolation-mode: bicubic; border: 0; outline: none; text-decoration: none; }
    body { margin: 0 !important; padding: 0 !important; width: 100% !important; }
    a { color: #0025cc; }
 
    /* Responsive */
    @media only screen and (max-width: 620px) {
      .container { width: 100% !important; }
      .px { padding-left: 20px !important; padding-right: 20px !important; }
      .stack { display: block !important; width: 100% !important; }
      .stack-label { padding-bottom: 2px !important; }
      .h1 { font-size: 22px !important; }
      .btn a { display: block !important; }
    }
  </style>
</head>
<body style="margin:0; padding:0; background-color:#f3f4fa;">
 
  <!-- Preheader (hidden preview text) -->
  <div style="display:none; max-height:0; overflow:hidden; mso-hide:all;">
    {{$name}} sent a message through your website: {{$subject}}.
  </div>
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f3f4fa;">
    <tr>
      <td align="center" style="padding:32px 12px;">
 
        <table role="presentation" class="container" width="600" cellpadding="0" cellspacing="0" border="0" style="width:600px; max-width:600px; background-color:#ffffff; border-radius:12px; overflow:hidden;">
          <!-- Top colour bar: primary → secondary -->
          <tr>
            <td height="6" style="height:6px; line-height:6px; font-size:0; background-color:#0025cc; background-image:linear-gradient(90deg, #0025cc 0%, #be5897 70%, #e374b9 100%);">&nbsp;</td>
          </tr>
          <!-- Header -->
          <tr>
            <td class="px" style="padding:32px 40px 8px 40px; font-family:Arial, Helvetica, sans-serif;">
              <p style="margin:0 0 8px 0; font-size:14px; color:#be5897; font-weight:bold;">
                New contact form message
              </p>
              <h1 class="h1" style="margin:0; font-size:26px; line-height:1.3; color:#0f1633; font-weight:bold;text-transform:capitalize;">
                {{$subject}}
              </h1>
              <p style="margin:8px 0 0 0; font-size:14px; color:#6b7090;">
                Received {{ now() }} from {{$name}} ({{$email}})
              </p>
            </td>
          </tr>
 
          <!-- Sender details -->
          <tr>
            <td class="px" style="padding:24px 40px 0 40px; font-family:Arial, Helvetica, sans-serif;">
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-top:1px solid #e6e8f2;">
                <tr>
                  <td class="stack stack-label" width="120" style="padding:14px 0; font-size:14px; color:#6b7090; vertical-align:top;">Name</td>
                  <td class="stack" style="padding:14px 0; font-size:15px; color:#0f1633; font-weight:bold;">{{$name}}</td>
                </tr>
                <tr>
                  <td class="stack stack-label" width="120" style="padding:14px 0; font-size:14px; color:#6b7090; border-top:1px solid #e6e8f2; vertical-align:top;">Email</td>
                  <td class="stack" style="padding:14px 0; font-size:15px; border-top:1px solid #e6e8f2;">
                    <a href="mailto:{{$email}}" style="color:#0025cc; text-decoration:none;">{{$email}}</a>
                  </td>
                </tr>
                <tr>
                  <td class="stack stack-label" width="120" style="padding:14px 0; font-size:14px; color:#6b7090; border-top:1px solid #e6e8f2; vertical-align:top;">Phone</td>
                  <td class="stack" style="padding:14px 0; font-size:15px; color:#0f1633; border-top:1px solid #e6e8f2;">{{$phone ?? 'Not provided'}}</td>
                </tr>
              </table>
            </td>
          </tr>
 
          <!-- Message -->
          <tr>
            <td class="px" style="padding:16px 40px 0 40px; font-family:Arial, Helvetica, sans-serif;">
              <p style="margin:0 0 10px 0; font-size:14px; color:#6b7090;">Message</p>
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                  <td style="background-color:#fbf3f8; border-left:4px solid #be5897; border-radius:6px; padding:18px 20px; font-size:15px; line-height:1.6; color:#2a2f4a; white-space:pre-line;">{{$message}}</td>
                </tr>
              </table>
            </td>
          </tr>
 
          <!-- Reply button -->
          <tr>
            <td class="px" style="padding:28px 40px 36px 40px; font-family:Arial, Helvetica, sans-serif;">
              <table role="presentation" cellpadding="0" cellspacing="0" border="0" class="btn">
                <tr>
                  <td align="center" style="border-radius:8px; background-color:#0025cc;">
                    <a href="mailto:{{$email}}?subject=Re:%20{{$subject}}" target="_blank" rel="noopener noreferrer"
                       style="display:inline-block; padding:13px 28px; font-size:15px; font-weight:bold; color:#ffffff; text-decoration:none; border-radius:8px; font-family:Arial, Helvetica, sans-serif;">
                      Reply to {{$name}}
                    </a>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
 
          <!-- Footer -->
          <tr>
            <td class="px" style="padding:18px 40px; background-color:#f7f8fc; border-top:1px solid #e6e8f2; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:1.5; color:#8a8fa8;">
              Sent from the contact form on <a href="#" style="color:#be5897; text-decoration:none;">ScribbleDiary</a>.
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>