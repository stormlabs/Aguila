<?php

namespace Storm\AguilaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Static controller.
 *
 * @Route("/site")
 */
class StaticController extends Controller
{

    /**
     * Show css examples
     *
     * @Route("/typo", name="aguila_static_typo")
     * @Template()
     */
    public function typoAction()
    {
        return array();
    }
}
