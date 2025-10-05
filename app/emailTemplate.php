<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>{{subject}}</title>
  <style>
    body { font-family: Verdana, Geneva, sans-serif; margin:0; padding:0; background:#f8f8f8; }
    .container { max-width:600px; margin:20px auto; background:#fff; border-radius:8px; overflow:hidden; box-shadow:0 2px 5px rgba(0,0,0,.1); }
    .header { background:#004080; padding:20px; text-align:center; }
    .header img { max-width:150px; }
    .content { padding:20px; }
    .btn { display:inline-block; background:#e59125; color:#fff !important; text-decoration:none; padding:12px 20px; border-radius:5px; margin:20px 0; font-weight:bold; }
    .footer { background:#f0f0f0; padding:15px; font-size:12px; text-align:center; color:#777; }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <img src="{{logo}}" alt="Logo">
    </div>
    <div class="content">
      <h2>Hi {{fullname}},</h2>
      <p>{{body}}</p>
      {{button}}
    </div>
    <div class="footer">
      Do not reply to this email. If you need help, contact the system administrator.
    </div>
  </div>
</body>
</html>
