<?php

namespace Sergiosanchezalvarez\CecaPlugin;

/**
 * Class CecaWrapper
 * @package Sergiosanchezalvarez\CecaPlugin
 * @author Sergio Sánchez <sergiosanchezalvarez@gmail.com>
 */
final class CecaWrapper implements CecaWrapperInterface {

    const CREATED = 'CREATED';
    const PAID = 'PAID';
    const CANCELED = 'CANCELED';
    const TIMEOUTED = 'TIMEOUTED';

    var $ceca;

    public function __construct()
    {
        /*
        public function __construct($goid, $clientId, $clientSecret, $isProductionMode) {
            $this->gopay = Api::payments([
                'goid' => $goid,
                'clientId' => $clientId,
                'clientSecret' => $clientSecret,
                'isProductionMode' => $isProductionMode
            ]);
        }
        */
       // $this->ceca = new \stdClass();
    }

    /**
     * Create payment based on given order
     * @return \GoPay\Http\Response
     */
    public function create($order) {
        return $this->ceca->createPayment($order);
    }
    /**
     * Retrieve payment based on unique payment ID
     * @return \GoPay\Http\Response
     */
    public function retrieve($paymentID) {
        return $this->ceca->getStatus($paymentID);
    }
}
