<?php


namespace App\Controller\Auth;

use App\Model\User\UseCase\SignUp;
use App\ReadModel\User\UserFetcher;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Security\LoginFormAuthenticator;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class SignUpController extends AbstractController
{
    private $logger;

    /**
     * @var TranslatorInterface
     */
    private $translator;
    private UserFetcher $users;


    public function __construct(UserFetcher $users, LoggerInterface $logger, TranslatorInterface $translator)
    {
        $this->logger=$logger;
        $this->translator = $translator;
        $this->users = $users;
    }

    /**
     * @Route("/signup", name="auth.signup")
     * @param Request $request
     * @param SignUp\Request\Handler $handler
     * @return Response
     */
    public function request(Request $request, SignUp\Request\Handler $handler): Response
    {
        $command = new SignUp\Request\Command();

        $form = $this->createForm(SignUp\Request\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Check your email.');
                return $this->redirectToRoute('home');
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/auth/signup.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/signup/{token}", name="auth.signup.confirm")
     * @param string $token
     * @param \App\Model\User\UseCase\SignUp\Confirm\ByToken\Handler $handler
     * @return Response
     */
    public function confirm(string $token, SignUp\Confirm\ByToken\Handler $handler):Response
    {
        $command = new SignUp\Confirm\ByToken\Command($token);
        try{
            $handler->handle($command);
            $this->addFlash('success',$this->translator->trans('Email is successfully confirmed',[],'success'));
        }catch (\DomainException $exception){
            $this->logger->error($exception->getMessage(),['exception'=>$exception]);
            $this->addFlash('error',$this->translator->trans($exception->getMessage(),[],'exceptions'));
        }
        return $this->redirectToRoute('home');
    }
}
