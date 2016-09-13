<?php

namespace Fi\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FunzioniController extends FiController
{
    public function traduzionefiltroAction(Request $request)
    {
        $tuttofiltri = $request->query->get('filters');

        return new Response(Griglia::traduciFiltri(array('filtri' => json_decode($tuttofiltri))));
    }
}
