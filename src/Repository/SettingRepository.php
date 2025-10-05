<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Setting;
use Doctrine\ORM\EntityManagerInterface;

class SettingRepository implements SettingRepositoryInterface {

    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    public function findOneByKey(string $key): ?Setting {
        return $this->em->getRepository(Setting::class)
            ->findOneBy([
                'key' => $key
            ]);
    }

    /**
     * @return Setting[]
     */
    public function findAll(): array {
        return $this->em->getRepository(Setting::class)
            ->findAll();
    }

    public function persist(Setting $setting): void {
        $this->em->persist($setting);
        $this->em->flush();
    }

}
