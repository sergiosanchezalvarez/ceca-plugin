<?php

namespace Sergiosanchezalvarez\CecaPlugin;

use Sergiosanchezalvarez\CecaPlugin\Action\CaptureAction;
use Sergiosanchezalvarez\CecaPlugin\Action\ConvertPaymentAction;
use Sergiosanchezalvarez\CecaPlugin\Action\NotifyAction;
use Sergiosanchezalvarez\CecaPlugin\Action\CecaAction;
use Sergiosanchezalvarez\CecaPlugin\Action\StatusAction;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;

/**
 * Class CecaGatewayFactory
 * @package Sergiosanchezalvarez\CecaPlugin
 * @author Sergio Sánchez <sergiosanchezalvarez@gmail.com>
 */
class CecaGatewayFactory extends GatewayFactory {
    protected function populateConfig(ArrayObject $config)
    {
        $config->defaults([
            'payum.factory_name' => 'ceca',
            'payum.factory_title' => 'Ceca',
            // Main method for setting up gopay api
            'payum.action.set_ceca' => new CecaAction(),
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
                'tipoMoneda' => '',
                'exponente' => '',
                'cifrado' => '',
                'idioma' => '',
                'isProductionMode' => false
            ];
            $config->defaults($config['payum.default_options']);
            // Set Ceca required fields

            $config['payum.required_options'] = ['merchantID', 'acquirerBIN', 'terminalID', 'claveCifrado', 'tipoMoneda', 'exponente', 'cifrado', 'idioma', 'pagoSoportado'];
            // Set Payum API
            $config['payum.api'] = function (ArrayObject $config) {
                $config->validateNotEmpty($config['payum.required_options']);

                $cecaconfig = [
                    'merchantID' => $config['merchantID'],
                    'acquirerBIN' => $config['acquirerBIN'],
                    'terminalID' => $config['terminalID'],
                    'claveCifrado' => $config['claveCifrado'],
                    'tipoMoneda' => $config['tipoMoneda'],
                    'exponente' => $config['exponente'],
                    'cifrado' => $config['cifrado'],
                    'idioma' => $config['idioma'],
                    'pagoSoportado' => $config['pagoSoportado'],
                    'timeout' => 30
                ];

                return $cecaconfig;
            };
        }
    }
}