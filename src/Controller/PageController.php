<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Photo;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    #[Route('/', name: 'gallery')]
    public function index(EntityManagerInterface $em): Response
    {
        $photos = $em->getRepository(Photo::class)->findAll();
        $categories = $em->getRepository(Category::class)->findAll();

        return $this->render('pages/index.html.twig', [
            'categories' => $categories,
            'photos' => $photos,
        ]);
    }

    #[Route('/photo/{id}', name: 'photo_show')]
    public function show(Photo $photo): Response
    {
        return $this->render('pages/photo.html.twig', [
            'photo' => $photo,
        ]);
    }

    #[Route('/about-me', name: 'about_me')]
    public function about(): Response
    {
        return $this->render('pages/about_me.html.twig');
    }

    #[Route('/contact', name: 'contact')]
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createForm(ContactType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $email = (new Email())
                ->from($data['email'])
                ->to('quentin.lemoine62580@gmail.com')
                ->subject('Contact Joode Rock site')
                ->text(
                    'You received a new message from ' . $data['fullName'] . "\n\n" .
                    'Phone Number: ' . $data['phoneNumber'] . "\n\n" .
                    'Message: ' . $data['message']
                );

            $mailer->send($email);

            $this->addFlash('success', 'Your message has been sent successfully.');

            return $this->redirectToRoute('contact');
        }

        return $this->render('pages/contact.html.twig', [
            'contactForm' => $form->createView(),
        ]);
    }
}