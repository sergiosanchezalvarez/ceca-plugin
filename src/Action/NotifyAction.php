<?php

namespace Sergiosanchezalvarez\CecaPlugin\Action;

use Sergiosanchezalvarez\CecaPlugin\SetCeca;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\GetHumanStatus;
use Payum\Core\Request\Notify;

/**
 * Class NotifyAction
 * @package Sergiosanchezalvarez\CecaPlugin\Action
 * @author Sergio Sánchez <sergiosanchezalvarez@gmail.com>
 */
final class NotifyAction implements ActionInterface, GatewayAwareInterface {
    use GatewayAwareTrait;

    /**
     * @param mixed $request
     * @throws Payum\Core\Exception\RequestNotSupportedException if the action dose not support the request.
     */
    public function execute($request) {
        echo "Notify Action";exit;
        /** @var $request Payum\Core\Request\Notify */
        RequestNotSupportedException::assertSupports($this, $request);

        $setCeca = new SetCeca($request->getToken());
        $setCeca->setModel($request->getModel());
        $this->getGateway()->execute($setCeca);

        $status = new GetHumanStatus($request->getToken());
        $status->setModel($request->getModel());
        $this->getGateway()->execute($status);
    }

    /**
     * @return \Payum\Core\GatewayInterface
     */
    public function getGateway() {
        return $this->gateway;
    }

    /**
     * @param mixed $request
     * @return bool
     */
    public function supports($request) {
        return
            $request instanceof Notify &&
            $request->getModel() instanceof \ArrayObject;
    }
}