<?php
declare(strict_types=1);

namespace App\Controller\Users;

use App\Model\User\Entity\User\User;
use App\ReadModel\User\UserFetcher;
use Psr\Log\LoggerInterface;
use App\Model\User\UseCase\Edit;
use App\Model\User\UseCase\Create;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Model\User\UseCase\SignUp\Confirm;

/**
 * Class UsersController
 * @package App\Controller
 * @Route("/users")
 */
class UsersController extends AbstractController
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @Route("/",name="users")
     * return Response
     */
    public function index(Request $request,UserFetcher $fetcher):Response
    {
        $users=$fetcher->all();
        return $this->render('app/users/index.html.twig', compact('users'));
    }

    /**
     * @Route("/create", name="users.create")
     * @param Request $request
     * @param Create\Handler $handler
     * @return Response
     */
    public function create(Request $request, Create\Handler $handler): Response
    {
        $command = new Create\Command();

        $form = $this->createForm(Create\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                return $this->redirectToRoute('users');
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/users/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="users.edit")
     * @param User $user
     * @param Request $request
     * @param Edit\Handler $handler
     * @return Response
     */
    public function edit(User $user, Request $request, Edit\Handler $handler): Response
    {
        $command = Edit\Command::fromUser($user);

        $form = $this->createForm(Edit\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                return $this->redirectToRoute('users.show', ['id' => $user->getId()]);
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/users/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="users.show")
     * @param User $user
     * @return Response
     */
    public function show(User $user): Response
    {
        return $this->render('app/users/show.html.twig', compact('user'));
    }

    /**
     * @Route("/{id}/confirm", name="users.confirm", methods={"POST"})
     * @param User $user
     * @param Request $request
     * @param Confirm\Manual\Handler $handler
     * @return Response
     */
    public function confirm(User $user, Request $request, Confirm\Manual\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('confirm', $request->request->get('token'))) {
            return $this->redirectToRoute('users.show', ['id' => $user->getId()]);
        }

        $command = new Confirm\Manual\Command($user->getId()->getValue());

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('users.show', ['id' => $user->getId()]);
    }
}