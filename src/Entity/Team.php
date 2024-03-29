<?php

namespace App\Entity;

use App\Entity\Player;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\TeamRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: TeamRepository::class)]
class Team
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $team_name = null;

    #[ORM\Column(length: 255)]
    private ?string $team_logo = null;

    #[ORM\Column(length: 100)]
    private ?string $league = null;

    #[ORM\Column(length: 200)]
    private ?float $releaseYear = null;

    #[ORM\OneToMany(mappedBy: 'team', targetEntity: Player::class, cascade: ["persist"])]
    private Collection $players;




    public function __construct()
    {
        $this->players = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTeamName(): ?string
    {
        return $this->team_name;
    }


    public function setTeamName(?string $team_name): self
    {
        $this->team_name = $team_name;

        return $this;
    }

    public function getTeamLogo(): ?string
    {
        return $this->team_logo;
    }

    public function setTeamLogo(?string $team_logo): self
    {
        $this->team_logo = $team_logo;

        return $this;
    }

    public function getLeague(): ?string
    {
        return $this->league;
    }

    public function setLeague(?string $league): self
    {
        $this->league = $league;
        return $this;
    }

    public function getReleaseYear(): ?float
    {
        return $this->releaseYear;
    }

    public function setReleaseYear(?float $releaseYear): self
    {
        $this->releaseYear = $releaseYear;

        return $this;
    }

    /**
     * @return Collection<int, Player>
     */
    public function getPlayers(): Collection
    {
        return $this->players;
    }

    public function addPlayer(Player $player): self
    {
        if (!$this->players->contains($player)) {
            $this->players->add($player);
            $player->setTeam($this);
        }

        return $this;
    }

    public function removePlayer(Player $player): self
    {
        if ($this->players->removeElement($player)) {
            // set the owning side to null (unless already changed)
            if ($player->getTeam() === $this) {
                $player->setTeam(null);
            }
        }

        return $this;
    }
}
