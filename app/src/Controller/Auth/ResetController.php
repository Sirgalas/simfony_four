<?php


namespace App\Controller\Auth;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Model\User\UseCase\Reset;
use App\ReadModel\User\UserFetcher;
use App\Controller\ErrorHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ResetController extends AbstractController
{
    private $errors;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(ErrorHandler $errors,TranslatorInterface $translator)
    {
        $this->errors=$errors;
        $this->translator = $translator;
    }

    /**
     * @Route ("/reset", name="auth.reset")
     * @param Request $request
     * @param Reset\Request\Handler $handler
     * @return Response
     */
    public function request(Request $request,Reset\Request\Handler $handler):Response
    {
        $command= new Reset\Request\Command();
        $form= $this->createForm(Reset\Request\Form::class,$command);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            try{
                $handler->handle($command);
                $this->addFlash('success',$this->translator->trans('Check your email.',[],'success'));
                return $this->redirectToRoute('home');
            }catch (\DomainException $e){
                $this->errors->handle($e);
                $this->addFlash('error', $this->translator->trans($e->getMessage(),[],'exceptions'));
            }
        }
        return $this->render('app/auth/reset/request.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/reset/{token}", name="auth.reset.reset")
     * @param string $token
     * @param Request $request
     * @param Reset\Reset\Handler $handler
     * @param UserFetcher $users
     * @return Response
     */
    public function reset(string $token,Request $request,Reset\Reset\Handler $handler, UserFetcher $user):Response
    {
        if(!$user->existsByResetToken($token)){
            $this->addFlash('error',$this->translator->trans('Incorrect or already confirmed token.',[],'exceptions'));
            return $this->redirectToRoute('home');
        }
        $command=new Reset\Reset\Command($token);
        $form=$this->createForm(Reset\Reset\Form::class,$command);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            try {
                $handler->handle($command);
                $this->addFlash('success',$this->translator->trans('Password is successfully changed.'));
                return $this->redirectToRoute('home');
            } catch(\DomainException $e){
                $this->errors->warning($e->getMessage(),['exception'=>$e]);
                $this->addFlash('error',$this->translator->trans($e->getMessage(),[],'exceptions'));
            }
        }
        return $this->render('app/auth/reset/reset.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
