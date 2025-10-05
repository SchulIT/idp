<?php

declare(strict_types=1);

namespace App\ActiveDirectory;

use App\Entity\ActiveDirectorySyncOptionInterface;
use App\Entity\ActiveDirectorySyncSourceType;

/**
 * Helper for AD sync options.
 */
class OptionResolver {

    /**
     * Return the ActiveDirectorySyncOptionInterface (ActiveDirectoryGradeSyncOption or ActiveDirectorySyncOption) based
     * on the OU and group memberships.
     *
     * @param ActiveDirectorySyncOptionInterface[] $options List of possible options
     * @param string $ou Organizational unit of the user
     * @param string[] $groups Groups the user is a member of
     * @return ActiveDirectorySyncOptionInterface|null Resulting ActiveDirectorySyncOption
     */
    public function getOption(array $options, string $ou, array $groups): ?ActiveDirectorySyncOptionInterface {
        foreach($options as $option) {
            $isOuMembership = $option->getSourceType() === ActiveDirectorySyncSourceType::Ou && $this->checkForOuMembership($option, $ou);
            $isGroupsMembership = $option->getSourceType() === ActiveDirectorySyncSourceType::Group && $this->checkForGroupMembership($option, $groups);

            if($isOuMembership || $isGroupsMembership) {
                return $option;
            }
        }

        return null;
    }

    /**
     * Return all matching ActiveDirectorySyncOptionInterfaces based on the OU and group memberships.P
     * Pretty much works like getOptions() but returns an array of all matching options instead of returning the first one.
     *
     * @param ActiveDirectorySyncOptionInterface[] $options List of possible options
     * @param string $ou Organizational unit of the user
     * @param string[] $groups Groups the user is a member of
     * @return ActiveDirectorySyncOptionInterface[] Matching ActiveDirectorySyncOptionInterfaces
     */
    public function getAllOptions(array $options, string $ou, array $groups): array {
        $result = [ ];

        foreach($options as $option) {
            $isOuMembership = $option->getSourceType() === ActiveDirectorySyncSourceType::Ou && $this->checkForOuMembership($option, $ou);
            $isGroupsMembership = $option->getSourceType() === ActiveDirectorySyncSourceType::Group && $this->checkForGroupMembership($option, $groups);

            if($isOuMembership || $isGroupsMembership) {
                $result[] = $option;
            }
        }

        return $result;
    }

    private function checkForOuMembership(ActiveDirectorySyncOptionInterface $option, string $ou): bool {
        return $option->getSource() === $ou || str_ends_with($ou, $option->getSource());
    }

    /**
     * @param string[] $groups
     */
    private function checkForGroupMembership(ActiveDirectorySyncOptionInterface $options, array $groups): bool
    {
        return in_array($options->getSource(), $groups, true);
    }
}
