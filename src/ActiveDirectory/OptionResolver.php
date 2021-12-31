<?php

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
            $isOuMembership = $option->getSourceType()->equals(ActiveDirectorySyncSourceType::Ou()) && $this->checkForOuMembership($option, $ou);
            $isGroupsMembership = $option->getSourceType()->equals(ActiveDirectorySyncSourceType::Group()) && $this->checkForGroupMembership($option, $groups);

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
            $isOuMembership = $option->getSourceType()->equals(ActiveDirectorySyncSourceType::Ou()) && $this->checkForOuMembership($option, $ou);
            $isGroupsMembership = $option->getSourceType()->equals(ActiveDirectorySyncSourceType::Group()) && $this->checkForGroupMembership($option, $groups);

            if($isOuMembership || $isGroupsMembership) {
                $result[] = $option;
            }
        }

        return $result;
    }

    /**
     * @param ActiveDirectorySyncOptionInterface $option
     * @param string $ou
     * @return bool
     */
    private function checkForOuMembership(ActiveDirectorySyncOptionInterface $option, string $ou): bool {
        return $option->getSource() === $ou || substr($ou, -strlen($option->getSource())) == $option->getSource();
    }

    /**
     * @param ActiveDirectorySyncOptionInterface $options
     * @param string[] $groups
     * @return bool
     */
    private function checkForGroupMembership(ActiveDirectorySyncOptionInterface $options, array $groups): bool {
        foreach($groups as $group) {
            if($options->getSource() === $group) {
                return true;
            }
        }

        return false;
    }
}