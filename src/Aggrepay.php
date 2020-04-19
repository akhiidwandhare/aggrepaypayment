<?php

namespace ssi\aggrepaypayment;
use Symfony\Component\HttpFoundation\Response;

class Aggrepay
{
    const PRODUCTION_URL = 'https://biz.aggrepaypayments.com/v2/paymentrequest';

    /**
     * @var string
     */
    private $KEY;

    /**
     * @var string
     */
    private $SALT;

    /**
     * @var string
     */
    private $MODE;

    /**
     * @param array $options
     */
    protected $config;


    public function __construct(array $options = [])
    {
        foreach ($options as $key => $value) {
            config(["aggrepayconfig.{$key}" =>  $value]);
        }
        $this->config = config('aggrepayconfig');
        $this->KEY = $this->config["KEY"];
        $this->MODE = $this->config["MODE"];
        $this->SALT = $this->config["SALT"];
    }

    /**
     * @return string
     */
    public function getMerchantKey()
    {
        return $this->KEY;
    }

    /**
     * @return string
     */
    public function getMerchantSalt()
    {
        return $this->SALT;
    }

    /**
     * @return string
     */
    public function getMode()
    {
        return $this->MODE;
    }

    /**
     * @return string
     */
    public function getServiceUrl()
    {
        return self::PRODUCTION_URL;
    }

    /**
     * @return array
     */
    public function getChecksumParams()
    {
        return array_merge(
            ['address_line_1', 'address_line_2', 'amount', 'api_key', 'city', 'country', 'currency', 'description', 'email', 'mode', 'name', 'order_id', 'phone', 'return_url', 'state', 'zip_code',],
            array_map(function ($i) {
                return "udf{$i}";
            }, range(1, 6))
        );
    }

    /**
     * @param array $params
     *
     * @return string
     */
    private function getChecksum($input)
    {
        /* Columns used for hash calculation, Donot add or remove values from $hash_columns array */
        $hash_columns = ['address_line_1', 'address_line_2', 'amount', 'api_key', 'city', 'country', 'currency', 'description', 'email', 'mode', 'name', 'order_id', 'phone', 'return_url', 'state', 'udf1', 'udf2', 'udf3', 'udf4', 'udf5', 'zip_code',];
        /*Sort the array before hashing*/
        sort($hash_columns);

        /*Create a | (pipe) separated string of all the $input values which are available in $hash_columns*/
        $hash_data = $this->getMerchantSalt();
        foreach ($hash_columns as $column) {
            if (isset($input[$column])) {
                if (strlen($input[$column]) > 0) {
                    $hash_data .= '|' . trim($input[$column]);
                }
            }
        }

        $hash = strtoupper(hash("sha512", $hash_data));
        
        return $hash;
    }

    /**
     * @param array $params
     *
     * @throws \InvalidArgumentException
     *
     * @return Response
     */
    public function startPayment(array $params)
    {
        $requiredParams = ['order_id', 'mode', 'amount', 'currency', 'description', 'name', 'email', 'phone', 'city', 'country', 'zip_code', 'return_url'];
        foreach ($requiredParams as $requiredParam) {
            if (!isset($params[$requiredParam])) {
                throw new \InvalidArgumentException(sprintf('"%s" is a required param.', $requiredParam));
            }
        }

        $params = array_merge($params, ['api_key' => $this->getMerchantKey(), 'mode' => $this->getMode()]);
        $params = array_merge($params, ['hash' => $this->getChecksum($params)]);
        $params = array_map(function ($param) {
            return htmlentities($param, ENT_QUOTES, 'UTF-8', false);
        }, $params);

        $output = sprintf('<form id="payment_form" method="POST" action="%s">', $this->getServiceUrl());

        foreach ($params as $key => $value) {
            $output .= sprintf('<input type="hidden" name="%s" value="%s" />', $key, $value);
        }
        
        $output .= '<div id="redirect_info" style="display: none">Redirecting...</div>
                <input id="payment_form_submit" type="submit" value="Proceed to Aggrepay" />
            </form>
            <script>
                document.getElementById(\'redirect_info\').style.display = \'block\';
                document.getElementById(\'payment_form_submit\').style.display = \'none\';
                document.getElementById(\'payment_form\').submit();
            </script>';

        return Response::create($output, 200, [
            'Content-type' => 'text/html; charset=utf-8',
        ]);
    }

    public function aggrepayData(array $params)
    {
        return new AggrepayResponse($this, $params);
    }
}
