<?php

namespace Sergiosanchezalvarez\CecaPlugin\Action;

use Sergiosanchezalvarez\CecaPlugin\SetCeca;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\Capture;
use Payum\Core\Security\TokenInterface;

/**
 * Class CaptureAction
 * @package Sergiosanchezalvarez\CecaPlugin\Action
 * @author Sergio Sánchez <sergiosanchezalvarez@gmail.com>
 */
final class CaptureAction implements ActionInterface, GatewayAwareInterface {
    use GatewayAwareTrait;

    /**
     * Execute capture action based on given request and prepare customer and order
     * @param mixed $request
     * @throws Payum\Core\Exception\RequestNotSupportedException if the action does not support the request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $model = $request->getModel();
        ArrayObject::ensureArrayObject($model);

        $model['customer'] = $request->getFirstModel()->getOrder()->getCustomer();
        $model['order'] = $request->getFirstModel()->getOrder();

        $goPayAction = $this->getGoPayAction($request->getToken(), $model);

        $this->getGateway()->execute($goPayAction);
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