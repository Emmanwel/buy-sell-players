<?php

namespace App\Controller;

use App\Entity\Team;
use App\Entity\Player;
use App\Form\TeamFormType;
use App\Form\PlayerFormType;
use App\Repository\TeamRepository;
use App\Repository\PlayerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
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

            // Handle team logo upload
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
            foreach ($playersData as $playerData) {
                $player = new Player();
                $player->setPlayerName($playerData['player_name']);
                $player->setTeam($newTeam);
                $entityManager->persist($player);
            }

            $entityManager->persist($newTeam);
            $entityManager->flush();

            // Redirect the user or display a success message
            return $this->redirectToRoute('teams');
        }

        return $this->render('teams/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/teams/{teamId}/add-players', name: 'add_players_to_team')]
    public function addPlayersToTeam(Request $request, EntityManagerInterface $entityManager, int $teamId): Response
    {
        // Retrieve the team by its ID
        $team = $entityManager->getRepository(Team::class)->find($teamId);

        if (!$team) {
            throw $this->createNotFoundException('Team not found');
        }

        // Create a collection of player entities
        $players = new ArrayCollection();

        // Add a blank player to the collection for the form
        $players->add(new Player());

        // Create the form with the players collection
        $form = $this->createFormBuilder(['players' => $players])
            ->add('players', CollectionType::class, [
                'entry_type' => PlayerFormType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Get the form data
            $formData = $form->getData();
            $submittedPlayers = $formData['players'];

            // Iterate over the submitted players
            foreach ($submittedPlayers as $player) {
                // Set the team for each player
                $player->setTeam($team);

                // Persist each player entity
                $entityManager->persist($player);
            }

            // Flush the changes to the database
            $entityManager->flush();

            $this->addFlash('success', 'Players added to the team successfully!');

            return $this->redirectToRoute('teams', ['id' => $teamId]);
        }

        return $this->render('teams/add_players.html.twig', [
            'form' => $form->createView(),
            'team' => $team,
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
