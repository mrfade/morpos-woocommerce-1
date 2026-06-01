<?php

if (!defined('ABSPATH')) {
    exit;
}

class MorPOS_API_Client
{
    // Base URLs
    private const SANDBOX_BASE_URL = 'https://finagopay-pf-sale-api-gateway.prp.morpara.com';
    private const PRODUCTION_BASE_URL = 'https://sale-gateway.morpara.com';

    // Endpoints
    private const EP_HOSTED_PAYMENT = '/v1/HostedPayment/HostedPaymentRedirect';
    private const EP_EMBEDDED_PAYMENT = '/v1/EmbeddedPayment/CreatePaymentForm';
    private const EP_CHECK_PAYMENT = '/v1/Payment/CheckPayment';
    private const EP_BIN_CHECK = '/v1/BinList/CheckBin';

    // Scopes
    private const SCOPE_PAYMENT = 'payment';
    private const SCOPE_PF_RW = 'pf_write pf_read';

    private string $base_url;
    private string $client_id;
    private string $client_secret;
    private string $merchant_id;
    private string $submerchant_id;
    private string $api_key;
    private string $environment;

    public function __construct(string $client_id, string $client_secret, string $merchant_id, string $submerchant_id, string $api_key, string $environment = 'production')
    {
        $baseUrl = $environment === 'production'
            ? self::PRODUCTION_BASE_URL
            : self::SANDBOX_BASE_URL;

        $this->environment = $environment;
        $this->base_url = rtrim($baseUrl, '/');
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->merchant_id = $merchant_id;
        $this->submerchant_id = $submerchant_id;
        $this->api_key = $api_key;
    }

    /**
     * Create payment with MorPOS API.
     *
     * @param array $args
     * @return array
     */
    public function create_payment(array $args): array
    {
        if ($this->api_key === '') {
            MorPOS_Logger::error('CreatePayment: blocked, API key is empty in gateway settings');
            return $this->errorResult(__('API Key not found.', 'morpos-for-woocommerce'));
        }

        $conversationId = (string) ($args['conversationId'] ?? morpos_generate_conversation_id());
        $paymentMethod = (string) ($args['paymentMethod'] ?? 'HOSTEDPAYMENT');
        $paymentInstrumentType = (string) ($args['paymentInstrumentType'] ?? 'CARD');
        $language = (string) ($args['language'] ?? 'tr');
        $transactionType = (string) ($args['transactionType'] ?? 'SALE');
        $installmentCount = (int) ($args['installmentCount'] ?? 0);
        $amount = (string) $args['amount'];
        $currencyCode = (string) $args['currencyCode'];
        $returnUrl = (string) $args['returnUrl'];
        $failUrl = (string) $args['failUrl'];

        $path = $paymentMethod === 'HOSTEDPAYMENT'
            ? self::EP_HOSTED_PAYMENT
            : self::EP_EMBEDDED_PAYMENT;

        $vftFlag = false;
        $sign = morpos_sign([
            $conversationId,
            $this->merchant_id,
            $returnUrl,
            $failUrl,
            $paymentMethod,
            $language,
            $paymentInstrumentType,
            $transactionType,
            $vftFlag ? 'True' : 'False',
            $installmentCount,
            $amount,
            $currencyCode,
            $this->submerchant_id,
            $this->api_key,
        ]);

        $payload = [
            'merchantId' => $this->merchant_id,
            'returnUrl' => $returnUrl,
            'failUrl' => $failUrl,
            'paymentMethod' => $paymentMethod,
            'paymentInstrumentType' => $paymentInstrumentType,
            'language' => $language,
            'conversationId' => $conversationId,
            'sign' => $sign,
            'transactionDetails' => [
                'transactionType' => $transactionType,
                'installmentCount' => $installmentCount,
                'amount' => $amount,
                'currencyCode' => $currencyCode,
                'vftFlag' => $vftFlag,
            ],
            'extraParameter' => [
                'pFSubMerchantId' => $this->submerchant_id,
            ],
        ];

        return $this->post_json($path, $payload, self::SCOPE_PAYMENT, 'CreatePayment');
    }

    /**
     * Check payment status with MorPOS API.
     *
     * @param array $args
     * @return array
     */
    public function check_payment(array $args = []): array
    {
        if ($this->api_key === '') {
            MorPOS_Logger::error('CheckPayment: blocked, API key is empty in gateway settings');
            return $this->errorResult(__('API Key not found.', 'morpos-for-woocommerce'));
        }

        $conversationId = $args['conversationId'];
        if (!$conversationId) {
            MorPOS_Logger::error('CheckPayment: blocked, conversation ID is missing');
            return $this->errorResult(__('Conversation ID is required.', 'morpos-for-woocommerce'));
        }

        $sign = morpos_sign([
            $conversationId,
            $this->merchant_id,
            $this->api_key,
        ]);

        $payload = [
            'conversationId' => $conversationId,
            'merchantId' => $this->merchant_id,
            'sign' => $sign,
        ];

        return $this->post_json(self::EP_CHECK_PAYMENT, $payload, self::SCOPE_PF_RW, 'CheckPayment');
    }

    /**
     * Small connectivity check to validate credentials.
     *
     * @param array $args
     * @return array
     */
    public function make_test_connection(array $args = []): array
    {
        if ($this->api_key === '') {
            MorPOS_Logger::error('TestConnection: blocked, API key is empty');
            return $this->errorResult(__('API Key not found.', 'morpos-for-woocommerce'));
        }

        $conversationId = (string) ($args['conversationId'] ?? morpos_generate_conversation_id());
        $bin = (string) ($args['bin'] ?? '402940');
        $language = (string) ($args['language'] ?? 'tr');

        $sign = morpos_sign([
            $conversationId,
            $this->merchant_id,
            $language,
            $bin,
            $this->api_key,
        ]);

        $payload = [
            'bin' => $bin,
            'language' => $language,
            'conversationId' => $conversationId,
            'merchantId' => $this->merchant_id,
            'sign' => $sign,
        ];

        return $this->post_json(self::EP_BIN_CHECK, $payload, self::SCOPE_PF_RW, 'TestConnection');
    }

    /**
     * Sends a POST request with JSON payload to the specified path.
     *
     * @param string $path
     * @param array $payload
     * @param string $scope
     * @param string $logPrefix
     * @return array
     */
    private function post_json(string $path, array $payload, string $scope, string $logPrefix): array
    {
        $endpoint = $this->base_url . $path;
        $timestamp = $this->current_timestamp_str();
        $headers = $this->build_headers($timestamp, $scope);

        $httpArgs = [
            'headers' => $headers,
            'body' => wp_json_encode($payload),
            'timeout' => 45,
            'sslverify' => $this->environment !== 'sandbox',
        ];

        MorPOS_Logger::debug($logPrefix . ': request sent', [
            'endpoint' => $endpoint,
            'body' => $httpArgs['body'],
        ]);

        $response = wp_remote_post($endpoint, $httpArgs);

        if (is_wp_error($response)) {
            // Network-level failure: timeout, SSL/TLS handshake, DNS resolution, blocked outbound connection, etc.
            MorPOS_Logger::error($logPrefix . ': HTTP request failed', [
                'endpoint' => $endpoint,
                'error' => $response->get_error_message(),
            ]);
            return [
                'ok' => false,
                'error' => $response->get_error_message(),
                'http' => null,
                'body' => null,
            ];
        }

        $code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);

        MorPOS_Logger::debug($logPrefix . ': response received', [
            'http' => $code,
            'body' => $body,
        ]);

        $data = $this->json_decode_assoc($body);

        if ($this->is_http_success($code) && is_array($data)) {
            return ['ok' => true, 'http' => $code, 'data' => $data];
        }

        MorPOS_Logger::error($logPrefix . ': request rejected', [
            'http' => $code,
            'body' => $body,
        ]);

        return [
            'ok' => false,
            'http' => $code,
            'body' => $body,
            'message' => $data['Message'] ?? null,
        ];
    }

    /**
     * Builds the headers for the API request.
     *
     * @param string $timestamp
     * @param string $scope
     * @return array
     */
    private function build_headers(string $timestamp, string $scope): array
    {
        return [
            'Content-Type' => 'application/json',
            'X-ClientSecret' => $this->encode_hash($this->client_secret, $timestamp),
            'X-ClientId' => $this->client_id,
            'X-Timestamp' => $timestamp,
            'X-GrantType' => 'client_credentials',
            'X-Scope' => $scope,
        ];
    }

    /**
     * Checks if the HTTP status code indicates success (2xx).
     *
     * @param int $code
     * @return bool
     */
    private function is_http_success(int $code): bool
    {
        return $code >= 200 && $code < 300;
    }

    /**
     * Decodes JSON string into associative array.
     *
     * @param string $json
     * @return array|null
     */
    private function json_decode_assoc(string $json): ?array
    {
        $data = json_decode($json, true);
        return is_array($data) ? $data : null;
    }

    /**
     * Returns the current timestamp in "YmdHis" format.
     *
     * @return string
     */
    protected function current_timestamp_str(): string
    {
        return gmdate('YmdHis');
    }

    /**
     * Encodes the client secret and timestamp using SHA-256 and Base64 encoding.
     *
     * @param string $clientSecret
     * @param string $timestamp
     * @return string
     */
    protected function encode_hash(string $clientSecret, string $timestamp): string
    {
        $decoded = base64_decode($clientSecret, true);
        if ($decoded === false) {
            $decoded = '';
        }

        $shaHex = hash('sha256', $decoded . $timestamp);
        return base64_encode($shaHex);
    }

    /**
     * Returns an error result.
     *
     * @param string $message
     * @return array
     */
    private function errorResult(string $message): array
    {
        return ['ok' => false, 'error' => $message, 'http' => null, 'body' => null];
    }
}
