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

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Reflection\ObjectAccess;
use Jenssegers\Agent\Agent;
use TYPO3\Flow\Core\Bootstrap;
use TYPO3\Flow\Security\Context as SecurityContext;
use TYPO3\Flow\Utility\Environment;
use function Sentry\captureException;
use function Sentry\configureScope;
use function Sentry\init as initSentry;
use Sentry\State\Hub;
use Sentry\State\Scope;

/**
 * @Flow\Scope("singleton")
 */
class ErrorHandler
{

    /**
     * @var string
     */
    protected $dsn;

    /**
     * @var string
     */
    protected $release;

    /**
     * @var Agent
     */
    protected $agent;

    /**
     * @var \Unikka\Neos\SentryClient\Context\UserContextServiceInterface
     * @Flow\Inject
     */
    protected $userContextService;

    /**
     * Initialize the sentry client and environment detection agent
     */
    public function initializeObject()
    {
        initSentry(['dsn' => $this->dsn]);
        $this->agent = new Agent();
    }

    /**
     * Explicitly handle an exception, should be called from an exception handler (in Flow or TypoScript)
     *
     * @param object $exception The exception to capture
     * @param array $extraData Additional data passed to the Sentry sample
     */
    public function handleException($exception, array $extraData = array())
    {
        if (!$exception instanceof \Throwable) {
            // can`t handle anything different from \Exception and \Throwable
            return;
        }

        if ($exception instanceof \TYPO3\Flow\Exception) {
            $extraData['referenceCode'] = $exception->getReferenceCode();
        }

        $this->setExtraContext($extraData);
        $this->setUserContext();
        $this->setTagsContext(['code' => $exception->getCode()]);
        $this->setReleaseContext();
        captureException($exception);
    }

    protected function getBrowserContext(): array
    {
        return [
            'contexts' => [
                'browser' => [
                    'name' => $this->agent->browser(),
                    'version' => $this->agent->version($this->agent->browser())
                ]
            ]
        ];
    }

    protected function getOsContext(): array
    {
        return [
            'contexts' => [
                'os' => [
                    'name' => $this->agent->platform(),
                    'version' => $this->agent->version($this->agent->platform())
                ]
            ]
        ];
    }

    /**
     * Set extra on the sentry event scope
     * @param array $additionalExtraData
     */
    protected function setExtraContext(array $additionalExtraData): void
    {
        $data = array_merge_recursive($additionalExtraData, $this->getBrowserContext(), $this->getOsContext());
        configureScope(function (Scope $scope) use ($data): void {
            foreach ($data as $key => $value) {
                $scope->setExtra($key, $value);
            }
        });
    }

    /**
     * Set tags on the sentry event scope
     * @param array $additionalTags
     */
    protected function setTagsContext(array $additionalTags): void
    {
        $objectManager = Bootstrap::$staticObjectManager;
        $environment = $objectManager->get(Environment::class);
        $tags = [
            'php_version' => PHP_VERSION,
            'flow_context' => (string)$environment->getContext(),
            'flow_version' => FLOW_VERSION_BRANCH
        ];
        $tags = array_merge($tags, $additionalTags);
        configureScope(function (Scope $scope) use ($tags): void {
            foreach ($tags as $tagKey => $tagValue) {
                $scope->setTag($tagKey, $tagValue);
            }
        });
    }

    /**
     * Set user information on the raven context
     * @throws \Exception
     */
    protected function setUserContext()
    {
        $objectManager = Bootstrap::$staticObjectManager;
        /** @var SecurityContext $securityContext */
        $securityContext = $objectManager->get(SecurityContext::class);

        $userContext = array();

        if ($securityContext->isInitialized()) {
            $account = $securityContext->getAccount();
            if ($account !== NULL) {
                $userContext['username'] = $account->getAccountIdentifier();
            }
            $externalUserContextData = $this->userContextService->getUserContext($securityContext);
            if ($externalUserContextData !== []) {
                $userContext = array_merge($userContext, $externalUserContextData);
            }
        }

        configureScope(function (Scope $scope) use ($userContext): void {
            if ($userContext !== []) {
                $scope->setUser($userContext);
            }
        });
    }

    /**
     * Set release information as client option
     */
    protected function setReleaseContext(): void
    {
        $client = Hub::getCurrent()->getClient();
        if ($this->release !== '' && $client) {
            $options = $client->getOptions();
            $options->setRelease($this->release);
        }
    }

    /**
     * @param array $settings
     */
    public function injectSettings(array $settings)
    {
        $this->dsn = $settings['dsn'] ?? '';
        $this->release = $settings['release'] ?? '';
    }
}
