<?php

namespace Unikka\Neos\SentryClient;

/*
 * This file is part of the Unikka Legacy.Neos.SentryClient package.
 *
 * (c) unikka
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use TYPO3\Flow\Package\Package as BasePackage;
use TYPO3\Flow\Core\Booting\Sequence;
use Unikka\Neos\SentryClient\ErrorHandler;

class Package extends BasePackage
{

    /**
     * {@inheritdoc}
     */
    public function boot(\TYPO3\Flow\Core\Bootstrap $bootstrap)
    {

        $bootstrap->getSignalSlotDispatcher()->connect(Sequence::class, 'afterInvokeStep', function ($step, $runlevel) use ($bootstrap) {
            if ($step->getIdentifier() === 'typo3.flow:objectmanagement:runtime') {
                // This triggers the initializeObject method
                $bootstrap->getObjectManager()->get(ErrorHandler::class);
            }
        });
    }
}
