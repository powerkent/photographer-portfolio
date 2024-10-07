<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Settings;
use Doctrine\ORM\EntityManagerInterface;

class SettingsService
{
    private EntityManagerInterface $em;
    private ?Settings $settings = null;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getSettings(): ?Settings
    {
        if ($this->settings === null) {
            $this->settings = $this->em->getRepository(Settings::class)->findOneBy([]);
        }

        return $this->settings;
    }
}