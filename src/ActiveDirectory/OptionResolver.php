<?php

namespace App\ActiveDirectory;

use App\Entity\ActiveDirectorySyncOptionInterface;
use App\Entity\ActiveDirectorySyncSourceType;

class OptionResolver {

    /**
     * @param ActiveDirectorySyncOptionInterface[] $options
     * @param string $ou
     * @param string[] $groups
     * @return ActiveDirectorySyncOptionInterface|null
     */
    public function getOption(array $options, $ou, array $groups) {
        foreach($options as $option) {
            $isOuMembership = $option->getSourceType() === ActiveDirectorySyncSourceType::OU && $this->checkForOuMembership($option, $ou);
            $isGroupsMembership = $option->getSourceType() === ActiveDirectorySyncSourceType::GROUP && $this->checkForGroupMembership($option, $groups);

            if($isOuMembership || $isGroupsMembership) {
                return $option;
            }
        }

        return null;
    }

    /**
     * @param ActiveDirectorySyncOptionInterface $option
     * @param string $ou
     * @return bool
     */
    private function checkForOuMembership(ActiveDirectorySyncOptionInterface $option, $ou) {
        return $option->getSource() === $ou;
    }

    /**
     * @param ActiveDirectorySyncOptionInterface $option
     * @param string[] $groups
     * @return bool
     */
    private function checkForGroupMembership(ActiveDirectorySyncOptionInterface $options, array $groups) {
        foreach($groups as $group) {
            if($options->getSource() === $group) {
                return true;
            }
        }

        return false;
    }
}