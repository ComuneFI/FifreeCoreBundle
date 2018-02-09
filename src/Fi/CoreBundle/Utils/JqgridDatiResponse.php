<?php

namespace Fi\CoreBundle\Utils;

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
