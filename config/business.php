<?php

return [
    'allowRegistration' => true,
    'themeColors' => [
        'blue' => 'Blue',
        'black' => 'Black',
        'purple' => 'Purple',
        'green' => 'Green',
        'red' => 'Red',
        'yellow' => 'Yellow',
        'blue-light' => 'Blue Light',
        'black-light' => 'Black Light',
        'purple-light' => 'Purple Light',
        'green-light' => 'Green Light',
        'red-light' => 'Red Light',
    ],
    'settings' => [
        'name',
        'start_date',
        'tax_number_1',
        'tax_label_1',
        'tax_number_2',
        'tax_label_2'
    ],
    'default_date_format' => 'm/d/Y',
    'enabledModules' => [
        'purchases',
        'add_sale',
        'pos_sale',
        'stock_transfers',
        'stock_adjustment',
        'expenses'
    ],
    'ref_no_prefixes' => [
        'purchase' => 'PO',
        'stock_transfer' => 'ST',
        'stock_adjustment' => 'SA',
        'sell_return' => 'CN',
        'expense' => 'EP',
        'contacts' => 'CO',
        'purchase_payment' => 'PP',
        'sell_payment' => 'SP',
        'business_location' => 'BL'
    ],
    'keyboardShortcuts' => '{"pos":{"express_checkout":"shift+e","pay_n_ckeckout":"shift+p","draft":"shift+d","cancel":"shift+c","edit_discount":"shift+i","edit_order_tax":"shift+t","add_payment_row":"shift+r","finalize_payment":"shift+f","recent_product_quantity":"f2","add_new_product":"f4"}}',
];
