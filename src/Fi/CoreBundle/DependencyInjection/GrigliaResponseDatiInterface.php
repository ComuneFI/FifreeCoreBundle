<?php

namespace Fi\CoreBundle\DependencyInjection;

/**
 * @author manzolo
 */
interface GrigliaResponseDatiInterface
{
    /**
     * Get response.
     *
     * @return string
     */
    public function getResponse();
}
