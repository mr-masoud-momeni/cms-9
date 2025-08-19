<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>درگاه پرداخت</title></head>
<body style="text-align:center; padding:50px; font-family:tahoma">
<h2>پرداخت {{ number_format($amount) }} تومان</h2>
<a href="{{ $callback_url }}?authority={{ $authority }}&status=OK"
   style="padding:10px 20px; background:green; color:white;">
    پرداخت موفق
</a>
</body>
</html>
