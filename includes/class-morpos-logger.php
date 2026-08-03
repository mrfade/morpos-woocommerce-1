<?php

if (!defined('ABSPATH')) {
    exit;
}

class MorPOS_Logger
{
    /** Log source name for the WooCommerce logger (WooCommerce → Status → Logs) */
    private const SOURCE = 'morpos';

    /** Replacement string for redacted values */
    private const MASK = '***';

    /**
     * Field/context key names whose values must never appear in logs.
     * Compared after normalization (lowercase, dashes/underscores removed),
     * so this list covers api_key / apiKey / API-Key etc.
     */
    private const REDACT_KEYS = [
        'sign',            // request/callback signature, derived from the API key
        'apikey',          // api_key / apiKey
        'clientsecret',    // client_secret / clientSecret
        'xclientsecret',   // X-ClientSecret header
        'password',
        'authorization',
        // Defense in depth — card data never flows through this plugin, but mask it if it ever shows up
        'pan',
        'cardnumber',
        'cvv',
        'cvc',
    ];

    /**
     * Write a log entry.
     *
     * Uses the WooCommerce logger when available (visible under
     * WooCommerce → Status → Logs, source "morpos"), falling back to
     * error_log() when WP_DEBUG is enabled.
     *
     * Entries follow a single template:
     *   {Operation}: {event} | key=value key=value ...
     *
     * @param string $msg     Event description, e.g. "CreatePayment: blocked, API key is empty".
     * @param string $level   One of: debug, info, notice, warning, error, critical, alert, emergency.
     * @param array  $context Key/value pairs appended as "key=value" for grep-friendly output.
     */
    public static function log($msg, $level = 'info', array $context = [])
    {
        $line = self::format($msg, $context);

        if (function_exists('wc_get_logger')) {
            wc_get_logger()->log($level, $line, ['source' => self::SOURCE]);
            return;
        }

        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('[' . gmdate('c') . "] [$level] $line");
        }
    }

    /**
     * Log a debug message (verbose details, e.g. API request/response bodies).
     *
     * @param string $msg
     * @param array  $context
     */
    public static function debug($msg, array $context = [])
    {
        self::log($msg, 'debug', $context);
    }

    /**
     * Log an informational message.
     *
     * @param string $msg
     * @param array  $context
     */
    public static function info($msg, array $context = [])
    {
        self::log($msg, 'info', $context);
    }

    /**
     * Log a warning (unexpected but recoverable situation).
     *
     * @param string $msg
     * @param array  $context
     */
    public static function warning($msg, array $context = [])
    {
        self::log($msg, 'warning', $context);
    }

    /**
     * Log an error (operation failed).
     *
     * @param string $msg
     * @param array  $context
     */
    public static function error($msg, array $context = [])
    {
        self::log($msg, 'error', $context);
    }

    /**
     * Render "{event} | key=value key=value" from an event string and context pairs.
     * Sensitive values (API key, secrets, signatures) are redacted.
     *
     * @param string $event
     * @param array  $context
     * @return string
     */
    private static function format(string $event, array $context): string
    {
        $event = self::redact_string($event);

        if (empty($context)) {
            return $event;
        }

        $pairs = [];
        foreach ($context as $key => $value) {
            if (!is_scalar($value)) {
                $value = wp_json_encode($value);
            }

            $value = (string) $value;

            if (self::is_sensitive_key((string) $key)) {
                $value = self::MASK;
            } else {
                $value = self::redact_string($value);
            }

            $pairs[] = $key . '=' . ($value === '' ? '-' : $value);
        }

        return $event . ' | ' . implode(' ', $pairs);
    }

    /**
     * Whether a context key holds a sensitive value.
     *
     * @param string $key
     * @return bool
     */
    private static function is_sensitive_key(string $key): bool
    {
        $normalized = strtolower(str_replace(['-', '_'], '', $key));
        return in_array($normalized, self::REDACT_KEYS, true);
    }

    /**
     * Redact sensitive fields embedded inside a string.
     *
     * Covers two formats only: JSON pairs ({"sign":"ABC..."} → {"sign":"***"})
     * and bare key=value pairs as found in query strings or form bodies
     * (api_key=ABC → api_key=***). Values logged in any other format are NOT
     * caught here and must be filtered through is_sensitive_key() before they
     * reach a log line.
     *
     * @param string $text
     * @return string
     */
    private static function redact_string(string $text): string
    {
        static $jsonPattern = null;
        static $pairPattern = null;
        if ($jsonPattern === null) {
            // sign, api_key/apiKey, client_secret/clientSecret, X-ClientSecret, password, authorization, card data
            $fields = 'sign|api[_-]?key|(?:x-?)?client[_-]?secret|password|authorization|pan|card[_-]?number|cvv|cvc';
            $jsonPattern = '/"(' . $fields . ')"(\s*:\s*)"(?:[^"\\\\]|\\\\.)*"/i';
            $pairPattern = '/\b(' . $fields . ')(=)[^&\s"\']+/i';
        }

        $text = (string) preg_replace($jsonPattern, '"$1"$2"' . self::MASK . '"', $text);
        return (string) preg_replace($pairPattern, '$1$2' . self::MASK, $text);
    }
}
