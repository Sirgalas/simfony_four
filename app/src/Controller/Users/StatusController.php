<?php
declare(strict_types=1);

namespace App\Controller\Users;

use App\Model\User\Entity\User\User;
use App\Model\User\UseCase\Activate;
use App\Model\User\UseCase\Block;
use App\Controller\ErrorHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
/**
 * @Route("/users/status", name="users.status")
 * @IsGranted ("ROLE_MANAGE_USERS")
 */
class StatusController extends AbstractController
{
    private $errors;

    public function __construct(ErrorHandler $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @Route("/{id}/activate", name=".activate", methods={"POST"})
     * @param User $user
     * @param Request $request
     * @param Activate\Handler $handler
     * @return Response
     */
    public function activate(User $user,Request $request, Activate\Handler $handler):Response
    {
        if (!$this->isCsrfTokenValid('activate', $request->request->get('token'))) {
            return $this->redirectToRoute('users.show', ['id' => $user->getId()]);
        }
        $command = new Activate\Command($user->getId()->getValue());

        try {
            $handler->handle($command);
        }catch (\DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());
        }
        return $this->redirectToRoute('users.show',['id'=>$user->getId()]);
    }

    /**
     * @Route("/{id}/block", name=".block", methods={"POST"})
     * @param User $user
     * @param Request $request
     * @param Block\Handler $handler
     * @return Response
     */
    public function block(User $user,Request $request,Block\Handler $handler):Response
    {
        if (!$this->isCsrfTokenValid('block', $request->request->get('token'))) {
            return $this->redirectToRoute('users.show', ['id' => $user->getId()]);
        }

        if ($user->getId()->getValue() === $this->getUser()->getId()) {
            $this->addFlash('error', 'Unable to block yourself.');
            return $this->redirectToRoute('users.show', ['id' => $user->getId()]);
        }

        $command= new Block\Command($user->getId()->getValue());

        try{
            $handler->handle($command);
        }catch(\DomainException $e){
            $this->errors->warning($e->getMessage(),['exception'=>$e]);
            $this->addFlash('error',$e->getMessage());
        }
        return $this->redirectToRoute('users.show', ['id' => $user->getId()]);
    }
}