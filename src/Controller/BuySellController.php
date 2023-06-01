<?php

namespace App\Controller;


use App\Entity\Team;
use App\Entity\Player;
use App\Repository\TeamRepository;
use App\Repository\PlayerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;


class BuySellController extends AbstractController
{
    private $playerRepository;
    private $teamRepository;
    private $em;
    private $paginator;


    // public function __construct(EntityManagerInterface $teamRepository, )
    public function __construct(EntityManagerInterface $em, PlayerRepository  $playerRepository, TeamRepository  $teamRepository,  PaginatorInterface $paginator)
    {
        $this->playerRepository = $playerRepository;
        $this->teamRepository = $teamRepository;
        $this->em = $em;
        $this->paginator = $paginator;
    }


    #[Route('/buy&sell', methods: ['GET'], name: 'buy&sell')]

    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        $players = $this->playerRepository->findAll();

        $teams = $this->teamRepository->findAll();

        // Paginate the players data
        $pagination = $paginator->paginate(
            $teams,
            $request->query->getInt('page', 1),
            7 // Number of players to display per page
        );
        // dd($players);
        return $this->render('buy&sell/index.html.twig', [
            'players' => $players,
            'teams' => $teams,
            'pagination' => $pagination
        ]);
    }

    #[Route('/buy&sell/{id}', methods: ['GET'], name: 'show_player')]
    public function show(int $id, TeamRepository $teamRepository, PlayerRepository $playerRepository): Response
    {

        // $team = $teamRepository->find($id);
        $player = $playerRepository->find($id);
        $teams = $teamRepository->findAll();

        // dd($player);

        return $this->render('buy&sell/buy_player.html.twig', [
            'teams' => $teams,
            'player' => $player,
        ]);
    }

    #[Route('/transfer', methods: ["GET", "POST"], name: 'transfer_player')]
    public function transferPlayer(Request $request, EntityManagerInterface $entityManager, TeamRepository $teamRepository, PlayerRepository $playerRepository): Response
    {
        $playerId = $request->request->get('playerId');
        $teamId = $request->request->get('teamId');
        $transferAmount = $request->request->get('transferAmount');

        // Retrieve the player and destination team from the database
        $player = $entityManager->getRepository(Player::class)->find($playerId);
        $team = $entityManager->getRepository(Team::class)->find($teamId);

        if ($player && $team) {
            // Retrieve the parent team from the player
            $parentTeam = $player->getTeam();
            $parentBalance = $parentTeam->getReleaseYear();

            // Update the player's team
            $player->setTeam($team);

            // Decrease the balance of the parent team
            $parentTeam->setReleaseYear($parentBalance - $transferAmount);

            // Increase the balance of the destination team
            $teamBalance = $team->getReleaseYear();
            $team->setReleaseYear($teamBalance + $transferAmount);

            // Save the changes to the database
            $entityManager->persist($player);
            $entityManager->persist($parentTeam);
            $entityManager->persist($team);
            $entityManager->flush();



            return new Response('Player transferred successfully');
        }

        return new Response('Transfer failed');
    }
}