<?php
/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Igor Borodikhin <iborodikhin@gmail.com>
 * @license MIT
 */
namespace Wooppay;

use Wooppay\Objects\Request;
use Wooppay\Objects\Response;

/**
 * Wooppay SOAP client.
 */
class Client extends \SoapClient
{
    /**
     * Default options.
     *
     * @var array
     */
    protected $defaults = [
        'connection_timeout' => 60,
        'cache_wsdl'         => WSDL_CACHE_MEMORY,
        'trace'              => 1,
        'soap_version'       => SOAP_1_2,
        'encoding'           => 'UTF-8',
        'exceptions'         => true,
    ];

    /**
     * A flag indicating whether a client has logged in
     *
     * @var bool
     */
    protected $isLoggedIn = false;

    /**
     * Client constructor.
     *
     * @param string $wsdl
     * @param array  $options
     */
    public function __construct($wsdl = null, array $options = [])
    {
        $options = array_merge($this->defaults, $options);

        parent::__construct($wsdl, $options);
    }

    /**
     * Calls a SOAP function
     *
     * @param string $name
     * @param array  $args
     * @param array  $options
     * @param mixed  $inputHeaders
     * @param array  $outputHeaders
     * @return mixed
     */
    public function __soapCall($name, $args, $options = null, $inputHeaders = null, &$outputHeaders = null)
    {
        $result = parent::__soapCall($name, $args, $options, $inputHeaders,$outputHeaders);
        $result->raw_result = $this->__getLastResponse();

        return $result;
    }

    /**
     * Logs user in.
     *
     * @param  string                                $username
     * @param  string                                $password
     * @return \Wooppay\Objects\Response\Login
     * @throws \Wooppay\Objects\Exceptions\Exception
     */
    public function login($username, $password)
    {
        $request  = (new Request\Login())
            ->setUsername($username)
            ->setPassword($password);
        $result   = $this->__soapCall('core_login', [$request]);
        $response = Response\Login::factory($result);

        $this->__setCookie('session', $response->getSession());
        $this->isLoggedIn = true;

        return $response;
    }

    /**
     * Returns a flag indicating whether a client has logged in
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->isLoggedIn;
    }

    /**
     * Sends confirmation code to users' phone.
     *
     * @param  string                                           $phone
     * @return boolean
     * @throws \Wooppay\Objects\Exceptions\BadCredentials
     * @throws \Wooppay\Objects\Exceptions\UnsuccessfulResponse
     */
    public function requestConfirmationCode($phone)
    {
        $request = (new Request\RequestConfirmationCode())
            ->setPhone($phone);
        $result  = $this->__soapCall('core_requestConfirmationCode', [$request]);

        return Response\Base::checkResponse($result);
    }

    /**
     * Creates invoice.
     *
     * @param  string                                          $referenceId
     * @param  string                                          $backUrl
     * @param  string                                          $requestUrl
     * @param  string                                          $addInfo
     * @param  float                                           $amount
     * @param  string                                          $deathDate
     * @param  integer                                         $serviceType
     * @param  string                                          $description
     * @param  string                                          $orderNumber
     * @param  string                                          $userEmail
     * @param  string                                          $userPhone
     * @return \Wooppay\Objects\Response\CreateInvoiceExtended
     * @throws \Wooppay\Objects\Exceptions\Exception
     */
    public function createInvoiceExtended(
        $referenceId,
        $backUrl,
        $requestUrl,
        $addInfo,
        $amount,
        $deathDate,
        $serviceType,
        $description,
        $orderNumber,
        $userEmail,
        $userPhone
    ) {
        $request = (new Request\CreateInvoiceExtended())
            ->setReferenceId($referenceId)
            ->setBackUrl($backUrl)
            ->setRequestUrl($requestUrl)
            ->setAddInfo($addInfo)
            ->setAmount($amount)
            ->setDeathDate($deathDate)
            ->setServiceType($serviceType)
            ->setDescription($description)
            ->setOrderNumber($orderNumber)
            ->setUserEmail($userEmail)
            ->setUserPhone($userPhone);

        $result = $this->__soapCall('cash_createInvoiceExtended', [$request]);

        return Response\CreateInvoiceExtended::factory($result);
    }

    /**
     * Creates invoice by service.
     *
     * @param  string                                          $referenceId
     * @param  string                                          $backUrl
     * @param  string                                          $requestUrl
     * @param  string                                          $addInfo
     * @param  float                                           $amount
     * @param  string                                          $deathDate
     * @param  integer                                         $serviceType
     * @param  string                                          $description
     * @param  string                                          $orderNumber
     * @param  string                                          $userEmail
     * @param  string                                          $userPhone
     * @param  string                                          $serviceName
     * @return \Wooppay\Objects\Response\CreateInvoiceExtended
     */
    public function createInvoiceByService(
        $referenceId,
        $backUrl,
        $requestUrl,
        $addInfo,
        $amount,
        $deathDate,
        $serviceType,
        $description,
        $orderNumber,
        $userEmail,
        $userPhone,
        $serviceName
    ) {
        $request = (new Request\CreateInvoiceByService())
            ->setServiceName($serviceName)
            ->setReferenceId($referenceId)
            ->setBackUrl($backUrl)
            ->setRequestUrl($requestUrl)
            ->setAddInfo($addInfo)
            ->setAmount($amount)
            ->setDeathDate($deathDate)
            ->setServiceType($serviceType)
            ->setDescription($description)
            ->setOrderNumber($orderNumber)
            ->setUserEmail($userEmail)
            ->setUserPhone($userPhone);

        $result = $this->__soapCall('cash_createInvoiceByService', [$request]);

        return Response\CreateInvoiceExtended::factory($result);
    }

    /**
     * Get operations' status.
     *
     * @param  array $ids
     * @return array
     */
    public function getOperationData(array $ids)
    {
        $request = (new Request\GetOperationData())
            ->setOperationId($ids);

        $result = $this->__soapCall('cash_getOperationData', [$request]);

        return Response\GetOperationData::factory($result);
    }
}
