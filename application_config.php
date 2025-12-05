<?php

    define("GOOGLE_CLIENT_ID", getenv("GOOGLE_CLIENT_ID"));
    define("GOOGLE_CLIENT_SECRET", getenv("GOOGLE_CLIENT_SECRET"));
    define("GOOGLE_REDIRECT_URI", getenv("GOOGLE_REDIRECT_URI"));

    define("MAIL_HOST", getenv("MAIL_HOST") ?: "smtp.gmail.com");
    define("MAIL_USERNAME", getenv("MAIL_USERNAME"));
    define("MAIL_APP_PASSWORD", getenv("MAIL_APP_PASSWORD"));
    define("MAIL_SENDER", getenv("MAIL_SENDER"));

    define("VNPAY_TMNCODE", getenv("VNPAY_TMNCODE"));
    define("VNPAY_HASHSECRET", getenv("VNPAY_HASHSECRET"));
    define("VNPAY_RETURNURL", getenv("VNPAY_RETURNURL"));


?>