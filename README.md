# aggrepaypayment
aggrepaypayment

    $params = [
        'order_id' => 'test0013', 
        'mode' => 'TEST', 
        'amount' => '20.00', 
        'currency' => 'INR', 
        'description' => 'test', 
        'name' => 'akii', 
        'email' => 'akhiiw.office@gmail.com', 
        'phone' => '8698330550', 
        'city' => 'Nagpur', 
        'country' => 'IND', 
        'zip_code' => '440015', 
        'return_url' => url('/payStatus')
    ];

    Aggrepay::startPayment($params)->send();
