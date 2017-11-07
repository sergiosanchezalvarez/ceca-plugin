<?php

namespace Sergiosanchezalvarez\CecaPlugin\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class CecaGatewayConfigurationType
 * @package Sergiosanchezalvarez\CecaPlugin\Form\Type
 * @author Sergio Sánchez <sergiosanchezalvarez@gmail.com>
 */
final class CecaGatewayConfigurationType extends AbstractType {
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('merchantID', TextType::class, [
                'label' => 'sergiosanchezalvarez.ceca_plugin.merchantID',
                'constraints' => [
                    new NotBlank([
                        'message' => 'sergiosanchezalvarez.ceca_plugin.gateway_configuration.merchantID.not_blank',
                    ])
                ]
            ])
            ->add('acquirerBIN', TextType::class, [
                'label' => 'sergiosanchezalvarez.ceca_plugin.acquirerBIN',
                'constraints' => [
                    new NotBlank([
                        'message' => 'sergiosanchezalvarez.ceca_plugin.gateway_configuration.acquirerBIN.not_blank',
                    ])
                ]
            ])
            ->add('terminalID', TextType::class, [
                'label' => 'sergiosanchezalvarez.ceca_plugin.terminalID',
                'constraints' => [
                    new NotBlank([
                        'message' => 'sergiosanchezalvarez.ceca_plugin.gateway_configuration.terminalID.not_blank',
                    ])
                ]
            ])
            ->add('claveCifrado', TextType::class, [
                'label' => 'sergiosanchezalvarez.ceca_plugin.claveCifrado',
                'constraints' => [
                    new NotBlank([
                        'message' => 'sergiosanchezalvarez.ceca_plugin.gateway_configuration.claveCifrado.not_blank',
                    ])
                ]
            ])
            ->add('tipoMoneda', TextType::class, [
                'label' => 'sergiosanchezalvarez.ceca_plugin.tipoMoneda',
                'constraints' => [
                    new NotBlank([
                        'message' => 'sergiosanchezalvarez.ceca_plugin.gateway_configuration.tipoMoneda.not_blank',
                    ])
                ]
            ])
            ->add('exponente', TextType::class, [
                'label' => 'sergiosanchezalvarez.ceca_plugin.exponente',
                'constraints' => [
                    new NotBlank([
                        'message' => 'sergiosanchezalvarez.ceca_plugin.gateway_configuration.exponente.not_blank',
                    ])
                ]
            ])
            ->add('cifrado', TextType::class, [
                'label' => 'sergiosanchezalvarez.ceca_plugin.cifrado',
                'constraints' => [
                    new NotBlank([
                        'message' => 'sergiosanchezalvarez.ceca_plugin.gateway_configuration.cifrado.not_blank',
                    ])
                ]
            ])
            ->add('idioma', TextType::class, [
                'label' => 'sergiosanchezalvarez.idioma.merchantID',
                'constraints' => [
                    new NotBlank([
                        'message' => 'sergiosanchezalvarez.ceca_plugin.gateway_configuration.idioma.not_blank',
                    ])
                ]
            ])
            ->add('pagoSoportado', TextType::class, [
                'label' => 'sergiosanchezalvarez.ceca_plugin.merchantID',
                'constraints' => [
                    new NotBlank([
                        'message' => 'sergiosanchezalvarez.ceca_plugin.gateway_configuration.pagoSoportado.not_blank',
                    ])
                ]
            ])
            ->add('isProductionMode', CheckboxType::class, [
                'label' => 'sergiosanchezalvarez.ceca_plugin.isProductionMode'
            ]);
    }
}

/*
 * isProductionMode:
    MerchantID: Merchant ID
    AcquirerBIN: Acquirer BIN
    TerminalID: Terminal ID
    ClaveCifrado: Clave cifrado
    TipoMoneda: Tipo Moneda
    Exponente: Exponente
    Cifrado: Cifrado
    Idioma: Idioma
    Pago_soportado: Pago soportado
 */