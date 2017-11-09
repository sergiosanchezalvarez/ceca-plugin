<?php

namespace Sergiosanchezalvarez\CecaPlugin\Action;

use Sergiosanchezalvarez\CecaPlugin\Exception\CecaException;
use Sergiosanchezalvarez\CecaPlugin\CecaWrapper;
use Sergiosanchezalvarez\CecaPlugin\CecaWrapperInterface;
use Sergiosanchezalvarez\CecaPlugin\SetCeca;
use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Exception\UnsupportedApiException;
use Payum\Core\Reply\HttpRedirect;
use Payum\Core\Security\TokenInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Webmozart\Assert\Assert;

/**
 * Class GoPayAction
 * @package Sergiosanchezalvarez\CecaPlugin\Action
 * @author Sergio Sánchez <sergiosanchezalvarez@gmail.com>
 */
final class CecaAction implements ApiAwareInterface, ActionInterface
{
    /**
     * @var Payum\Core\ApiAwareInterface
     */
    private $api = [];
    /**
     * @var Sergiosanchezalvarez\CecaPlugin\CecaWrapperInterface
     */
    private $cecaWrapper;

    /**
     * @param mixed $api
     * @throws Payum\Core\Exception\UnsupportedApiException if the given Api is not supported.
     */
    public function setApi($api)
    {
        if (!is_array($api)) {
            throw new UnsupportedApiException('Not supported.');
        }
        $this->api = $api;
    }

    /**
     * @param mixed $request
     * @throws Payum\Core\Exception\RequestNotSupportedException if the action dose not support the request.
     */
    public function execute($request)
    {
        echo "Ceca Action";exit;
        RequestNotSupportedException::assertSupports($this, $request);

        $merchantID = $this->api['merchantID'];
        $acquirerBIN = $this->api['acquirerBIN'];
        $terminalID = $this->api['terminalID'];
        $claveCifrado = $this->api['claveCifrado'];
        $tipoMoneda = $this->api['tipoMoneda'];
        $exponente = $this->api['exponente'];
        $cifrado = $this->api['cifrado'];
        $idioma = $this->api['idioma'];
        $isProductionMode = $this->api['isProductionMode'];


        $cecaApi = $this->getCecaWrapper() ? $this->getCecaWrapper() : new CecaWrapper($merchantID, $acquirerBIN, $terminalID, $claveCifrado, $tipoMoneda, $exponente, $cifrado, $idioma, $isProductionMode);
        $model = ArrayObject::ensureArrayObject($request->getModel());

        // MAIN CHECK for status after payment capture
        if ($model['external_payment_id'] !== null) {

            /** @var mixed $response */
            $response = $cecaApi->retrieve($model['external_payment_id']);


            if ($response->json['state'] === CecaWrapper::PAID) {
                $model['status'] = CecaWrapper::PAID;
                $request->setModel($model);
            }

            if ($response->json['state'] === CecaWrapper::CANCELED) {
                $model['status'] = CecaWrapper::CANCELED;
                $request->setModel($model);
            }

            if ($response->json['state'] === CecaWrapper::TIMEOUTED) {
                $model['status'] = CecaWrapper::CANCELED;
                $request->setModel($model);
            }

            if ($response->json['state'] === CecaWrapper::CREATED) {
                $model['status'] = CecaWrapper::CANCELED;
                $request->setModel($model);
                return;
            }

            if ($response->json['state'] !== CecaWrapper::CREATED) {
                return;
            }
        }


        /**
         * In case of new the payment scenario
         * @var TokenInterface $token
         */
        $token = $request->getToken();
        $order = $this->prepareOrder($token, $model, $goid);
        $response = $cecaApi->create($order);
        if ($response && $response->hasSucceed()) {
            $model['external_payment_id'] = $response->json['id'];
            $request->setModel($model);
            throw new HttpRedirect($response->json['gw_url']);
        }
        throw CecaException::newInstance($response->statusCode);
    }

    /**
     * @param mixed $request
     * @return boolean
     */
    public function supports($request)
    {
        return
            $request instanceof SetCeca &&
            $request->getModel() instanceof \ArrayObject;
    }

    /**
     * @return Sergiosanchezalvarez\CecaPlugin\CecaWrapperInterface
     */
    public function getCecaWrapper()
    {
        return $this->cecaWrapper;
    }

    /**
     * @param Sergiosanchezalvarez\CecaPlugin\CecaWrapperInterface $cecaWrapper
     */
    public function setCecaWrapper($cecaWrapper)
    {
        $this->cecaWrapper = $cecaWrapper;
    }

    /**
     * Prepare order body for GoPay request
     * @param  TokenInterface $token
     * @param  mixed $model
     * @param  string $goid
     * @return []
     */
    private function prepareOrder(TokenInterface $token, $model, $goid)
    {
        $order = [];
        $order['target']['type'] = 'ACCOUNT';
        $order['target']['goid'] = $goid;
        $order['currency'] = $model['currencyCode'];
        $order['amount'] = $model['totalAmount'];
        $order['order_number'] = $model['extOrderId'];
        $order['lang'] = strtoupper(substr($model['order']->getLocaleCode(), 0, 2));
        /** @var CustomerInterface $customer */
        $customer = $model['customer'];
        Assert::isInstanceOf(
            $customer,
            CustomerInterface::class,
            sprintf(
                'Make sure the first model is the %s instance.',
                CustomerInterface::class
            )
        );
        $payerContact = [
            'email' => (string)$customer->getEmail(),
            'first_name' => (string)$customer->getFirstName(),
            'last_name' => (string)$customer->getLastName(),
        ];
        $order['payer']['contact'] = $payerContact;
        $order['items'] = $this->resolveProducts($model['order']);
        $order['callback']['return_url'] = $token->getTargetUrl();
        $order['callback']['notification_url'] = $token->getTargetUrl();
        return $order;
    }

    /**
     * @param Sylius\Component\Core\Model\Order $order
     * @return []
     */
    private function resolveProducts($order)
    {
        // Set order items array for GoPay body
        if (!array_key_exists('items', $order) || count($order['items']) === 0) {
            $items = [];
            foreach ($order->getItems() as $item) {
                $items[] = [
                    'name' => $item->getVariant()->getInventoryName(),
                    'count' => $item->getQuantity(),
                    'amount' => $item->getTotal()
                ];
            }
        }
        // Set shipping price into body
        if ($order->getShippingTotal() > 0) {
            $items[] = [
                'name' => $order->getShipments()->first()->getMethod()->getName(),
                'count' => 1,
                'amount' => $order->getShippingTotal()
            ];
        }
        return $items;
    }
}