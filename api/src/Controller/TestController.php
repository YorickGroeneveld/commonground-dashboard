<?php

// src/Controller/DashboardController.php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use App\Service\CommonGroundService;

/**
 * Class TestController
 * @package App\Controller
 * @Route("/test")
 */
class TestController extends AbstractController
{

    /**
     * @Route("/")
     * @Template
     */
	public function indexAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService)
    {
        $babsschets = "";

        $h1 = "test";

        $functie = "functie";

    	return ["babsschets"=>$babsschets, "h1"=>$h1, "functie"=>$functie];
    }

    /**
     * @Route("/subpage")
     * @Template
     */
    public function subpageAction()
    {
        $babsschets = "";

        $h1 = "test";

        $functie = "functie";

        return ["babsschets"=>$babsschets, "h1"=>$h1, "functie"=>$functie];
    }
}
