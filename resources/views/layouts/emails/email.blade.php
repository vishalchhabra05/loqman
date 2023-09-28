<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Email</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body style="background-color:#E1E1E1; font-family: arial">

        <div class="container">
            <div class="email-page" align="center" style="max-width:600px; background-color:#FFFFFF; margin:20px auto;box-shadow:0 0 50px 15px rgba(0,0,0,0.1); padding-bottom:40px; min-height:621px; position:relative">
                <div style="margin-top: 3px; border-bottom:1px solid #557d87;padding: 0px 0;background-color: #253d49;">
                    <img src="{{ asset('images/local-tips.png') }}" height="50">
                </div>
                @yield('content')




                <p style="position:absolute; bottom:0px; left:0;right:0; background: #253d49; margin-bottom: 0;padding: 10px 0; color: white;">&copy; {!! date('Y') !!} All Right Reserved</p>
            </div>
        </div>

    </body>
</html>
