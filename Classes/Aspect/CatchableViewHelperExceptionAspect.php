<?php

namespace Unikka\Neos\SentryClient\Aspect;

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
