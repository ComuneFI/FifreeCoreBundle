<?php

namespace Fi\CoreBundle\Twig\Extension;

use Fi\CoreBundle\Controller\FiUtilita;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;
class UtilitaExtension extends AbstractExtension
{

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new TwigFunction('db2data', array($this, 'getDb2data', 'is_safe' => array('html'))),
        );
    }
    public function getDb2data($giorno)
    {
        // highlight_string highlights php code only if '<?php' tag is present.

        return FiUtilita::db2data($giorno, true);
    }
}
