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


        if (null == $model['Num_operacion']) {
            $request->markNew();
            return;
        }

        if ($model['Num_operacion'] && null === $model['Firma']) {
            echo "Pendiente";
            $request->markPending();
            return;
        }

        if (isset($_GET['pagocancelado'])) {
            echo "Cancelado";
            var_dump($request);exit;
            $request->markCanceled();
            return;
        }

        if (0 <= $model['Firma'] && 99 >= $model['Firma']) {
            echo "Ok";
            var_dump($request);exit;
            $request->markCaptured();
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