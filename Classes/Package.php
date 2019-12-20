<?php

namespace Unikka\Neos\SentryClient;

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
