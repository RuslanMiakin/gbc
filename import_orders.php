<?php

declare(strict_types=1);

/**
 * RetailCRM orders import script for cron usage.
 *
 * Usage:
 *   php import_orders.php
 *
 * Config in code:
 *   RETAILCRM_BASE_URL, RETAILCRM_API_KEY, RETAILCRM_SITE
 *   ORDERS_JSON_PATH=W:\gbc-tz\mock_orders.json
 *   LOG_PATH=W:\gbc-tz\import_orders.log
 */

const DEFAULT_ORDERS_JSON_PATH = __DIR__ . DIRECTORY_SEPARATOR . 'mock_orders.json';
const DEFAULT_LOG_PATH = __DIR__ . DIRECTORY_SEPARATOR . 'import_orders.log';
const API_VERSION_PATH = '/api/v5';
const RETAILCRM_BASE_URL = '';
const RETAILCRM_API_KEY = '';
const RETAILCRM_SITE = '';
const RETAILCRM_CUSTOM_FIELD_UTM_SOURCE = 'utm_source';
const RETAILCRM_CUSTOM_FIELD_ORDER_TYPE = 'order_type';

main();

function main(): void
{
    $baseUrl = rtrim(RETAILCRM_BASE_URL, '/');
    $apiKey = RETAILCRM_API_KEY;
    $site = RETAILCRM_SITE;
    $ordersJsonPath = (string) (getenv('ORDERS_JSON_PATH') ?: DEFAULT_ORDERS_JSON_PATH);
    $logPath = (string) (getenv('LOG_PATH') ?: DEFAULT_LOG_PATH);

    $orders = readOrders($ordersJsonPath);
    $stats = ['created' => 0, 'skipped' => 0, 'failed' => 0];

    foreach ($orders as $index => $sourceOrder) {
        try {
            validateOrder($sourceOrder, $index);
            $externalId = buildOrderExternalId($sourceOrder);

            if (orderExists($baseUrl, $apiKey, $externalId)) {
                $stats['skipped']++;
                logLine($logPath, sprintf('[SKIP] #%d externalId=%s already exists', $index, $externalId));
                continue;
            }

            $payload = transformOrderToRetailCrm($sourceOrder, $site, $externalId);
            $response = createOrder($baseUrl, $apiKey, $site, $payload);

            if (($response['success'] ?? false) === true) {
                $stats['created']++;
                $createdId = (string) ($response['id'] ?? 'n/a');
                logLine($logPath, sprintf('[CREATE] #%d externalId=%s id=%s', $index, $externalId, $createdId));
            } else {
                $stats['failed']++;
                logLine($logPath, sprintf('[FAIL] #%d externalId=%s response=%s', $index, $externalId, json_encode($response, JSON_UNESCAPED_UNICODE)));
            }
        } catch (Throwable $e) {
            $stats['failed']++;
            logLine($logPath, sprintf('[ERROR] #%d %s', $index, $e->getMessage()));
        }
    }

    logLine(
        $logPath,
        sprintf(
            '[SUMMARY] created=%d skipped=%d failed=%d total=%d',
            $stats['created'],
            $stats['skipped'],
            $stats['failed'],
            count($orders)
        )
    );
}

/**
 * @return array<int, array<string, mixed>>
 */
function readOrders(string $path): array
{
    if (!is_file($path)) {
        throw new RuntimeException('Orders file not found: ' . $path);
    }

    $contents = file_get_contents($path);
    if ($contents === false) {
        throw new RuntimeException('Unable to read file: ' . $path);
    }

    $decoded = json_decode($contents, true);
    if (!is_array($decoded)) {
        throw new RuntimeException('Orders JSON must be an array');
    }

    return $decoded;
}

/**
 * @param array<string, mixed> $order
 */
function validateOrder(array $order, int $index): void
{
    $required = ['firstName', 'lastName', 'phone', 'orderType', 'orderMethod', 'status', 'items'];

    foreach ($required as $key) {
        if (!array_key_exists($key, $order)) {
            throw new RuntimeException(sprintf('Order #%d missing required field: %s', $index, $key));
        }
    }

    if (!is_array($order['items']) || count($order['items']) === 0) {
        throw new RuntimeException(sprintf('Order #%d has empty items', $index));
    }
}

/**
 * @param array<string, mixed> $order
 */
function buildOrderExternalId(array $order): string
{
    $signatureData = [
        'phone' => normalizePhone((string) ($order['phone'] ?? '')),
        'email' => mb_strtolower(trim((string) ($order['email'] ?? ''))),
        'firstName' => trim((string) ($order['firstName'] ?? '')),
        'lastName' => trim((string) ($order['lastName'] ?? '')),
        'items' => normalizeItemsForHash($order['items'] ?? []),
        'deliveryAddress' => trim((string) ($order['delivery']['address']['text'] ?? '')),
    ];

    return 'mock-' . hash('sha256', json_encode($signatureData, JSON_UNESCAPED_UNICODE));
}

/**
 * @param mixed $items
 * @return array<int, array<string, mixed>>
 */
function normalizeItemsForHash($items): array
{
    if (!is_array($items)) {
        return [];
    }

    $normalized = [];
    foreach ($items as $item) {
        if (!is_array($item)) {
            continue;
        }
        $normalized[] = [
            'productName' => trim((string) ($item['productName'] ?? '')),
            'quantity' => (float) ($item['quantity'] ?? 0),
            'initialPrice' => (float) ($item['initialPrice'] ?? 0),
        ];
    }

    usort(
        $normalized,
        static fn(array $a, array $b): int => strcmp(
            $a['productName'] . '|' . $a['quantity'] . '|' . $a['initialPrice'],
            $b['productName'] . '|' . $b['quantity'] . '|' . $b['initialPrice']
        )
    );

    return $normalized;
}

function normalizePhone(string $phone): string
{
    return preg_replace('/\D+/', '', $phone) ?? $phone;
}

function orderExists(string $baseUrl, string $apiKey, string $externalId): bool
{
    $url = $baseUrl . API_VERSION_PATH . '/orders';
    $query = http_build_query(
        [
            'apiKey' => $apiKey,
            'limit' => 20,
            'filter' => [
                'externalIds' => [$externalId],
            ],
        ],
        '',
        '&',
        PHP_QUERY_RFC3986
    );

    $response = httpRequest('GET', $url . '?' . $query);
    if (!isset($response['success']) || $response['success'] !== true) {
        throw new RuntimeException('Failed to check duplicates by externalId=' . $externalId);
    }

    return isset($response['orders']) && is_array($response['orders']) && count($response['orders']) > 0;
}

/**
 * @param array<string, mixed> $sourceOrder
 * @return array<string, mixed>
 */
function transformOrderToRetailCrm(array $sourceOrder, string $site, string $externalId): array
{
    $orderItems = [];
    foreach ($sourceOrder['items'] as $item) {
        if (!is_array($item)) {
            continue;
        }

        $orderItems[] = [
            'productName' => (string) ($item['productName'] ?? 'Unknown product'),
            'quantity' => (float) ($item['quantity'] ?? 1),
            'initialPrice' => (float) ($item['initialPrice'] ?? 0),
        ];
    }

    $order = [
        'externalId' => $externalId,
        'firstName' => (string) ($sourceOrder['firstName'] ?? ''),
        'lastName' => (string) ($sourceOrder['lastName'] ?? ''),
        'phone' => (string) ($sourceOrder['phone'] ?? ''),
        'email' => (string) ($sourceOrder['email'] ?? ''),
        'orderType' => (string) ($sourceOrder['orderType'] ?? ''),
        'orderMethod' => (string) ($sourceOrder['orderMethod'] ?? ''),
        'status' => (string) ($sourceOrder['status'] ?? ''),
        'items' => $orderItems,
        'customer' => [
            'firstName' => (string) ($sourceOrder['firstName'] ?? ''),
            'lastName' => (string) ($sourceOrder['lastName'] ?? ''),
            'phones' => [
                ['number' => (string) ($sourceOrder['phone'] ?? '')],
            ],
            'email' => (string) ($sourceOrder['email'] ?? ''),
            'site' => $site,
        ],
    ];

    if (isset($sourceOrder['delivery']) && is_array($sourceOrder['delivery'])) {
        $order['delivery'] = $sourceOrder['delivery'];
    }

    $customFields = [];
    if (isset($sourceOrder['customFields']) && is_array($sourceOrder['customFields'])) {
        $customFields = $sourceOrder['customFields'];
    }

    // Keep custom UTM value aligned with RetailCRM custom field code.
    if (isset($sourceOrder['customFields']['utm_source'])) {
        $customFields[RETAILCRM_CUSTOM_FIELD_UTM_SOURCE] = (string) $sourceOrder['customFields']['utm_source'];
    }

    // Save order type into custom field as requested (in addition to standard orderType).
    if (isset($sourceOrder['orderType']) && $sourceOrder['orderType'] !== '') {
        $customFields[RETAILCRM_CUSTOM_FIELD_ORDER_TYPE] = (string) $sourceOrder['orderType'];
    }

    if ($customFields !== []) {
        $order['customFields'] = $customFields;
    }

    return $order;
}

/**
 * @param array<string, mixed> $order
 * @return array<string, mixed>
 */
function createOrder(string $baseUrl, string $apiKey, string $site, array $order): array
{
    $url = $baseUrl . API_VERSION_PATH . '/orders/create';
    $postFields = http_build_query(
        [
            'apiKey' => $apiKey,
            'site' => $site,
            'order' => json_encode($order, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ],
        '',
        '&',
        PHP_QUERY_RFC3986
    );

    return httpRequest('POST', $url, $postFields, ['Content-Type: application/x-www-form-urlencoded']);
}

/**
 * @return array<string, mixed>
 */
function httpRequest(string $method, string $url, ?string $body = null, array $headers = []): array
{
    $ch = curl_init($url);
    if ($ch === false) {
        throw new RuntimeException('Cannot initialize curl');
    }

    $opts = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_CONNECTTIMEOUT => 15,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTPHEADER => $headers,
    ];

    if ($body !== null) {
        $opts[CURLOPT_POSTFIELDS] = $body;
    }

    curl_setopt_array($ch, $opts);
    $responseRaw = curl_exec($ch);
    $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($responseRaw === false) {
        throw new RuntimeException('HTTP request failed: ' . $curlError);
    }

    $decoded = json_decode($responseRaw, true);
    if (!is_array($decoded)) {
        throw new RuntimeException('Invalid JSON response, HTTP ' . $httpCode . ', body: ' . $responseRaw);
    }

    if ($httpCode >= 400) {
        throw new RuntimeException('HTTP ' . $httpCode . ': ' . json_encode($decoded, JSON_UNESCAPED_UNICODE));
    }

    return $decoded;
}

function logLine(string $logPath, string $message): void
{
    $line = sprintf("[%s] %s\n", date('Y-m-d H:i:s'), $message);
    file_put_contents($logPath, $line, FILE_APPEND);
}
