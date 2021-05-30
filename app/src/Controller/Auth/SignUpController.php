<?php


namespace App\Controller\Auth;

use App\Model\User\UseCase\SignUp;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class SignUpController extends AbstractController
{
    private $logger;

    /**
     * @var TranslatorInterface
     */
    private $translator;


    public function __construct(LoggerInterface $logger, TranslatorInterface $translator)
    {
        $this->logger=$logger;
        $this->translator = $translator;
    }

    /**
     * @Route("/signup", name="auth.signup")
     * @param Request $request
     * @param SignUp\Request\Handler $handler
     * @return Response
     */
    public function request(Request $request,SignUp\Request\Handler $handler):Response
    {
        $command= new SignUp\Request\Command();
        $form = $this->createForm(SignUp\Request\Form::class,$command);
        $form->handleRequest($request);
        if($form->isSubmitted()&&$form->isValid()){
            try {
                $handler->handle($command);
                $this->addFlash('success',$this->translator->trans('Check your email.',[],'success'));
                return $this->redirectToRoute('home');
            }catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $this->translator->trans($e->getMessage(),[],'exceptions'));
            }
        }
        return $this->render('app/auth/auth.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/signup/{token}", name="auth.signup.confirm")
     * @param string $token
     * @param SignUp\Confirm\Handler $handler
     * @return Response
     */
    public function confirm(string $token, SignUp\Confirm\Handler $handler):Response
    {
        $command = new SignUp\Confirm\Command($token);
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
