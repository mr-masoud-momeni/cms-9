<form method="POST" action="{{ $gatewayUrl }}">
    <input type="hidden" name="RefId" value="{{ $refId }}">
</form>
<script>document.forms[0].submit();</script>
