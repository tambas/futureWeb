<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ config('dofus.title') }}</title>
</head>
<body gcolor="#FFFFFF" marginheight="0" marginwidth="0">
    <table bgcolor="#FFFFFF" style="line-height:10px" width="100%">
        <tbody>
            <tr style="line-height:10px">
                <td align="center" style="line-height:10px">
                    <table border="0" cellpadding="0" cellspacing="0" style="line-height:10px" width="700">
                        <tbody>
                            <tr style="line-height:10px">
                                <td height="187" style="line-height:10px" width="700"><img alt="" border="0" height="187" src="{{ URL::asset('imgs/assets/email_header.jpg') }}" style="display:block" width="700"></td>
                            </tr>
                        </tbody>
                    </table>
                    <table border="0" cellpadding="0" cellspacing="0" style="line-height:10px" width="700">
                        <tbody>
                            <tr style="line-height:10px">
                                <td style="line-height:14px;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:13px;" width="700">@yield('content')<p>Cordialement,</p></td>
                            </tr>
                        </tbody>
                    </table>
                    <table cellpadding="0" cellspacing="0" style="line-height:10px;border-top:1px dotted #ccc" width="700">
                        <tbody>
                            <tr style="line-height:10px">
                                <td height="25" width="700">&nbsp;</td>
                            </tr>
                        </tbody>
                    </table>
                    <table border="0" cellpadding="0" cellspacing="0" style="line-height:10px" width="700">
                        <tbody>
                            <tr style="line-height:10px">
                                <td height="48" style="line-height:10px" width="700"><img alt="" border="0" height="28" src="{{ URL::asset('imgs/azote_text.png') }}" style="display:block" ></td>
                            </tr>
                        </tbody>
                    </table>
                    <table border="0" cellpadding="0" cellspacing="0" style="line-height:10px" width="700">
                        <tbody>
                            <tr style="line-height:10px">
                                <td height="14" style="font-family:Lucida Sans Unicode,Verdana,Arial,Sans-serif;font-size:11px;line-height:10px;color:#aaa" width="700"><span class="lG">{{ config('dofus.title') }}</span> &copy;  {{ date('Y') }}. Tous droits réservés.</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</body>