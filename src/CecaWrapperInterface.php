<?php

namespace Sergiosanchezalvarez\CecaPlugin;

interface CecaWrapperInterface {
    /**
     * Create reuqest for Ceca
     * @param $config
     * @return mixed
     */
    public function create($config);

    /**
     * Get status of the payment based on given Ceca payment ID
     * @param $paymentID
     * @return mixed
     */
    public function retrieve($paymentID);
}