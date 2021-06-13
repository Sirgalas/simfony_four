<?php
declare(strict_types=1);

namespace App\Controller\Users;

use App\Model\User\Entity\User\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Model\User\UseCase\Role;
use App\ReadModel\User\UserFetcher;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
/**
 * Class UsersController
 * @package App\Controller
 * @Route("/user/role")
 * @IsGranted ("ROLE_MANAGE_USERS")
 */
class RoleController extends AbstractController
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @Route ("/{id}", name="users.role")
     * @param User $user
     * @param Request $request
     * @param Role\Handler $handler
     * @return Response
     */
    public function role(User $user,Request $request,Role\Handler $handler):Response
    {
        if ($user->getId()->getValue() === $this->getUser()->getId()) {
            $this->addFlash('error', 'Unable to change role for yourself.');
            return $this->redirectToRoute('users.show', ['id' => $user->getId()]);
        }

        $command= Role\Command::fromUser($user);
        $form=$this->createForm(Role\Form::class,$command);

        if($form->isSubmitted() && $form->isValid()){
            try{
                $handler->handle($command);
                return $this->redirectToRoute('users.show',['id'=>$user->getId()]);
            }catch (\DomainException $e){
                $this->logger->warning($e->getMessage(),['exception'=>$e]);
                $this->addFlash('error',$e->getMessage());
            }
        }
        return $this->render('app/users/role.html.twig',[
            'user'=>$user,
            'form'=>$form->createView()
        ]);
    }
}