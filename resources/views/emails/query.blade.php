<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Query</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
</head>

<body style="margin: 0px; padding: 0px; background-color: #FCF3F3; font-family: 'Inter', sans-serif;
">
    <div class="div">
        <div>
            <p><b>Full name:</b> {{$data['full_name']}}</p>
            <p><b>Mobile number:</b>{{$data['contact_no']}}</p>
            <p><b>Email id:</b>{{$data['email']}}</p>
            <p><b>Help Option:</b>{{$data['help_option']}}</p>
            <p><b>Description:</b>{{$data['description']}}</p>
            <p>
                <b>File:</b>
                <a href="{{$data['file']}}">{{$data['file']}}</a>
            </p>
        </div>
    </div>
</body>

</html>
