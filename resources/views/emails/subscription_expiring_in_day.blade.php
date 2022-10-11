<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
</head>

<body style="margin: 0px; padding: 0px; background-color: #FCF3F3; font-family: 'Inter', sans-serif;
">
    <table cellpadding="0" cellspacing="0" width="600" style="margin: 0 auto;">
        <tr>
            <td>
                <table width="100%" cellpadding="0" cellspacing="0"
                    style="margin: 0 auto; background: url('{{ asset('frontend/email-images/email-bg.png') }}');  background-size: 100%;">
                    <tr>
                        <td
                            style="height: 4px; background: linear-gradient(90deg, #FA4512 0%, #FFE814 26.04%, #DFFB04 49.48%, #71D1F9 71.36%, #5F9FFF 100%);">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table width="90%"  cellpadding="0" cellspacing="0" style="margin: 0 auto;">
                                <tr style="height: 32px;"></tr>
                                <tr>
                                    <td><img style="max-width: 190px;" src="{{ asset('frontend/email-images/logo.png') }}" alt="logo"></td>
                                </tr>
                                <tr style="height: 32px;"></tr>
                                <tr>
                                    <td>
                                        <h1
                                            style="margin: 0px; font-weight: 700; font-size: 16px; line-height: 22px; letter-spacing: 0.006em; color: #170A52;">
                                            Hi Hello @if($data['userTransEn']){{ $data['userTransEn']['full_name'] }}@endif,</h1>
                                    </td>
                                </tr>
                                <tr style="height: 32px;"></tr>
                                <tr>
                                    <td>
                                        <p style="margin: 0px; font-weight: 400; letter-spacing: 0.006em;
                                font-size: 16px; line-height: 22px; color: #645A8F;">Hey beautiful! Only 24 hrs of exclusive experiences remain at Hi Hello as your subscription comes to an end. Continue enjoying the sweet experiences and matches that you are having with the Hi Hello community by subscribing soon.  
                                        </p>
                                    </td>
                                </tr>
                                <tr style="height: 32px;"></tr>
                                <tr style="height: 32px;"></tr>
                                <tr>
                                    <td>
                                        <table width="100%" cellpadding="0" cellspacing="0"
                                            style="
                                        text-align: center; background-position: center; background: url('{{ asset('frontend/email-images/action-section-bg.png') }}'); background-repeat: no-repeat; background-size: cover ; border-radius: 18px;">
                                            <tr style="height: 30px;"></tr>
                                            <tr>
                                                <td
                                                    style="text-align: center; font-weight: 600; font-size: 16px; line-height: 23px; color: #250A52;">
                                                    Click Here to <br> Subscribe.</td>
                                            </tr>
                                            <tr style="height: 20px;"></tr>
                                            <tr>
                                                <td style="text-align: center;"><a href="#"
                                                        style="padding: 16px 20px; font-weight: 700; font-size: 14px; line-height: 17px; display: inline-block; letter-spacing: 0.032em; text-transform: uppercase;  color: #FFFFFF; text-decoration: none; background: #250A52; border-radius: 12px; width: 184px;">Subscribe Soon</a></td>
                                            </tr>
                                            <tr style="height: 30px;"></tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr style="height: 30px;"></tr>
                                <tr>
                                    <td>
                                        <p style="margin: 0px; font-weight: 400; font-size: 14px; line-height: 20px; color: #645A8F; letter-spacing: 0.006em;
                                    ">Until you subscribe, you will be unable to access and fully use the Hi
                                            Hello App. We will begin sending out reminders after a period of time.</p>
                                    </td>
                                </tr>
                                <tr style="height: 30px;"></tr>
                                <td>
                                    <p style="margin: 0px; font-weight: 400; font-size: 14px; line-height: 20px; color: #645A8F; letter-spacing: 0.006em;
                                ">Regards,</p>
                                </td>
                                <tr>
                                    <td>
                                        <h1
                                            style="margin: 0px; font-weight: 700; font-size: 16px; line-height: 22px; letter-spacing: 0.006em; color: #170A52;">
                                            Team Hi Hello</h1>
                                    </td>
                                </tr>
                                <tr style="height: 50px;"></tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td
                            style="height: 4px; background: linear-gradient(90deg, #FA4512 0%, #FFE814 26.04%, #DFFB04 49.48%, #71D1F9 71.36%, #5F9FFF 100%);">
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr style="height: 48px;"></tr>
        <tr>
            <td style="font-weight: 400; text-align: center; font-size: 10px; line-height: 15px; color: #C6A9A9;">if you wish to Unsubscribe <a href="" style="color: #C6A9A9;">click here</a></td>
        </tr>
        <tr style="height: 56px;"></tr>
    </table>
    <table class="gmail-app-fix">
        <tr>
            <td>
                <table cellpadding="0" cellspacing="0" border="0" align="center" width="600">
                    <tr>
                        <td cellpadding="0" cellspacing="0" border="0" height="1"; style="line-height: 1px; min-width: 200px;">
                         </td>
                        <td cellpadding="0" cellspacing="0" border="0" height="1"; style="line-height: 1px; min-width: 200px;">                        
                        </td>
                        <td cellpadding="0" cellspacing="0" border="0" height="1"; style="line-height: 1px; min-width: 200px;">                      
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>