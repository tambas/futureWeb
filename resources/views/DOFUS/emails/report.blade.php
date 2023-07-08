<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>PHP Error Report</title>
    </head>
    <body>
        <table style="width:100%;" >
            <tr>
                <th colspan="2" align="center" style="border-width:1px;border-style:solid;border-color:#b5b5b5;padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;font-style:normal;font-variant:normal;font-weight:normal;font-size:12px;font-family:Monaco, Consolas, monospace;line-height:normal;background-color:#d4d4d4;" >Azote.us - PHP Error Report</th>
            </tr>
            <tr>
                <td style="border-width:1px;border-style:solid;border-color:#b5b5b5;padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;font-style:normal;font-variant:normal;font-weight:normal;font-size:12px;font-family:Monaco, Consolas, monospace;line-height:normal;" >Date</td>
                <td style="border-width:1px;border-style:solid;border-color:#b5b5b5;padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;font-style:normal;font-variant:normal;font-weight:normal;font-size:12px;font-family:Monaco, Consolas, monospace;line-height:normal;" >{{ $date }}</td>
            </tr>
            <tr>
                <td style="border-width:1px;border-style:solid;border-color:#b5b5b5;padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;font-style:normal;font-variant:normal;font-weight:normal;font-size:12px;font-family:Monaco, Consolas, monospace;line-height:normal;" >Error</td>
                <td style="border-width:1px;border-style:solid;border-color:#b5b5b5;padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;font-style:normal;font-variant:normal;font-weight:normal;font-size:12px;font-family:Monaco, Consolas, monospace;line-height:normal;" >{{ $exception->getMessage() }}</td>
            </tr>
            <tr>
                <td style="border-width:1px;border-style:solid;border-color:#b5b5b5;padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;font-style:normal;font-variant:normal;font-weight:normal;font-size:12px;font-family:Monaco, Consolas, monospace;line-height:normal;" >File</td>
                <td style="border-width:1px;border-style:solid;border-color:#b5b5b5;padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;font-style:normal;font-variant:normal;font-weight:normal;font-size:12px;font-family:Monaco, Consolas, monospace;line-height:normal;" >{{ $exception->getFile() }}:{{ $exception->getLine() }}</td>
            </tr>
            @if (isset($_SERVER['HTTP_REFERER']))
            <tr>
                <td style="border-width:1px;border-style:solid;border-color:#b5b5b5;padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;font-style:normal;font-variant:normal;font-weight:normal;font-size:12px;font-family:Monaco, Consolas, monospace;line-height:normal;" >Referer</td>
                <td style="border-width:1px;border-style:solid;border-color:#b5b5b5;padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;font-style:normal;font-variant:normal;font-weight:normal;font-size:12px;font-family:Monaco, Consolas, monospace;line-height:normal;" >{{ $_SERVER['HTTP_REFERER'] }}</td>
            </tr>
            @endif
            <tr>
                <td style="border-width:1px;border-style:solid;border-color:#b5b5b5;padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;font-style:normal;font-variant:normal;font-weight:normal;font-size:12px;font-family:Monaco, Consolas, monospace;line-height:normal;" >User</td>
                @if ($user)
                <td style="border-width:1px;border-style:solid;border-color:#b5b5b5;padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;font-style:normal;font-variant:normal;font-weight:normal;font-size:12px;font-family:Monaco, Consolas, monospace;line-height:normal;" >#{{ $user->id }} - {{ $user->pseudo }} - {{ $user->email }} - {{ $user->firstname }} {{ $user->lastname }}</td>
                @else
                <td style="border-width:1px;border-style:solid;border-color:#b5b5b5;padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;font-style:normal;font-variant:normal;font-weight:normal;font-size:12px;font-family:Monaco, Consolas, monospace;line-height:normal;" >Guest</td>
                @endif
            </tr>
        </table>
    </body>
</html>
