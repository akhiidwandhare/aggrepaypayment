# aggrepaypayment
aggrepaypayment

    #Open the config/app.php and add this line in providers section.
    ssi\aggrepaypayment\AggrepayPaymentServiceProvider::class,

    #Add this line in the aliases section.
    'Aggrepay' => ssi\aggrepaypayment\AggrepayFacade::class

    #Publish vendor.
    php artisan vendor:publish

    $params = [
        'order_id' => 'test0013', 
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

    //Returns response
    $result = Aggrepay::aggrepayData($_POST);

    //Returns an json of all the parameters of use in the transaction
    echo json_encode($result->getParams());
