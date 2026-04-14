<?php
/**
 * Webhook для RetailCRM → уведомление в Telegram (сумма заказа).
 * PHP 7.4+
 *
 * Настройте константы ниже, укажите URL этого файла в RetailCRM.
 * Рекомендуется защитить секретом (query-параметр secret или заголовок).
 *
 * Запрос: GET (основной) или POST. Данные берутся в порядке: JSON-тело → $_POST → $_GET
 * (последние не перетирают уже заданные ключи из тела).
 *
 * Сумма ищется по ключам:
 *   amount, totalSumm, summ, cost, price, value, sum
 *   или order[totalSumm], order[summ] (вложенный объект order)
 *
 * Пример GET:
 *   https://example.com/retailcrm_tg_webhook.php?secret=XXX&totalSumm=2751
 */

declare(strict_types=1);

// --- настройки ---
const TELEGRAM_BOT_TOKEN = '7362629527:AAEtqdC_c4Mchnx8TD7umxVc1aMMhRSGFNg';
const TELEGRAM_CHAT_ID = '-5042787813'; // число или @channelusername

/** Опционально: если не пусто — запрос без этого секрета отклоняется */
const WEBHOOK_SECRET = 'sec222';

/** Имя заголовка с секретом (если WEBHOOK_SECRET задан) */
const SECRET_HEADER = 'X-Webhook-Secret';

// ---

header('Content-Type: application/json; charset=utf-8');

$method = isset($_SERVER['REQUEST_METHOD']) ? (string) $_SERVER['REQUEST_METHOD'] : '';
if ($method !== 'GET' && $method !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed', 'allowed' => ['GET', 'POST']]);
    exit;
}

if (WEBHOOK_SECRET !== '') {
    $provided = '';
    if (!empty($_SERVER['HTTP_' . str_replace('-', '_', strtoupper(SECRET_HEADER))])) {
        $provided = (string) $_SERVER['HTTP_' . str_replace('-', '_', strtoupper(SECRET_HEADER))];
    }
    if ($provided === '' && isset($_GET['secret'])) {
        $provided = (string) $_GET['secret'];
    }
    if (!hash_equals(WEBHOOK_SECRET, $provided)) {
        http_response_code(403);
        echo json_encode(['ok' => false, 'error' => 'Forbidden']);
        exit;
    }
}

$raw = file_get_contents('php://input');
$data = [];

if ($raw !== false && $raw !== '') {
    $json = json_decode($raw, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($json)) {
        $data = $json;
    }
}

foreach ($_POST as $k => $v) {
    if (!array_key_exists($k, $data)) {
        $data[$k] = $v;
    }
}

foreach ($_GET as $k => $v) {
    if ($k === 'secret') {
        continue;
    }
    if (!array_key_exists($k, $data)) {
        $data[$k] = $v;
    }
}

$amount = extractAmount($data);

if ($amount === null) {
    http_response_code(400);
    echo json_encode([
        'ok' => false,
        'error' => 'Could not find amount',
        'hint' => 'GET: ?totalSumm=12345 or ?amount=12345. POST: JSON/form with totalSumm, summ, amount, …',
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$text = sprintf("Новый заказ / сумма: %s", formatMoney($amount));

$result = telegramSendMessage($text);

if (!$result['ok']) {
    http_response_code(502);
    echo json_encode(['ok' => false, 'error' => 'Telegram failed', 'detail' => $result], JSON_UNESCAPED_UNICODE);
    exit;
}

echo json_encode(['ok' => true, 'telegram' => $result['response']], JSON_UNESCAPED_UNICODE);

/**
 * @param array<string, mixed> $data
 */
function extractAmount(array $data): ?float
{
    $keys = ['amount', 'totalSumm', 'summ', 'cost', 'price', 'value', 'sum'];

    foreach ($keys as $k) {
        if (isset($data[$k]) && is_numeric($data[$k])) {
            return (float) $data[$k];
        }
    }

    if (isset($data['order']) && is_array($data['order'])) {
        foreach ($keys as $k) {
            if (isset($data['order'][$k]) && is_numeric($data['order'][$k])) {
                return (float) $data['order'][$k];
            }
        }
    }

    return null;
}

function formatMoney(float $n): string
{
    return number_format($n, 0, '.', ' ');
}

/**
 * @return array{ok: bool, response?: mixed, error?: string}
 */
function telegramSendMessage(string $text): array
{
    $url = 'https://api.telegram.org/bot' . TELEGRAM_BOT_TOKEN . '/sendMessage';
    $payload = http_build_query(
        [
            'chat_id' => TELEGRAM_CHAT_ID,
            'text' => $text,
            'parse_mode' => 'HTML',
        ],
        '',
        '&',
        PHP_QUERY_RFC3986
    );

    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        if ($ch === false) {
            return ['ok' => false, 'error' => 'curl_init failed'];
        }
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
        ]);
        $body = curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($body === false) {
            return ['ok' => false, 'error' => 'curl_exec failed'];
        }
        $decoded = json_decode($body, true);
        return [
            'ok' => $code >= 200 && $code < 300 && is_array($decoded) && ($decoded['ok'] ?? false) === true,
            'response' => $decoded !== null ? $decoded : $body,
        ];
    }

    $ctx = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
            'content' => $payload,
            'timeout' => 20,
        ],
    ]);
    $body = @file_get_contents($url, false, $ctx);
    if ($body === false) {
        return ['ok' => false, 'error' => 'file_get_contents failed'];
    }
    $decoded = json_decode($body, true);
    return [
        'ok' => is_array($decoded) && ($decoded['ok'] ?? false) === true,
        'response' => $decoded !== null ? $decoded : $body,
    ];
}
