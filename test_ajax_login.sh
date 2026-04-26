#!/bin/bash
# Clear old cookies
rm -f /tmp/ajax_test.txt /tmp/ajax_get.html /tmp/ajax_post.html

# GET the login page to set session and XSRF cookie
curl -s -c /tmp/ajax_test.txt http://127.0.0.1:8000/login -o /tmp/ajax_get.html

# Extract tokens
XSRF_COOKIE=$(grep XSRF-TOKEN /tmp/ajax_test.txt | awk '{print $7}')
FORM_TOKEN=$(grep -o 'name="_token" value="[^"]*"' /tmp/ajax_get.html | head -1 | sed 's/name="_token" value="//;s/"$//')

echo "=== TOKENS ==="
echo "XSRF-TOKEN cookie: $XSRF_COOKIE"
echo "Form _token field: $FORM_TOKEN"
echo ""

# POST with X-XSRF-TOKEN header
curl -s -c /tmp/ajax_test.txt -b /tmp/ajax_test.txt \
  -X POST http://127.0.0.1:8000/login \
  -H "X-XSRF-TOKEN: $XSRF_COOKIE" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "_token=$FORM_TOKEN&email=admin@smartshamba.com&password=password" \
  -o /tmp/ajax_post.html \
  -w "HTTP Status: %{http_code}\n"

echo ""
echo "=== POST Response (first 5 lines) ==="
head -5 /tmp/ajax_post.html