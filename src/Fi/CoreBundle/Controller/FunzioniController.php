<?php

namespace Fi\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Fi\CoreBundle\Utils\GrigliaFiltriUtils;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class FunzioniController extends Controller
{

    public function traduzionefiltroAction(Request $request)
    {
        $tuttofiltri = $request->query->get('filters');

        return new Response(GrigliaFiltriUtils::traduciFiltri(array('filtri' => json_decode($tuttofiltri))));
    }
}
