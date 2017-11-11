<?php

namespace Sergiosanchezalvarez\CecaPlugin\Action;

use Sergiosanchezalvarez\CecaPlugin\Api;
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

        if (isset($_GET['pagoisok'])) {
            $request->markCaptured();
            return;
        }

        if (isset($_GET['pagocancelado'])) {
            $request->markCanceled();
            return;
        }

        if (null == $model['Num_operacion']) {
            $request->markNew();
            return;
        }

        if ($model['Num_operacion'] && null === $model['Firma']) {
            $request->markPending();
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