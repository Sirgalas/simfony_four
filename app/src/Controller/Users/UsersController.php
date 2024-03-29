<?php
declare(strict_types=1);

namespace App\Controller\Users;

use App\Model\User\Entity\User\User;
use App\ReadModel\User\Filter;
use App\ReadModel\User\UserFetcher;
use App\ReadModel\Work\Members\Member\MemberFetcher;
use App\Controller\ErrorHandler;
use App\Model\User\UseCase\Edit;
use App\Model\User\UseCase\Create;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Model\User\UseCase\SignUp\Confirm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class UsersController
 * @package App\Controller
 * @Route("/users", name="users")
 */
class UsersController extends AbstractController
{
    private const PER_PAGE=50;
    private ErrorHandler $errors;

    public function __construct(ErrorHandler $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @Route("/",name="")
     * return Response
     */
    public function index(Request $request,UserFetcher $fetcher):Response
    {
        $filter = new Filter\Filter();
        $form = $this->createForm(Filter\Form::class, $filter);
        $form->handleRequest($request);
        $users=$fetcher->all(
            $filter,
            $request->query->getInt('page', 1),
            self::PER_PAGE,
            $request->query->get('sort', 'created_at'),
            $request->query->get('direction', 'desc')
        );
        return $this->render('app/users/index.html.twig',
            [
                'users' => $users,
                'form' => $form->createView()
            ]);
    }

    /**
     * @Route("/create", name=".create")
     * @param Request $request
     * @param Create\Handler $handler
     * @return Response
     * @IsGranted("ROLE_MANAGE_USERS")
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
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/users/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name=".edit")
     * @param User $user
     * @param Request $request
     * @param Edit\Handler $handler
     * @return Response
     * @IsGranted("ROLE_MANAGE_USERS")
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
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/users/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name=".show")
     * @param User $user
     * @return Response
     * @IsGranted("ROLE_MANAGE_USERS")
     */
    public function show(User $user, MemberFetcher $members): Response
    {
        $member = $members->find($user->getId()->getValue());
        return $this->render('app/users/show.html.twig', compact('user','member'));
    }

    /**
     * @Route("/{id}/confirm", name=".confirm", methods={"POST"})
     * @param User $user
     * @param Request $request
     * @param Confirm\Manual\Handler $handler
     * @return Response
     * @IsGranted("ROLE_MANAGE_USERS")
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
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('users.show', ['id' => $user->getId()]);
    }
}