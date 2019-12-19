<?php
namespace Unikka\Neos\SentryClient\Aspect;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Aop\JoinPointInterface;
use Networkteam\SentryClient\ErrorHandler;
use TYPO3\TypoScript\Core\ExceptionHandlers\AbsorbingHandler;

/**
 * @Flow\Aspect
 */
class FusionHandlerAspect
{

    /**
     * @Flow\Inject
     * @var ErrorHandler
     */
    protected $errorHandler;

    /**
     * Forward all exceptions that are handled in Fusion rendering exception handlers to Sentry
     *
     * Ignores the exception, if it was handled by an AbsorbingHandler.
     *
     * @Flow\After("within(TYPO3\TypoScript\Core\ExceptionHandlers\AbstractRenderingExceptionHandler) && method(.*->handle())")
     * @param JoinPointInterface $joinPoint
     */
    public function captureException(JoinPointInterface $joinPoint)
    {
        if ($joinPoint->getProxy() instanceof AbsorbingHandler) {
            return;
        }
        $exception = $joinPoint->getMethodArgument('exception');
        $args = $joinPoint->getMethodArguments();
        $fusionPath = $args['fusionPath'] ?? $args['typoScriptPath'];
        $this->errorHandler->handleException($exception, ['fusionPath' => $fusionPath]);
    }
}
