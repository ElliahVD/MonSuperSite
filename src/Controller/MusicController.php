<?php

namespace App\Controller;

use App\Entity\Music;
use App\Form\MusicFormType;
use App\Repository\MusicRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MusicController extends AbstractController
{


//    Afficher liste des musiques
    #[Route('/music', name: 'app_music')]
    public function listeMusic(MusicRepository $musicRepository): Response
    {
        $musics = $musicRepository->findAll();
        return $this->render('music/index.html.twig', [
            'musics' => $musics,
        ]);
    }


//    Création nouvelle musique
    #[Route('/music/new', name: 'app_music_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $music = new Music();
        $form = $this->createForm(MusicFormType::class, $music);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $music->setCreatedAt(new \DateTimeImmutable());
            $entityManager->persist($music);
            $entityManager->flush();
            $this->addFlash('sucess', 'La chanson ' . $music->getTitle() . ' est ajoutée');
            return $this->render('app_music');
        }

        return $this->render('music/new.html.twig', [
            'musicForm' => $form->createView(),

        ]);
    }

    //  Afficher le détail d'une musique
    #[Route('/musique/{id}', name: 'app_music_details')]
    public function details(Music $music): Response
    {

        return $this->render('music/details.html.twig', [
            'music' => $music

        ]);
    }

//   Editer une musique
    #[Route('/music/{id}/edit', name: 'app_music_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager, Music $music): Response
    {
        $form = $this->createForm(MusicFormType::class, $music);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('sucess', 'La chanson ' . $music->getName() . ' est éditée');
            return $this->render('app_music_edit');
        }

        return $this->render('music/edit.html.twig', [
            'musicForm' => $form->createView(),
            'music' => $music,
        ]);
    }

//    Supprimer une musique
    #[Route('/music/{id}/delete', name: 'app_music_delete')]
    public function deleteMusic(EntityManagerInterface $entityManager, Music $music)
    {
        $entityManager->remove($music);
        $entityManager->flush();

        return $this->redirectToRoute('app_music');
    }
}