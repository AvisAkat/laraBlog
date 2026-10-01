<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="x-apple-disable-message-reformatting">
  <meta name="color-scheme" content="light">
  <meta name="supported-color-schemes" content="light">
  <title>{{$post->title}}</title>
  <style>
    /* Client resets */
    body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
    table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
    img { -ms-interpolation-mode: bicubic; border: 0; outline: none; text-decoration: none; display: block; }
    body { margin: 0 !important; padding: 0 !important; width: 100% !important; }
    a { color: #0025cc; }
 
    /* Responsive */
    @media only screen and (max-width: 620px) {
      .container { width: 100% !important; }
      .px { padding-left: 20px !important; padding-right: 20px !important; }
      .full-img { width: 100% !important; height: auto !important; }
      .h1 { font-size: 24px !important; line-height: 1.3 !important; }
      .btn a { display: block !important; }
      .stack { display: block !important; width: 100% !important; }
      .stack-gap { padding-top: 16px !important; padding-left: 0 !important; }
      .hide-mobile { display: none !important; }
    }
  </style>
</head>
<body style="margin:0; padding:0; background-color:#f3f4fa;">
 
  <!-- Preheader (inbox preview text) -->
  <div style="display:none; max-height:0; overflow:hidden; mso-hide:all;">
    {{$post->meta_description}}
    &#8199;&#65279;&#847;&#8199;&#65279;&#847;&#8199;&#65279;&#847;&#8199;&#65279;&#847;&#8199;&#65279;&#847;&#8199;&#65279;&#847;
  </div>
 
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f3f4fa;">
    <tr>
      <td align="center" style="padding:24px 12px 32px 12px;">
 
        <!-- "View in browser" link -->
        <table role="presentation" class="container" width="600" cellpadding="0" cellspacing="0" border="0" style="width:600px; max-width:600px;">
          <tr>
            <td align="right" style="padding:0 4px 12px 4px; font-family:Arial, Helvetica, sans-serif; font-size:12px; color:#8a8fa8;">
              <a href="{{ route('blog.read_post', $post->slug)  }}" style="color:#8a8fa8; text-decoration:underline;">View in browser</a>
            </td>
          </tr>
        </table>
 
        <!-- Main card -->
        <table role="presentation" class="container" width="600" cellpadding="0" cellspacing="0" border="0" style="width:600px; max-width:600px; background-color:#ffffff; border-radius:12px; overflow:hidden;">
 
          <!-- Colour bar -->
          <tr>
            <td height="6" style="height:6px; line-height:6px; font-size:0; background-color:#0025cc; background-image:linear-gradient(90deg, #0025cc 0%, #be5897 70%, #e374b9 100%);">&nbsp;</td>
          </tr>
 
          <!-- Logo / brand -->
          <tr>
            <td class="px" style="padding:24px 40px; font-family:Arial, Helvetica, sans-serif;">
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                  <td style="vertical-align:middle;">
                    <a href="{{ route('blog.home') }}" style="text-decoration:none;">
                      <img src="{{ asset('images/site/').isset(settings()->site_logo) ? settings()->site_logo : '' }}" width="120" alt="" style="width:120px; max-width:120px; height:auto; font-size:18px; font-weight:bold; color:#0025cc;">
                    </a>
                  </td>
                  <td align="right" class="hide-mobile" style="vertical-align:middle; font-size:13px; color:#8a8fa8;">
                    New on the blog
                  </td>
                </tr>
              </table>
            </td>
          </tr>
 
          <!-- Post image -->
          <tr>
            <td class="px" style="padding:0 40px;">
              <a href="{{ route('blog.read_post', $post->slug) }}" style="text-decoration:none;">
                <img src="{{ asset('images/posts/'.$post->featured_image) }}" width="520" alt="Post Image" class="full-img"
                     style="width:520px; max-width:100%; height:auto; border-radius:10px; background-color:#e8eaf6; font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#6b7090;">
              </a>
            </td>
          </tr>
 
          <!-- Category + meta -->
          <tr>
            <td class="px" style="padding:24px 40px 0 40px; font-family:Arial, Helvetica, sans-serif;">
              <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                <tr>
                  <td style="background-color:#fbf3f8; border-radius:20px; padding:5px 12px; font-size:12px; font-weight:bold; color:#be5897;">
                    {{ $post->category }}
                  </td>
                  <td style="padding-left:12px; font-size:13px; color:#8a8fa8;">
                    {{ $post->created_at }} &nbsp;|&nbsp; {{ readingDuration($post->title, $post->content) }}
                                        @choice('min|mins', readingDuration($post->title, $post->content)) read
                  </td>
                </tr>
              </table>
            </td>
          </tr>
 
          <!-- Title -->
          <tr>
            <td class="px" style="padding:16px 40px 0 40px; font-family:Arial, Helvetica, sans-serif;">
              <h1 class="h1" style="margin:0; font-size:28px; line-height:1.25; color:#0f1633; font-weight:bold;">
                <a href="{{ route('blog.read_post', $post->slug) }}" style="color:#0f1633; text-decoration:none;">{{ $post->title }}</a>
              </h1>
            </td>
          </tr>
 
          <!-- Description -->
          <tr>
            <td class="px" style="padding:14px 40px 0 40px; font-family:Arial, Helvetica, sans-serif; font-size:16px; line-height:1.65; color:#3a3f5c;">
              {!!Str::ucfirst(strip_words($post->content, 43)) !!}
            </td>
          </tr>
 
          <!-- Author -->
          <tr>
            <td class="px" style="padding:22px 40px 0 40px; font-family:Arial, Helvetica, sans-serif;">
              <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                <tr>
                  <td width="40" style="vertical-align:middle;">
                    <img src="{{ asset($post->author->picture) }}" width="40" height="40" alt="" style="width:40px; height:40px; border-radius:50%; background-color:#e8eaf6;">
                  </td>
                  <td style="padding-left:12px; vertical-align:middle; font-size:14px; line-height:1.4;">
                    <span style="color:#0f1633; font-weight:bold;">{{ $post->author->username }}</span><br>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
 
          <!-- CTA -->
          <tr>
            <td class="px" style="padding:28px 40px 36px 40px; font-family:Arial, Helvetica, sans-serif;">
              <table role="presentation" cellpadding="0" cellspacing="0" border="0" class="btn">
                <tr>
                  <td align="center" style="border-radius:8px; background-color:#0025cc;">
                    <!--[if mso]>
                    <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" href="{{route('blog.read_post', $post->slug)}}" style="height:48px; v-text-anchor:middle; width:220px;" arcsize="17%" stroke="f" fillcolor="#0025cc">
                      <center style="color:#ffffff; font-family:Arial, sans-serif; font-size:16px; font-weight:bold;">Read the full post</center>
                    </v:roundrect>
                    <![endif]-->
                    <!--[if !mso]><!-->
                    <a href="{{ route('blog.read_post', $post->slug) }}"
                       style="display:inline-block; padding:14px 32px; font-size:16px; font-weight:bold; color:#ffffff; text-decoration:none; border-radius:8px; font-family:Arial, Helvetica, sans-serif;">
                      Read the full post
                    </a>
                    <!--<![endif]-->
                  </td>
                </tr>
              </table>
            </td>
          </tr>
 
        </table>
 
        <!-- Footer -->
        <table role="presentation" class="container" width="600" cellpadding="0" cellspacing="0" border="0" style="width:600px; max-width:600px;">
          <tr>
            <td align="center" class="px" style="padding:24px 40px 0 40px; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:1.6; color:#8a8fa8;">
              <!-- Social links -->
              {{-- <p style="margin:0 0 12px 0;">
                <a href="{{twitter_url}}" style="color:#be5897; text-decoration:none; font-weight:bold;">X / Twitter</a>
                &nbsp;&nbsp;
                <a href="{{linkedin_url}}" style="color:#be5897; text-decoration:none; font-weight:bold;">LinkedIn</a>
                &nbsp;&nbsp;
                <a href="{{instagram_url}}" style="color:#be5897; text-decoration:none; font-weight:bold;">Instagram</a>
              </p> --}}
              <p style="margin:0 0 8px 0;">
                You're receiving this because you subscribed to updates from
                <a href="{{ route('blog.home') }}" style="color:#0025cc; text-decoration:none;">ScribbleDiary</a>.
              </p>
              <p style="margin:0 0 8px 0;">
                {{-- <a href="{{preferences_url}}" style="color:#8a8fa8; text-decoration:underline;">Email preferences</a> --}}
                &nbsp;|&nbsp;
                <a href="#" style="color:#8a8fa8; text-decoration:underline;">Unsubscribe</a>
              </p>
            </td>
          </tr>
        </table>
 
      </td>
    </tr>
  </table>
</body>
</html>