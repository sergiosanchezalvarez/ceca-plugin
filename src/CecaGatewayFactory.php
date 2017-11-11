<?php

namespace Sergiosanchezalvarez\CecaPlugin;

use Sergiosanchezalvarez\CecaPlugin\Action\CaptureAction;
use Sergiosanchezalvarez\CecaPlugin\Action\ConvertPaymentAction;
use Sergiosanchezalvarez\CecaPlugin\Action\NotifyAction;
use Sergiosanchezalvarez\CecaPlugin\Action\StatusAction;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;

/**
 * Class CecaGatewayFactory
 * @package Sergiosanchezalvarez\CecaPlugin
 * @author Sergio Sánchez <sergiosanchezalvarez@gmail.com>
 */
class CecaGatewayFactory extends GatewayFactory {
    /**
     * @param ArrayObject $config
     */
    protected function populateConfig(ArrayObject $config)
    {
        $config->defaults([
            'payum.factory_name' => 'ceca',
            'payum.factory_title' => 'Ceca',
            // Main method for setting up ceca api
            'payum.action.capture' => new CaptureAction(),
            'payum.action.status' => new StatusAction(),
            'payum.action.convert_payment' => new ConvertPaymentAction(),
            'payum.action.notify' => new NotifyAction()
        ]);

        if (false == $config['payum.api']) {
            // Set Ceca default options
            $config['payum.default_options'] = [
                'merchantID' => '',
                'acquirerBIN' => '',
                'terminalID' => '',
                'claveCifrado' => '',
                'isProductionMode' => false
            ];
            $config->defaults($config['payum.default_options']);
            // Set Ceca required fields

            $config['payum.required_options'] = ['merchantID', 'acquirerBIN', 'terminalID', 'claveCifrado'];

            // Set Payum API
            $config['payum.api'] = function (ArrayObject $config) {
                $config->validateNotEmpty($config['payum.required_options']);

                $cecaconfig = [
                    'merchantID' => $config['merchantID'],
                    'acquirerBIN' => $config['acquirerBIN'],
                    'terminalID' => $config['terminalID'],
                    'claveCifrado' => $config['claveCifrado'],
                    'isProductionMode' => $config['isProductionMode'],
                    'timeout' => 30
                ];

                return $cecaconfig;
            };
        }
    }
}