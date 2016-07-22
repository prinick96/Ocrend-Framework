<?php

# Seguridad
defined('INDEX_DIR') OR exit('Ocrend software says .i.');

//------------------------------------------------

final class Paypal {

  //------------------------------------------------

  /**
    * MÉTODO PRIVADO
    * Inicializa la conexión con PayPal.
    *
    * @return ApiContext object
  */
  final private static function init() : \PayPal\Rest\ApiContext {

    try {
      if(Func::emp(PAYPAL_CLIENT_ID) || Func::emp(PAYPAL_CLIENT_SECRET)) {
        throw new Exception(true);
      }
    } catch (Exception $e) {
      die(json_decode(array('message' => 'Las constantes de conexión están vacías', 'success' => 0)));
    }

    $apiContext = new \PayPal\Rest\ApiContext(
        new \PayPal\Auth\OAuthTokenCredential(
            PAYPAL_CLIENT_ID,     // ClientID
            PAYPAL_CLIENT_SECRET    // ClientSecret
        )
    );

    $apiContext->setConfig(
      array(
        'mode' => PAYPAL_MODE,
        'http.ConnectionTimeOut' => 30,
        'log.LogEnabled' => DEBUG ? true : false,
        'log.FileName' => 'PayPal.log',
        'log.LogLevel' => DEBUG ? 'DEBUG' : 'FINE'
      )
    );

    return $apiContext;
  }

  //------------------------------------------------

  /**
    * Crea un pago con la API SDK de Paypal
    *
    * @param array $config: Array con la forma array('success' => , 'error' => , 'descripcion' => )
    * @param array $items: Matriz con todos los items y la forma array(
    *                                                               array(
    *                                                                  'nombre' => 'Item 1',
    *                                                                  'cantidad' => 1,
    *                                                                  'precio' => 7.5,
    *                                                                  'envio' => 0,
    *                                                                  'tax' => 0
    *                                                                  )
    *                                                              )
    * @param bool $individual: Indica si los costes de tax y shipping son individuales para cada producto, o totales
    * @param string $currency: Moneda
    *
    * @return array con la forma array('id' => , 'success' => , 'url' => , 'message' => )
  */
  final public static function pay(array $config, array $items, bool $individual = true, string $currency = 'USD') : array {
    $payer = new \PayPal\Api\Payer;
    $payer->setPaymentMethod('paypal');

    $cart = array();
    $tax = 0;
    $shipping = 0;
    $subtotal = 0;

    foreach ($items as $i => $item) {
      ${'item' . $i} = new \PayPal\Api\Item;
      ${'item' . $i}->setName($item['nombre'])
              ->setCurrency($currency)
              ->setQuantity((int) $item['cantidad'])
              ->setPrice((float) $item['precio']);

      $cart[] = ${'item' . $i};

      $shipping += $individual ? $item['envio'] : ($item['envio'] * $item['cantidad']);
      $tax += $individual ? $item['tax'] : ($item['tax'] * $item['cantidad']);
      $subtotal += $item['precio'] * $item['cantidad'];
    }

    $itemList = new \PayPal\Api\ItemList;
    $itemList->setItems($cart);

    $details = new \PayPal\Api\Details;
    $details->setShipping((float) $shipping)
        ->setTax((float) $tax)
        ->setSubtotal((float) $subtotal);

    $amount = new \PayPal\Api\Amount;
    $amount->setCurrency($currency)
        ->setTotal($subtotal + $shipping + $tax)
        ->setDetails($details);

    $transaction = new \PayPal\Api\Transaction;
    $transaction->setAmount($amount)
        ->setItemList($itemList)
        ->setDescription($config['descripcion'])
        ->setInvoiceNumber(uniqid());

    $redirectUrls = new \PayPal\Api\RedirectUrls;
    $redirectUrls->setReturnUrl(URL . $config['success'])
        ->setCancelUrl(URL . $config['error']);

    $payment = new \PayPal\Api\Payment;
    $payment->setIntent('sale')
        ->setPayer($payer)
        ->setRedirectUrls($redirectUrls)
        ->setTransactions(array($transaction));

    $request = clone $payment;

    try {
      $payment->create(self::init());

      $id = $payment->getID();
      $success = 1;
      $message = 'Conexión realizada';
      $url = $payment->getApprovalLink();

    } catch (PayPal\Exception\PayPalConnectionException $ex) {

      $id = $payment->getID();
      $success = 1;
      $message = $ex->getData();
      $url = '#';

    }

    return array(
      'id' => $id,
      'success' => $success,
      'message' => $message,
      'url' => $url
    );
  }

}

?>
