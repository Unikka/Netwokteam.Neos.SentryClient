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

interface UserContextServiceInterface
{

    /**
     * Returns ContextData to be added to the sentry entry
     *
     * @param \TYPO3\Flow\Security\Context $securityContext
     * @return array
     */
    public function getUserContext(\TYPO3\Flow\Security\Context $securityContext);
}
