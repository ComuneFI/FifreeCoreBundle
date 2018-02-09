<?php

namespace Fi\CoreBundle\Utils;

/**
 * Description of JqgridResponse.
 *
 * @author manzolo
 */
abstract class JqgridTestataResponse implements GrigliaResponseTestataInterface
{
    private $risposta;

    public function __construct($vettorerisposta)
    {
        $this->risposta = $vettorerisposta;
    }

    public function getResponse()
    {
        return $this->risposta;
    }
}
