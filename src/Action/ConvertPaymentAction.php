<?php

namespace Sergiosanchezalvarez\CecaPlugin\Action;


use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Exception\UnsupportedApiException;
use Payum\Core\Model\PaymentInterface;
use Payum\Core\Request\Convert;
use Payum\Core\ApiAwareInterface;

/**
 * Class ConvertPaymentAction
 * @package Sergiosanchezalvarez\CecaPlugin\Action
 * @author Sergio Sánchez <sergiosanchezalvarez@gmail.com>
 */
final class ConvertPaymentAction implements ActionInterface, GatewayAwareInterface, ApiAwareInterface {
    use GatewayAwareTrait;

    /**
     * @var Api
     */
    protected $api = [];

    /**
     * {@inheritDoc}
     */
    public function setApi($api) {
        if (!is_array($api)) {
            throw new UnsupportedApiException('Not supported.');
        }

        $this->api = $api;
    }


    /**
     * Execute convert payment action and prepare body for request
     * @param mixed $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);
        /** @var PaymentInterface $payment */
        $payment = $request->getSource();

        $details = ArrayObject::ensureArrayObject($payment->getDetails());

        $options = array(
            'Amount' => $payment->getTotalAmount(),
            'Num_operacion' => $payment->getNumber(),
            /* Aprovecho para inyectar aquí los valores, así los tengo en el controlador y puedo realizar la verificación de la firma al vuelo */
            'Environment' => $this->api['isProductionMode'] ? 'real' : 'test', // Puedes indicar test o real
            'MerchantID' => $this->api['merchantID'],
            'AcquirerBIN' => $this->api['acquirerBIN'],
            'TerminalID' => $this->api['terminalID'],
            'ClaveCifrado' => $this->api['claveCifrado'],
            'PaymentTokenHash' => $request->getToken()->getHash()
        );

        $details->defaults($options);
        $request->setResult((array)$details);
    }

    /**
     * @param mixed $request
     * @return bool
     */
    public function supports($request)
    {
        return
            $request instanceof Convert &&
            $request->getSource() instanceof PaymentInterface &&
            $request->getTo() === 'array';
    }

}