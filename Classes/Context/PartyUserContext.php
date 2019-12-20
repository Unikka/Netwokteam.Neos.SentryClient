<?php

namespace Unikka\Neos\SentryClient\Context;

/*
 * This file is part of the Unikka Legacy.Neos.SentryClient package.
 *
 * (c) unikka
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use TYPO3\Party\Domain\Model\Person;

class PartyUserContext implements UserContextServiceInterface
{

    /**
     * Returns ContextData to be added to the sentry entry
     *
     * @param \TYPO3\Flow\Security\Context $securityContext
     * @return array
     * @throws \TYPO3\Flow\Reflection\Exception\PropertyNotAccessibleException
     */
    public function getUserContext(\TYPO3\Flow\Security\Context $securityContext)
    {
        $party = $securityContext->getParty();
        $userContext = [];
        if ($party instanceof Person && $party->getPrimaryElectronicAddress() !== NULL) {
            $userContext['email'] = (string)$party->getPrimaryElectronicAddress();
        } elseif ($party !== NULL && \TYPO3\Flow\Reflection\ObjectAccess::isPropertyGettable($party, 'emailAddress')) {
            $userContext['email'] = (string)\TYPO3\Flow\Reflection\ObjectAccess::getProperty($party, 'emailAddress');
        }

        return $userContext;
    }
}
