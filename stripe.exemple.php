<?php

$key = '';
$domain = ''; 
				
require('../stripe/init.php');
\Stripe\Stripe::setApiKey($key);
header('Content-Type: application/json');

$checkout_session = \Stripe\Checkout\Session::create(
    [
        'payment_method_types' => ['card'],
        'line_items' => [
            [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => 5000,
                    'product_data' => [
                        'name' => '',
                        'images' => [],
                ],
            ],
            'quantity' => 1,
        ]
    ],
    'mode' => 'payment',
    'success_url' => "{$domain}/commander/succes/?session_id={CHECKOUT_SESSION_ID}",
    'cancel_url' => "{$domain}/commander/echec/?session_id={CHECKOUT_SESSION_ID}",
    'metadata' => [
       
    ]
]);
header("HTTP/1.1 303 See Other");
header("Location: " . $checkout_session->url);


// Retrouver une session

/*

require('../stripe/init.php');
\Stripe\Stripe::setApiKey('');
$session = \Stripe\Checkout\Session::retrieve($_GET['session_id']);

echo $session->mode;

*/