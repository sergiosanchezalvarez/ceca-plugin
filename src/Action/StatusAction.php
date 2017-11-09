<?php

namespace Sergiosanchezalvarez\CecaPlugin\Action;

use Sergiosanchezalvarez\CecaPlugin\CecaWrapper;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\GetStatusInterface;

final class StatusAction implements ActionInterface {

    public function execute($request)
    {
        /** @var $request GetStatusInterface */
        RequestNotSupportedException::assertSupports($this, $request);
        $model = ArrayObject::ensureArrayObject($request->getModel());
        $status = $model['status'];

        if ($status === null || $status === CecaWrapper::CREATED) {
            $request->markNew();
            return;
        }

        if ($status === CecaWrapper::PAID) {
            $request->markCaptured();
            return;
        }

        if ($status === CecaWrapper::CANCELED) {
            $request->markCanceled();
            return;
        }

        $request->markUnknown();
    }

    /**
     * @param mixed $request
     * @return boolean
     */
    public function supports($request)
    {
        return
            $request instanceof GetStatusInterface &&
            $request->getModel() instanceof \ArrayAccess;
    }
}