<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifica tu cuenta</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f4f7f9; margin: 0; padding: 0; }
        .wrapper { max-width: 560px; margin: 40px auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #4ECDC4 0%, #3BAFA7 100%); padding: 36px 40px; text-align: center; }
        .header img { height: 48px; margin-bottom: 12px; }
        .header h1 { color: white; margin: 0; font-size: 22px; font-weight: 800; letter-spacing: 0.05em; }
        .body { padding: 40px; }
        .greeting { font-size: 16px; color: #333; margin-bottom: 16px; }
        .info { font-size: 15px; color: #555; line-height: 1.6; margin-bottom: 28px; }
        .code-box { background: #f0fffe; border: 2px solid #4ECDC4; border-radius: 14px; padding: 24px; text-align: center; margin-bottom: 28px; }
        .code-label { font-size: 12px; color: #3BAFA7; font-weight: 700; text-transform: uppercase; letter-spacing: 0.12em; margin-bottom: 10px; }
        .code { font-size: 42px; font-weight: 900; letter-spacing: 12px; color: #1a1a1a; font-family: 'Courier New', monospace; }
        .expiry { font-size: 13px; color: #888; margin-top: 10px; }
        .divider { border: none; border-top: 1px solid #eee; margin: 28px 0; }
        .note { font-size: 13px; color: #aaa; line-height: 1.6; }
        .footer { background: #f9f9f9; padding: 20px 40px; text-align: center; font-size: 12px; color: #bbb; border-top: 1px solid #eee; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>💚 HEART TO HEART</h1>
        </div>
        <div class="body">
            <p class="greeting">Hola, bienvenido/a a Heart to Heart.</p>
            <p class="info">Para activar tu cuenta, introduce el siguiente código de verificación en la página de confirmación. El código es válido durante <strong>10 minutos</strong>.</p>

            <div class="code-box">
                <div class="code-label">Tu código de verificación</div>
                <div class="code">{{ $code }}</div>
                <div class="expiry">Expira en 10 minutos</div>
            </div>

            <hr class="divider">

            <p class="note">Si no has creado una cuenta en Heart to Heart, puedes ignorar este correo de forma segura. Nadie puede acceder a tu cuenta sin este código.</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Heart to Heart &mdash; Tu espacio de bienestar emocional
        </div>
    </div>
</body>
</html>
