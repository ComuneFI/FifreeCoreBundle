<?php

namespace Fi\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Fi\CoreBundle\DependencyInjection\GrigliaUtils;

class FunzioniController extends FiController
{
    public function traduzionefiltroAction(Request $request)
    {
        $tuttofiltri = $request->query->get('filters');

        return new Response(GrigliaUtils::traduciFiltri(array('filtri' => json_decode($tuttofiltri))));
    }
}
