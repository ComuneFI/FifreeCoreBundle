<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Fi\CoreBundle\DependencyInjection;

/**
 * Description of JqgridResponse.
 *
 * @author manzolo
 */
abstract class JqgridDatiResponse implements GrigliaResponseDatiInterface
{
    private $risposta;

    public function __construct($vettorerisposta)
    {
        $this->risposta = $vettorerisposta;
    }

    public function getResponse()
    {
        return json_encode($this->risposta);
    }
}
