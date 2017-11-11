<?php

namespace Sergiosanchezalvarez\CecaPlugin\Action;


use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Model\PaymentInterface;
use Payum\Core\Request\Convert;

/**
 * Class ConvertPaymentAction
 * @package Sergiosanchezalvarez\CecaPlugin\Action
 * @author Sergio Sánchez <sergiosanchezalvarez@gmail.com>
 */
final class ConvertPaymentAction implements ActionInterface, GatewayAwareInterface {
    use GatewayAwareTrait;



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
            'Num_operacion' => $payment->getNumber()
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