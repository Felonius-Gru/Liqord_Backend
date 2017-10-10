<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <h3>{{ $request }}</h3>
        
        <hr/>
        
        <h2 style="color:#222;;">{!! $error_message !!}</h2>        
        
        <hr/>
        
        <pre style="color:#222; line-height: 22px; font-size: 12px; font-family: sans-serif;">{!! $exception !!}</pre>
    </body>
</html>