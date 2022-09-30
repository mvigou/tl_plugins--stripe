<?php

// CLE STRIPE
$GLOBALS['stripe_key'] = 'sk_test_51JmViFLd2kB48DFM1ACN5UWMoDFlbOiTz78sG0AjXkr5ead7WxLuo52QjLRpkK6Rp8tVtJXNrMyBMybSMrOhH18q00YhztPImQ';

// Gestion du stock ?
$GLOBALS['allow_stock'] = true;

// filtres
$GLOBALS['filter_auth'] = array_merge(
    $GLOBALS['filter_auth'],
    [
        'panier',
        'succes',
        'annulation'
    ]
);

?>


