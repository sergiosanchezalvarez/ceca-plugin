<?php

namespace Sergiosanchezalvarez\CecaPlugin\Action;

use Sergiosanchezalvarez\CecaPlugin\Exception\CecaException;
use Sergiosanchezalvarez\CecaPlugin\CecaWrapper;
use Sergiosanchezalvarez\CecaPlugin\CecaWrapperInterface;
use Sergiosanchezalvarez\CecaPlugin\SetCeca;
use Czende\GoPayPlugin\SetGoPay;
use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Exception\UnsupportedApiException;
use Payum\Core\Reply\HttpRedirect;
use Payum\Core\Security\TokenInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Webmozart\Assert\Assert;

final class CecaAction implements ApiAwareInterface, ActionInterface {
    private $api = [];

    private $cecaWrapper;

    public function setApi($api)
    {
        // TODO: Implement setApi() method.
    }

    public function execute($request)
    {
        // TODO: Implement execute() method.
    }

    public function supports($request)
    {
        // TODO: Implement supports() method.
    }

    public function getCecaWrapper()
    {

    }

    public function setCecaWrapper()
    {

    }

    private function prepareOrder(TokenInterface $token, $model, $merchantid)
    {

    }

    private function resolveProducts($order)
    {

    }

}