<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Securion Pay</title>
</head>

<body>
    @php
        $checkoutRequest = [
            'charge' => [
                'amount' => (int) round($txnInfo->final_amount * 100),
                'currency' => $txnInfo->pay_currency,
                'metadata' => [
                    'txn' => $txnInfo->tnx,
                    'amount' => $txnInfo->final_amount,
                ],
            ],
        ];

        $json = json_encode($checkoutRequest);

        // Sign it with HMAC SHA256 using the secret key
        $securionGateway = gateway_info('securionpay');
        $publicKey = data_get($securionGateway, 'public_key');
        $secretKey = data_get($securionGateway, 'secret_key');
        $signature = hash_hmac('sha256', $json, $secretKey);
        $signedRequest = base64_encode($signature . '|' . $json);
    @endphp
    <form action="{{ route('ipn.non-hosted.securionpay') }}" method="POST">
        @csrf

        <input type="hidden" name="transaction_id" value="{{ $txnInfo->tnx }}">
        <script src="https://dev.shift4.com/checkout.js" class="shift4-button" data-key="{{ $publicKey }}"
            data-checkout-request="{{ $signedRequest }}" data-name="{{ setting('site_title') }}"
            data-description="{{ $txnInfo->description }}" data-checkout-button="Pay Now"></script>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btn = document.querySelector('button');
            if (btn) {
                btn.click();
                btn.setAttribute('hidden', '');
            }
        });
    </script>
</body>

</html>
