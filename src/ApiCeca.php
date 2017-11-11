<?php

namespace Sergiosanchezalvarez\CecaPlugin;

use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\InvalidArgumentException;
use Payum\Core\Exception\LogicException;
use Exception;

/**
 * Class Api
 * @package Sergiosanchezalvarez\CecaPlugin\Ceca
 * @author Sergio Sánchez <sergiosanchezalvarez@gmail.com>
 */
class ApiCeca {

    const PAGO_SOPORTADO = 'SSL';

    const TIPO_MONEDA = 978;

    const EXPONENTE = 2;

    const CIFRADO = 'SHA1';

    const IDIOMA = 1;

    const URL_PRO = 'https://pgw.ceca.es/cgi-bin/tpv';

    const URL_SANDBOX = 'http://tpv.ceca.es:8000/cgi-bin/tpv';

    private $options = array(
        'TipoMoneda' => self::TIPO_MONEDA,
        'Exponente' => self::EXPONENTE,
        'Cifrado' => self::CIFRADO,
        'Idioma' => self::IDIOMA,
        'Pago_soportado' => self::PAGO_SOPORTADO
    );

    private $o_required = array('Environment', 'ClaveCifrado', 'MerchantID', 'AcquirerBIN', 'TerminalID', 'TipoMoneda', 'Exponente', 'Cifrado', 'Pago_soportado');
    private $o_optional = array('Idioma', 'Descripcion', 'URL_OK', 'URL_NOK', 'Tipo_operacion', 'Datos_operaciones');

    private $environment = '';
    private $environments = array(
        'test' => self::URL_SANDBOX,
        'real' => self::URL_PRO
    );

    private $success = '$*$OKY$*$';

    private $values = array();
    private $hidden = array();

    /**
     * Api constructor.
     * @param array $options
     */
    public function __construct(array $options)
    {
        $options = array_merge($this->options, $options);
        return $this->setOption($options);
    }

    public function getHola()
    {
        return 'Hola';
    }

    /**
     * @param $option
     * @param null $value
     * @return $this
     * @throws Exception
     */
    public function setOption($option, $value = null)
    {
        if (is_array($option)) {
            $options = $option;
        } elseif ($value !== null) {
            $options = array($option => $value);
        } else {
            throw new Exception(sprintf('Option <strong>%s</strong> can not be empty', $option));
        }

        $options = array_merge($this->options, $options);

        foreach ($this->o_required as $option) {
            if (empty($options[$option])) {
                throw new Exception(sprintf('Option <strong>%s</strong> is required', $option));
            }
            $this->options[$option] = $options[$option];
        }

        foreach ($this->o_optional as $option) {
            if (array_key_exists($option, $options)) {
                $this->options[$option] = $options[$option];
            }
        }

        if (isset($options['environments'])) {
            $this->environments = array_merge($this->environments, $options['environments']);
        }

        $this->setEnvironment($options['Environment']);

        return $this;
    }

    /**
     * @param null $key
     * @return array|mixed
     */
    public function getOption($key = null)
    {
        return $key ? $this->options[$key] : $this->options;
    }

    /**
     * @param $mode
     * @return $this
     */
    public function setEnvironment($mode)
    {
        $this->environment = $this->getEnvironments($mode);
        return $this;
    }

    /**
     * @param string $path
     * @return string
     */
    public function getPath($path = '')
    {
        return $this->environment.$path;
    }

    /**
     * @param null $key
     * @return array|mixed
     * @throws Exception
     */
    public function getEnvironments($key = null)
    {
        if (empty($this->environments[$key])) {
            $envs = implode('|', array_keys($this->environments));
            throw new Exception(sprintf('Environment <strong>%s</strong> is not valid [%s]', $key, $envs));
        }

        return $key ? $this->environments[$key] : $this->environments;
    }

    /**
     * @param array $options
     * @return $this
     */
    public function setFormHiddens(array $options)
    {
        $this->hidden = $this->values = array();

        //$options['Importe'] = $this->getAmount($options['Importe']);

        $this->setValueDefault($options, 'MerchantID', 9);
        $this->setValueDefault($options, 'AcquirerBIN', 10);
        $this->setValueDefault($options, 'TerminalID', 8);
        $this->setValueDefault($options, 'TipoMoneda');
        $this->setValueDefault($options, 'Exponente');
        $this->setValueDefault($options, 'Cifrado');
        $this->setValueDefault($options, 'Pago_soportado');
        $this->setValueDefault($options, 'Idioma');

        $this->setValue($options, 'Num_operacion');
        $this->setValue($options, 'Importe');
        $this->setValue($options, 'URL_OK');
        $this->setValue($options, 'URL_NOK');
        $this->setValue($options, 'Descripcion');
        $this->setValue($options, 'Tipo_operacion');
        $this->setValue($options, 'Datos_operaciones');

        $this->setValueLength('MerchantID', 9);
        $this->setValueLength('AcquirerBIN', 10);
        $this->setValueLength('TerminalID', 8);

        $options['Firma'] = $this->getSignature();

        $this->setValue($options, 'Firma');
        $this->setHiddensFromValues();

        return $this;
    }

    /**
     * @param $key
     * @param $length
     * @return $this
     */
    private function setValueLength($key, $length)
    {
        $this->values[$key] = str_pad($this->values[$key], $length, '0', STR_PAD_LEFT);
        return $this;
    }

    /**
     * @return $this
     */
    private function setHiddensFromValues()
    {
        $this->hidden = $this->values;
        return $this;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getFormHiddens()
    {
        if (empty($this->hidden)) {
            throw new Exception('Form fields must be initialized previously');
        }

        $html = '';
        foreach ($this->hidden as $field => $value) {
            $html .= "\n".'<input type="hidden" name="'.$field.'" value="'.$value.'" />';
        }

        return trim($html);
    }

    /**
     * @param array $options
     * @param $option
     * @return $this
     */
    private function setValueDefault(array $options, $option)
    {
        if (isset($options[$option])) {
            $this->values[$option] = $options[$option];
        } elseif (isset($this->options[$option])) {
            $this->values[$option] = $this->options[$option];
        }

        return $this;
    }

    /**
     * @param array $options
     * @param $option
     * @return $this
     */
    private function setValue(array $options, $option)
    {
        if (isset($options[$option])) {
            $this->values[$option] = $options[$option];
        }

        return $this;
    }

    /**
     * @param $amount
     * @return mixed|string
     */
    public function getAmount($amount)
    {
        if (empty($amount)) {
            return '000';
        } elseif (preg_match('/[\.,]/', $amount)) {
            return str_replace(array('.', ','), '', $amount);
        } else {
            return ($amount * 100);
        }
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getSignature()
    {
        $fields = array('MerchantID', 'AcquirerBIN', 'TerminalID', 'Num_operacion', 'Importe', 'TipoMoneda', 'Exponente', 'Cifrado', 'URL_OK', 'URL_NOK');
        $key = '';

        foreach ($fields as $field) {
            if (!isset($this->values[$field])) {
                throw new Exception(sprintf('Field <strong>%s</strong> is empty and is required to create signature key', $field));
            }
            $key .= $this->values[$field];
        }

        return sha1($this->options['ClaveCifrado'].$key);
    }

    /**
     * @param array $post
     * @return mixed
     * @throws Exception
     */
    public function checkTransaction(array $post)
    {
        if (empty($post) || empty($post['Firma'])) {
            throw new Exception('_POST data is empty');
        }

        $fields = array('MerchantID', 'AcquirerBIN', 'TerminalID', 'Num_operacion', 'Importe', 'TipoMoneda', 'Exponente', 'Referencia');
        $key = '';
        foreach ($fields as $field) {
            if (empty($post[$field])) {
                throw new Exception(sprintf('Field <strong>%s</strong> is empty and is required to verify transaction'));
            }
            $key .= $post[$field];
        }

        $signature = sha1($this->options['ClaveCifrado'].$key);
        if ($signature !== $post['Firma']) {
            throw new Exception(sprintf('Signature not valid (%s != %s)', $signature, $post['Firma']));
        }

        return $post['Firma'];
    }

    /**
     * @return string
     */
    public function successCode()
    {
        return $this->success;
    }

    public function getValues()
    {
        return $this->values;
    }

    public function getCurrentEnviroment()
    {
        return $this->environment;
    }

}