<?php

declare(strict_types=1);

/**
 * Import orders from RetailCRM to Supabase table public.rcrm_orders.
 *
 * Usage:
 *   php import_retailcrm_to_supabase.php              — полный импорт в Supabase
 *   php import_retailcrm_to_supabase.php --dry-run  — только список полей и пример строки (без записи)
 *
 * Fill constants below and run script.
 */

const RETAILCRM_BASE_URL = 'https://aliniym.retailcrm.ru';
const RETAILCRM_API_KEY = 'P19fgkeVHqavN7MO3sofGu4xhMy9ADSv';
const API_VERSION_PATH = '/api/v5';

// Set your Supabase project values.
const SUPABASE_URL = 'https://bhcetloppklzmqsxxzxu.supabase.co';
const SUPABASE_SERVICE_ROLE_KEY = 'sb_publishable_P4wHtcgw1-NUQaeQcOjS-Q_jybs0Sn4';
const SUPABASE_TABLE = 'rcrm_orders';

const RETAILCRM_PAGE_LIMIT = 100;
const SUPABASE_INSERT_CHUNK = 100;

main();

function main(): void
{
    if (hasCliFlag('--dry-run')) {
        runDryRun();
        return;
    }

    $retailOrders = fetchAllRetailCrmOrders();
    if (count($retailOrders) === 0) {
        echo "No orders returned from RetailCRM.\n";
        return;
    }

    $rows = [];
    foreach ($retailOrders as $order) {
        $rows[] = mapRetailOrderToSupabaseRow($order);
    }

    $inserted = 0;
    foreach (array_chunk($rows, SUPABASE_INSERT_CHUNK) as $chunk) {
        insertChunkToSupabase($chunk);
        $inserted += count($chunk);
        echo sprintf("Inserted %d rows...\n", $inserted);
    }

    echo sprintf("Done. Imported %d orders to Supabase table %s.\n", $inserted, SUPABASE_TABLE);
}

function hasCliFlag(string $flag): bool
{
    global $argv;
    if (!isset($argv) || !is_array($argv)) {
        return false;
    }
    return in_array($flag, $argv, true);
}

/**
 * Показать, какие поля уйдут в БД, без записи в Supabase.
 */
function runDryRun(): void
{
    echo "Режим --dry-run: запись в Supabase не выполняется.\n\n";

    $templateRow = mapRetailOrderToSupabaseRow([]);
    $fieldNames = array_keys($templateRow);

    echo 'Таблица: ' . SUPABASE_TABLE . "\n";
    echo "Колонки (ключи JSON в теле POST /rest/v1/" . SUPABASE_TABLE . "):\n";
    foreach ($fieldNames as $name) {
        echo '  - ' . $name . "\n";
    }

    echo "\nВложенные структуры:\n";
    echo "  items — jsonb: массив объектов { productName, quantity, initialPrice }\n";
    echo "  custom_fields   — jsonb: объект из RetailCRM customFields + служебные _retailcrm_id, _retailcrm_external_id, _retailcrm_number\n";

    echo "\nЗапрос к RetailCRM: первая страница, limit=20 (минимум по API).\n";
    $response = fetchRetailCrmOrdersPage(1, 20);
    if (($response['success'] ?? false) !== true) {
        echo "RetailCRM вернул success=false, пример строки без данных API.\n";
        if (isset($response['errorMsg'])) {
            echo 'Причина API: ' . (string) $response['errorMsg'] . "\n";
        }
        if (isset($response['errors']) && is_array($response['errors'])) {
            echo 'Детали API: ' . json_encode($response['errors'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n";
        }
        echo "\nШаблон пустой строки (все скаляры пустые, items=[], custom_fields без CRM):\n";
        echo json_encode($templateRow, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n";
        return;
    }

    $orders = $response['orders'] ?? [];
    if (!is_array($orders) || count($orders) === 0) {
        echo "Список заказов пуст.\n";
        return;
    }

    $first = $orders[0];
    if (!is_array($first)) {
        echo "Некорректный элемент orders[0].\n";
        return;
    }

    $sample = mapRetailOrderToSupabaseRow($first);
    echo "\nПример одной записи (первый заказ из ответа API):\n";
    echo json_encode($sample, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n";
}

/**
 * @return array<int, array<string, mixed>>
 */
function fetchAllRetailCrmOrders(): array
{
    $allOrders = [];
    $page = 1;
    $totalPages = 1;

    do {
        $response = fetchRetailCrmOrdersPage($page, RETAILCRM_PAGE_LIMIT);
        if (($response['success'] ?? false) !== true) {
            throw new RuntimeException('RetailCRM API returned unsuccessful response on page ' . $page);
        }

        $orders = $response['orders'] ?? [];
        if (!is_array($orders)) {
            throw new RuntimeException('RetailCRM API response does not contain orders array');
        }

        foreach ($orders as $order) {
            if (is_array($order)) {
                $allOrders[] = $order;
            }
        }

        $pagination = $response['pagination'] ?? [];
        $totalPages = (int) ($pagination['totalPageCount'] ?? 1);
        $currentPage = (int) ($pagination['currentPage'] ?? $page);

        echo sprintf("Fetched page %d/%d, orders total so far: %d\n", $currentPage, $totalPages, count($allOrders));
        $page++;
    } while ($page <= $totalPages);

    return $allOrders;
}

/**
 * @return array<string, mixed>
 */
function fetchRetailCrmOrdersPage(int $page, int $limit): array
{
    $url = rtrim(RETAILCRM_BASE_URL, '/') . API_VERSION_PATH . '/orders';
    $query = http_build_query(
        [
            'apiKey' => RETAILCRM_API_KEY,
            'page' => $page,
            'limit' => $limit,
        ],
        '',
        '&',
        PHP_QUERY_RFC3986
    );

    return httpRequest('GET', $url . '?' . $query);
}

/**
 * @param array<string, mixed> $order
 * @return array<string, mixed>
 */
function mapRetailOrderToSupabaseRow(array $order): array
{
    $itemsSource = $order['items'] ?? [];
    $items = [];
    if (is_array($itemsSource)) {
        foreach ($itemsSource as $item) {
            if (!is_array($item)) {
                continue;
            }
            $items[] = [
                'productName' => (string) ($item['productName'] ?? ($item['offer']['name'] ?? 'Unknown product')),
                'quantity' => (float) ($item['quantity'] ?? 0),
                'initialPrice' => (float) ($item['initialPrice'] ?? 0),
            ];
        }
    }

    $deliveryCity = (string) ($order['delivery']['address']['city'] ?? '');
    $deliveryAddress = (string) ($order['delivery']['address']['text'] ?? '');

    $customFields = [];
    if (isset($order['customFields']) && is_array($order['customFields'])) {
        $customFields = $order['customFields'];
    }

    // Save source identifiers in custom fields to simplify traceability.
    if (isset($order['id'])) {
        $customFields['_retailcrm_id'] = (string) $order['id'];
    }
    if (isset($order['externalId'])) {
        $customFields['_retailcrm_external_id'] = (string) $order['externalId'];
    }
    if (isset($order['number'])) {
        $customFields['_retailcrm_number'] = (string) $order['number'];
    }

    $customFieldsValue = $customFields === [] ? (object) [] : $customFields;

    return [
        'first_name' => (string) ($order['firstName'] ?? ''),
        'last_name' => (string) ($order['lastName'] ?? ''),
        'phone' => (string) ($order['phone'] ?? ''),
        'email' => (string) ($order['email'] ?? ''),
        'order_type' => (string) ($order['orderType'] ?? ''),
        'order_method' => (string) ($order['orderMethod'] ?? ''),
        'status' => (string) ($order['status'] ?? ''),
        'items' => $items,
        'delivery_city' => $deliveryCity,
        'delivery_address' => $deliveryAddress,
        'custom_fields' => $customFieldsValue,
    ];
}

/**
 * @param array<int, array<string, mixed>> $chunk
 */
function insertChunkToSupabase(array $chunk): void
{
    $url = rtrim(SUPABASE_URL, '/') . '/rest/v1/' . SUPABASE_TABLE;
    $body = json_encode($chunk, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($body === false) {
        throw new RuntimeException('Failed to encode chunk for Supabase insert');
    }

    $headers = [
        'apikey: ' . SUPABASE_SERVICE_ROLE_KEY,
        'Authorization: Bearer ' . SUPABASE_SERVICE_ROLE_KEY,
        'Content-Type: application/json',
        'Prefer: return=minimal',
    ];

    $response = httpRequest('POST', $url, $body, $headers, true);
    if ($response['httpCode'] >= 400) {
        throw new RuntimeException(
            'Supabase insert failed, HTTP ' . $response['httpCode'] . ', body: ' . $response['raw']
        );
    }
}

/**
 * @return array<string, mixed>
 */
function httpRequest(string $method, string $url, ?string $body = null, array $headers = [], bool $allowNonJson = false): array
{
    $ch = curl_init($url);
    if ($ch === false) {
        throw new RuntimeException('Cannot initialize curl');
    }

    $opts = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_CONNECTTIMEOUT => 20,
        CURLOPT_TIMEOUT => 60,
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
        if ($allowNonJson) {
            return [
                'httpCode' => $httpCode,
                'raw' => $responseRaw,
            ];
        }
        throw new RuntimeException('Invalid JSON response, HTTP ' . $httpCode . ', body: ' . $responseRaw);
    }

    $decoded['httpCode'] = $httpCode;
    $decoded['raw'] = $responseRaw;
    return $decoded;
}

