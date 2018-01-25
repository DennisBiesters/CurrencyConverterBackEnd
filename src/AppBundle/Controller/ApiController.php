<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/api")
 */
class ApiController extends Controller
{
    /**
     * @Route("/test")
     */
    public function testAction()
    {
        return new JsonResponse(
            array(
                'text' => 'test',
            )
        );
    }
}
