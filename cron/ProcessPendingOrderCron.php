<?php

use PlacetoPay\PaymentMethod\GatewayMethod;

$isCli = in_array(PHP_SAPI, ['cli', 'phpdbg'], true)
    || (empty($_SERVER['REQUEST_METHOD']) && empty($_SERVER['REMOTE_ADDR']));

if (!$isCli) {
    if (!headers_sent()) {
        http_response_code(403);
        header('Content-Type: text/plain; charset=utf-8');
    }

    exit('Forbidden: this task can only be executed from the command line.');
}

$_SERVER['HTTP_HOST'] = $_SERVER['HTTP_HOST'] ?? 'localhost';
$_SERVER['REQUEST_METHOD'] = $_SERVER['REQUEST_METHOD'] ?? 'GET';

require_once(dirname(__FILE__) . '/../../../../wp-blog-header.php');

$customerOrders = wc_get_orders(apply_filters('woocommerce_my_account_my_orders_query', [
    'limit' => -1,
    'shop_order_status' => 'on-hold',
    'status' => [
        'wc-pending',
        'wc-on-hold',
    ],
]));

if ($customerOrders) {
    foreach ($customerOrders as $orderPost) {
        $requestId = get_post_meta(
            $orderPost->get_id(),
            GatewayMethod::META_REQUEST_ID,
            true
        );

        if (!$requestId) {
            continue;
        }

        $order = wc_get_order($orderPost->get_id());

        if (!GatewayMethod::isPendingStatusOrder($order->get_id())) {
            continue;
        }

        GatewayMethod::processPendingOrder($order->get_id(), $requestId);
    }
}
