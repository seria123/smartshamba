<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Read the cookie from the file
$cookieFile = '/tmp/fresh_cookies.txt';
if (! file_exists($cookieFile)) {
    exit("Cookie file not found\n");
}

$cookieLine = trim(shell_exec("grep laravel-session $cookieFile | awk '{print \$7}'"));
if (! $cookieLine) {
    exit("No laravel-session cookie found\n");
}

$encrypted = explode('=', $cookieLine, 2)[1];
$encrypted = urldecode($encrypted);

use Illuminate\Support\Facades\Crypt;

$decrypted = Crypt::decryptString($encrypted);
[$id, $payload] = explode('|', $decrypted, 2);

$data = unserialize(base64_decode($payload));
echo "Session data:\n";
print_r($data);
echo "\nToken in session: ".($data['_token'] ?? 'none')."\n";

// Also check XSRF-TOKEN cookie
$xsrfLine = trim(shell_exec("grep XSRF-TOKEN $cookieFile | awk '{print \$7}'"));
if ($xsrfLine) {
    $xsrf = explode('=', $xsrfLine, 2)[1];
    $xsrf = urldecode($xsrf);
    echo "XSRF-TOKEN cookie: $xsrf\n";
}
