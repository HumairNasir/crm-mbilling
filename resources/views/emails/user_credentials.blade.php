<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .header { text-align: center; border-bottom: 2px solid #eee; padding-bottom: 20px; margin-bottom: 20px; }
        .header h2 { color: #333; margin: 0; }
        .content { color: #555; line-height: 1.6; }
        .credentials-box { background: #f9f9f9; border: 1px solid #ddd; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .credentials-row { display: flex; justify-content: space-between; margin-bottom: 10px; border-bottom: 1px solid #eee; padding-bottom: 5px; }
        .credentials-row:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
        .label { font-weight: bold; color: #333; }
        .value { color: #007bff; font-weight: 600; }
        .footer { font-size: 12px; color: #999; text-align: center; margin-top: 30px; border-top: 1px solid #eee; padding-top: 10px; }
        .btn { display: inline-block; background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Welcome to Kriss CRM</h2>
        </div>
        
        <div class="content">
            <p>Hello <strong>{{ $data['name'] }}</strong>,</p>
            <p>Your account has been successfully created. Below are your login credentials and assignment details.</p>

            <div class="credentials-box">
                <div class="credentials-row">
                    <span class="label">Role:</span>
                    <span class="value">{{ $data['role'] }}</span>
                </div>
                
                @if(!empty($data['region']))
                <div class="credentials-row">
                    <span class="label">Region:</span>
                    <span class="value">{{ $data['region'] }}</span>
                </div>
                @endif

                @if(!empty($data['state']))
                <div class="credentials-row">
                    <span class="label">Area (State):</span>
                    <span class="value">{{ $data['state'] }}</span>
                </div>
                @endif

                <div class="credentials-row">
                    <span class="label">Email:</span>
                    <span class="value">{{ $data['email'] }}</span>
                </div>
                <div class="credentials-row">
                    <span class="label">Password:</span>
                    <span class="value">{{ $data['password'] }}</span>
                </div>
            </div>

            <div style="text-align: center;">
                <a href="{{ url('/login') }}" class="btn">Login to Dashboard</a>
            </div>
            
            <p style="margin-top: 20px;">For security reasons, please change your password after your first login.</p>
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} Kriss CRM. All rights reserved.
        </div>
    </div>
</body>
</html>