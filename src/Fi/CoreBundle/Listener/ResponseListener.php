<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ResponseListener
 *
 * @author d59495
 */

namespace Fi\CoreBundle\Listener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class ResponseListener
{
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $securityProtection = "default-src 'self' http://*.comune.fi.it http://*.comune.intranet https:;"
                . "script-src 'self' http://*.comune.fi.it http://*.comune.intranet https: 'unsafe-inline' 'unsafe-eval' ;"
                . "style-src   'self' http://*.comune.fi.it http://*.comune.intranet https: 'unsafe-inline' ;"
                . "img-src     'self' http://*.comune.fi.it http://*.comune.intranet http://a.tile.openstreetmap.org data: https: ;"
                . "object-src  'self' http://*.comune.fi.it http://*.comune.intranet ;"
                . "media-src   'self' http://*.comune.fi.it http://*.comune.intranet ;"
                . "child-src   'self' http://*.comune.fi.it http://*.comune.intranet https:;"
                . "form-action 'self' http://*.comune.fi.it http://*.comune.intranet https: ;"
                . "frame-ancestors 'self' http://*.comune.fi.it http://*.comune.intranet https: ;";

        $event->getResponse()->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $event->getResponse()->headers->set('X-Content-Type-Options', 'nosniff');
        $event->getResponse()->headers->set('X-XSS-Protection', '1; mode=block');
        $event->getResponse()->headers->set('Content-Security-Policy', $securityProtection);
        $event->getResponse()->headers->set('X-Content-Security-Policy', $securityProtection);
    }
}
