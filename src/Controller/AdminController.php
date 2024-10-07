<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Photo;
use App\Entity\Category;
use App\Entity\Settings;
use App\Form\PhotoType;
use App\Form\CategoryType;
use App\Form\SettingsType;
use App\Service\S3Service;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use function uniqid;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'admin_dashboard')]
    public function dashboard(): Response
    {
        return $this->redirectToRoute('admin_photos');
    }

    #[Route('/photos', name: 'admin_photos')]
    public function indexPhotos(EntityManagerInterface $em): Response
    {
        $photos = $em->getRepository(Photo::class)->findAll();

        return $this->render('admin/photo/index.html.twig', [
            'photos' => $photos,
        ]);
    }

    #[Route('/photos/add', name: 'admin_photos_add')]
    public function addPhoto(Request $request, EntityManagerInterface $em, S3Service $s3Service): Response
    {
        $photo = new Photo();
        $form = $this->createForm(PhotoType::class, $photo);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                $fileName = uniqid() . '.' . $imageFile->guessExtension();

                // Upload to S3
                $imagePath = $s3Service->upload($imageFile->getPathname(), $fileName);

                $photo->setImagePath($imagePath);
                $em->persist($photo);
                $em->flush();

                return $this->redirectToRoute('admin_photos');
            }
        }

        return $this->render('admin/photo/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/photos/delete/{id}', name: 'admin_photos_delete')]
    public function deletePhoto(Photo $photo, EntityManagerInterface $em, S3Service $s3Service): Response
    {
        $key = basename($photo->getImagePath());

        $s3Service->delete($key);

        $em->remove($photo);
        $em->flush();

        return $this->redirectToRoute('admin_photos');
    }

    #[Route('/categories', name: 'admin_categories')]
    public function indexCategories(EntityManagerInterface $em): Response
    {
        $categories = $em->getRepository(Category::class)->findAll();

        return $this->render('admin/category/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/categories/add', name: 'admin_categories_add')]
    public function addCategory(Request $request, EntityManagerInterface $em): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute('admin_categories');
        }

        return $this->render('admin/category/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/categories/delete/{id}', name: 'admin_categories_delete')]
    public function deleteCategory(Category $category, EntityManagerInterface $em): Response
    {
        if (!$category->getPhotos()->isEmpty()) {
            $this->addFlash('error', 'Vous ne pouvez pas supprimer une catégorie qui contient des photos.');
            return $this->redirectToRoute('admin_categories');
        }

        $em->remove($category);
        $em->flush();

        return $this->redirectToRoute('admin_categories');
    }

    #[Route('/settings', name: 'admin_settings')]
    public function settings(Request $request, EntityManagerInterface $em, S3Service $s3Service): Response
    {
        $settings = $em->getRepository(Settings::class)->findOneBy([]);

        if (!$settings) {
            $settings = new Settings();
        }

        $form = $this->createForm(SettingsType::class, $settings);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('deleteImage')->getData()) {
                $settings->setImage(null);
            }

            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                $fileName = uniqid() . '.' . $imageFile->guessExtension();

                $imagePath = $s3Service->upload($imageFile->getPathname(), $fileName);

                $settings->setImagePath($imagePath);
                $em->persist($settings);
                $em->flush();

                $this->addFlash('success', 'Les paramètres ont été mis à jour avec succès.');
            }

            return $this->redirectToRoute('admin_settings');
        }

        return $this->render('admin/settings.html.twig', [
            'settings' => $settings,
            'form' => $form->createView(),
        ]);
    }
}
