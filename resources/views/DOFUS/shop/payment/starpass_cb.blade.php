<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Paiement CB</title>
</head>
<body>
    <form id="payment" action="https://script.starpass.fr/kit3/submit_paiement.php" method="post">
        <input type="hidden" name="skidd" value="{{ $idd }}">
        <input type="hidden" name="sk-code-quantity" value="1">
        <input type="hidden" name="sk-access-type" value="cb">
        <input type="hidden" name="sk-customer-first-name" value="{{ Auth::user()->firstname }}">
        <input type="hidden" name="sk-customer-last-name" value="{{ Auth::user()->lastname }}">
        <input type="hidden" name="sk-customer-email" value="{{ Auth::user()->email }}">
        <input type="hidden" name="sk-customer-country" value="fr">
        <input type="hidden" name="sk-action-language" value="fr">
    </form>
    <script>document.forms["payment"].submit();</script>
</body>
</html>
