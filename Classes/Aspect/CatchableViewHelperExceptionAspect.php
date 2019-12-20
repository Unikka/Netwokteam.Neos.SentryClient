<?php

namespace Unikka\Neos\SentryClient\Aspect;

/*
 * This file is part of the Unikka Legacy.Neos.SentryClient package.
 *
 * (c) unikka
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use TYPO3\Flow\Annotations as Flow;
use Unikka\Neos\SentryClient\ErrorHandler;

/**
 * @Flow\Aspect
 */
class CatchableViewHelperExceptionAspect
{

    /**
     * @Flow\Inject
     * @var ErrorHandler
     */
    protected $errorHandler;

    /**
     * @Flow\AfterThrowing("within(TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper) && method(.*->render())")
     * @param \TYPO3\Flow\Aop\JoinPoint $joinPoint
     */
    public function catchException(\TYPO3\Flow\Aop\JoinPoint $joinPoint)
    {
        $exception = $joinPoint->getException();
        $this->errorHandler->handleException($exception);
    }
}
