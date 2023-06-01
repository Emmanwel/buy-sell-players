<?php

namespace App\Controller;

use App\Entity\Team;
use App\Entity\Player;
use App\Form\TeamFormType;
use App\Repository\TeamRepository;
use App\Repository\PlayerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;




class TeamsController extends AbstractController
{
    private $teamRepository;

    private $playerRepository;
    private $em;
    private $paginator;

    public function __construct(EntityManagerInterface $em, TeamRepository  $teamRepository, PlayerRepository  $playerRepository, PaginatorInterface $paginator)
    {
        $this->teamRepository = $teamRepository;
        $this->playerRepository = $playerRepository;
        $this->em = $em;
        $this->paginator = $paginator;
    }

    #[Route('/teams', methods: ['GET'], name: 'app_teams')]

    public function index(PaginatorInterface $paginator, Request $request): Response
    {

        $players = $this->playerRepository->findAll();
        $teams = $this->teamRepository->findAll();

        // Paginate the players data
        $pagination = $paginator->paginate(
            $teams,
            $request->query->getInt('page', 1),
            6 // Number of players to display per page
        );

        return $this->render('teams/index.html.twig', [
            'teams' => $teams,
            'players' => $players,
            'pagination' => $pagination
        ]);
    }




    #[Route('/teams/create', methods: ["GET", "POST"], name: 'create_team')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $team = new Team();
        $form = $this->createForm(TeamFormType::class, $team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newTeam = $form->getData();

            $teamLogo = $form->get('team_logo')->getData();
            if ($teamLogo) {
                $newFileName = uniqid() . '.' . $teamLogo->guessExtension();

                try {
                    $teamLogo->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads',
                        $newFileName
                    );
                } catch (FileException $e) {
                    return new Response($e->getMessage());
                }

                $newTeam->setTeamLogo('/uploads/' . $newFileName);
            }

            // Handle player fields
            $playersData = $form->get('players')->getData();
            $existingPlayers = [];

            foreach ($playersData as $playerData) {
                if ($playerData->getId()) {
                    // Existing player, update the player entity
                    $existingPlayer = $entityManager->getRepository(Player::class)->find($playerData->getId());
                    $existingPlayer->setPlayerName($playerData->getPlayerName());
                    $existingPlayers[] = $existingPlayer;
                } else {
                    // New player, create a new player entity
                    $player = new Player();
                    $player->setPlayerName($playerData->getPlayerName());
                    $player->setTeam($newTeam);
                    $entityManager->persist($player);
                }
            }

            // Remove players that are not included in the form
            $playersToRemove = array_diff($newTeam->getPlayers()->toArray(), $existingPlayers);
            foreach ($playersToRemove as $playerToRemove) {
                $newTeam->removePlayer($playerToRemove);
                $entityManager->remove($playerToRemove);
            }

            $entityManager->persist($newTeam);
            $entityManager->flush();
            $this->addFlash('success', 'Team created successfully!');

            return $this->redirectToRoute('teams', ['id' => $team->getId()]);
        }

        return $this->render('teams/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }





    #[Route('/teams/edit/{id}', methods: ['GET', 'POST'], name: 'edit_team')]

    public function edit(Request $request, TeamRepository $teamRepository, EntityManagerInterface $entityManager, int $id): Response
    {


        $team = $teamRepository->find($id);

        // dd($team);


        $form = $this->createForm(TeamFormType::class, $team);
        $form->handleRequest($request);
        $team_logo = $form->get('team_logo')->getData();

        if ($form->isSubmitted() && $form->isValid()) {

            if ($team_logo) {

                //Handle Logo Upload

                if ($team->getTeamLogo() !== null) {
                    if (file_exists($this->getParameter('kernel.project_dir') . $team->getTeamLogo())) {

                        $this->getParameter('kernel.project_dir') . $team->getTeamLogo();

                        $newFileName = uniqid() . '.' . $team_logo->guessExtension();

                        try {
                            $team_logo->move(

                                $this->getParameter('kernel.project_dir') . '/public/uploads',
                                $newFileName
                            );
                        } catch (FileException $e) {
                            return new Response($e->getMessage());
                        }

                        $team->setTeamLogo('/uploads/' . $newFileName);
                        $this->em->flush();

                        return $this->redirectToRoute('teams');
                    }
                }
            } else {

                $team->setTeamName($form->get('team_name')->getData());
                $team->setTeamLogo($form->get('team_logo')->getData());
                $team->setLeague($form->get('league')->getData());
                $team->setReleaseYear($form->get('releaseYear')->getData());

                $this->em->flush();
                return $this->redirectToRoute('teams', ['id' => $team->getId()]);
            }
        }

        return $this->render('teams/edit.html.twig', [
            'form' => $form->createView(),
            'team' => $team,
        ]);
    }

    #[Route('/teams/{id}', methods: ['GET'], name: 'show_team')]
    public function show(int $id, TeamRepository $teamRepository): Response
    {

        $team = $teamRepository->find($id);
        return $this->render('teams/show.html.twig', [
            'team' => $team,
        ]);
    }
}
