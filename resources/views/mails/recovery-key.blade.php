<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Recovery Key</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .container { background-color: #f4f4f4; border-radius: 5px; padding: 20px; }
        .header { background-color: #1e293b; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
        .content { background-color: white; padding: 30px; border-radius: 0 0 5px 5px; }
        .recovery-key { background-color: #f8fafc; border: 2px solid #e2e8f0; padding: 20px; text-align: center; font-size: 24px; font-weight: bold; font-family: monospace; margin: 20px 0; color: #1e293b; letter-spacing: 2px; }
        .warning { color: #dc2626; font-size: 14px; font-weight: bold; margin-top: 20px; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Account Recovery</h1>
        </div>
        <div class="content">
            <p>Hello,</p>
            <p>You have successfully generated a new recovery key for your account. Please keep this key in a safe place. You can use it to access your account if you forget your password and lose access to your email.</p>
            
            <div class="recovery-key">
                {{ $recoveryKey }}
            </div>
            
            <p class="warning">IMPORTANT: Never share this key with anyone. Our support team will never ask for this key.</p>
            
            <p>If you did not request this key, please secure your account immediately by changing your password.</p>
            
            <p>Best regards,<br>{{ config('app.name') }} Team</p>
        </div>
        <div class="footer">
            <p>This is an automated message, please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
