# Wooppay
Wooppay SOAP client

## Installation

    composer require kolesa-team/wooppay

## Usage example

    $client = new \Wooppay\Client($uri, $options);
    
    $client->login($username, $password);
    $client->requestConfirmationCode($phoneNumber);
    
    $result   = $client->createInvoiceExtended(…);
    $status   = $client->getOperationData([$result->getOperationId()]);
    $response = current($status);
    
    if ($status && $status->getStatus() == \Wooppay\Objects\Response\GetOperationData::STATUS_DONE) {
        echo "Operation successfully completed.";
    }
    
1
