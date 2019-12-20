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

/**
 * @Flow\Aspect
 */
class TypoScriptHandlerAspect
{

    /**
     * @Flow\Inject
     * @var \Unikka\Neos\SentryClient\ErrorHandler
     */
    protected $errorHandler;

    /**
     * Forward all exceptions that are handled in TypoScript rendering exception handlers to Sentry
     *
     * @Flow\After("within(TYPO3\TypoScript\Core\ExceptionHandlers\AbstractRenderingExceptionHandler) && method(.*->handle())")
     * @param \TYPO3\Flow\Aop\JoinPointInterface $joinPoint
     */
    public function captureException(\TYPO3\Flow\Aop\JoinPointInterface $joinPoint)
    {
        $exception = $joinPoint->getMethodArgument('exception');
        $this->errorHandler->handleException($exception, array('typoScriptPath' => $joinPoint->getMethodArgument('typoScriptPath')));
    }
}
