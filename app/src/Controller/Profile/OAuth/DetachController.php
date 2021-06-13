<?php
declare(strict_types=1);

namespace App\Controller\Profile\OAuth;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Model\User\UseCase\Network\Detach\Command;
use App\Model\User\UseCase\Network\Detach\Handler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DetachController
 * @package App\Controller\Profile\OAuth
 * @Route ("/profile/oauth/detach")
 */
class DetachController extends AbstractController
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @Route("/{network}/{identity}", name="profile.oauth.detach", methods={"DELETE"})
     * @param Request $request
     * @param string $network
     * @param string $identity
     * @param Handler $handler
     * @return Response
     */

    public function detach(Request $request,string $network, string  $identity, Handler $handler)
    {
        if(!$this->isCsrfTokenValid('delete',$request->request->get('token'))){
            return $this->redirectToRoute('profile');
        }
        $command= new Command(
            $this->getUser()->getId(),
            $network,
            $identity);

        try {
            $handler->handle($command);
            return $this->redirectToRoute('profile');
        }catch (\DomainException $e) {
            $this->logger->warning($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('profile');
        }
    }
}