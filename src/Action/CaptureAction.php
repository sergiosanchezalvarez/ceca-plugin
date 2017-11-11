<?php

namespace Sergiosanchezalvarez\CecaPlugin\Action;

use Sergiosanchezalvarez\CecaPlugin\SetCeca;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Exception\UnsupportedApiException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\Capture;
use Payum\Core\Security\TokenInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Reply\HttpPostRedirect;

use Sergiosanchezalvarez\CecaPlugin\ApiCeca;

/**
 * Class CaptureAction
 * @package Sergiosanchezalvarez\CecaPlugin\Action
 * @author Sergio Sánchez <sergiosanchezalvarez@gmail.com>
 */
final class CaptureAction implements ActionInterface, ApiAwareInterface {
    use GatewayAwareTrait;

    /**
     * @var Api
     */
    protected $api = [];

    /**
     * @var Sergiosanchezalvarez\CecaPlugin\ApiCeca
     */
    protected $apiCeca;

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
     * Execute capture action based on given request and prepare customer and order
     * @param mixed $request
     * @throws Payum\Core\Exception\RequestNotSupportedException if the action does not support the request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        if (isset($_GET['pagocancelado'])) {
            return;
        }

        $postData = ArrayObject::ensureArrayObject($request->getModel());

        $postData->validatedKeysSet(array(
            'Amount',
            'Num_operacion'
        ));

        // inyectamos los demás valores necesarios para la llamada a Ceca
        $cecaOptions = array(
            'Environment' => $this->api['isProductionMode'] ? 'real' : 'test', // Puedes indicar test o real
            'MerchantID' => $this->api['merchantID'],
            'AcquirerBIN' => $this->api['acquirerBIN'],
            'TerminalID' => $this->api['terminalID'],
            'ClaveCifrado' => $this->api['claveCifrado']);

        $apiCeca = $this->getApiCeca() ? $this->getApiCeca() : new ApiCeca($cecaOptions);

        if (false == $postData['URL_OK'] && $request->getToken()) {
            $postData['url_ok'] = $request->getToken()
                ->getTargetUrl();
        }
        if (false == $postData['URL_NOK'] && $request->getToken()) {
            $postData['url_nok'] = $request->getToken()
                ->getTargetUrl();
        }

        $apiCeca->setFormHiddens(array(
            'Num_operacion' => $postData['Num_operacion'],
            'Importe' => $postData['Amount'],
            'URL_OK' => $postData['url_ok'],
            'URL_NOK' => $postData['url_nok'].'?pagocancelado=1',
            'Descripcion' => $request->getToken()->getHash() // The payment token... to verify payments
        ));

        //var_dump($apiCeca->getValues());exit;
        throw new HttpPostRedirect($apiCeca->getCurrentEnviroment(), $apiCeca->getValues());
    }

    /**
     * @return Sergiosanchezalvarez\CecaPlugin\ApiCeca
     */
    public function getApiCeca() {
        return $this->apiCeca;
    }
    /**
     * @param Sergiosanchezalvarez\CecaPlugin\Ceca\ApiCeca
     */
    public function setApiCeca($apiCeca) {
        $this->apiCeca = $apiCeca;
    }

    public function supports($request)
    {
        return
            $request instanceof Capture &&
            $request->getModel() instanceof \ArrayAccess;
    }

    /**
     * @return \Payum\Core\GatewayInterface
     */
    public function getGateway()
    {
        return $this->gateway;
    }

    /**
     * @param TokenInterface $token
     * @param ArrayObject $model
     * @return mixed
     */
    public function getCecaAction(TokenInterface $token, ArrayObject $model)
    {
        $cecaAction = new SetCeca($token);
        $cecaAction->setModel($model);

        return $cecaAction;
    }
}