<?php

namespace Sergiosanchezalvarez\CecaPlugin\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sergiosanchezalvarez\CecaPlugin\ApiCeca;
use Payum\Core\Request\GetHumanStatus;
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\Component\Payment\Model\PaymentInterface;

final class NotifyController extends Controller
{
    public function doAction(Request $request):Response
    {
        // Nos viene por post...
        $params = $request->request->all();

        $order_id = (int) $params['Num_operacion']; // casteo a entero para eliminar los ceros
        /*
            [MerchantID] => 081458127
            [AcquirerBIN] => 0000554026
            [TerminalID] => 00000003
            [Num_operacion] => 000000029
            [Importe] => 000000004298
            [TipoMoneda] => 978
            [Exponente] => 2
            [Referencia] => 12004811261711111636486007000
            [Firma] => adf09308e10f04a5203ba3b939e78a876dd4f62e
            [Num_aut] => 101000
            [BIN] => 450767
            [FinalPAN] => 0009
            [Cambio_moneda] => 1,00
            [Idioma] => 1
            [Descripcion] => m_iJRxLqedEyxXTKlUKiH7m9fU22XrindFJMVytCY-M
            [Pais] => 724
            [Tipo_tarjeta] => C
            [Codigo_pedido] =>
            [Codigo_cliente] =>
            [Codigo_comercio] =>
            [Caducidad] => 201712
            [Idusuario]
        */

        //$order_id = 34; // overrideamos de momento para las pruebas
        $payment = $this->container->get('sylius.repository.payment')->findOneBy(['id' => $order_id]);

        /**
         * var $details Array
         */
        $details = $payment->getDetails();

        $cecaOptions = array(
            'Environment' => $details['Environment'],
            'MerchantID' => $details['MerchantID'],
            'AcquirerBIN' => $details['AcquirerBIN'],
            'TerminalID' => $details['TerminalID'],
            'ClaveCifrado' => $details['ClaveCifrado']);
        $apiCeca = new ApiCeca($cecaOptions);

        $apiCeca->setFormHiddens(array(
            'Num_operacion' => $details['Num_operacion'],
            'Importe' => $details['Amount'],
            'URL_OK' => $details['url_ok'],
            'URL_NOK' => $details['url_nok'].'?pagocancelado=1'
        ));

        $apiCeca->checkTransaction($params);

        $payment->setState(PaymentInterface::STATE_COMPLETED);
        $payment->getOrder()->setPaymentState(OrderPaymentStates::STATE_PAID);

        $manager = $this->container->get('sylius.manager.payment');
        $manager->persist($payment);
        $manager->flush();
        // y rezar... en teoría ya está OK el pago...

        return new Response();
    }
}