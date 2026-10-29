<?php

declare(strict_types=1);

namespace Metricool\Exceptions;

use Exception;
use Metricool\Bootstrap\App;
use Metricool\Support\Utility\SanitizeUtility;
use Metricool\Support\Helpers\Storages\EnvironmentConfig;

/**
 * Base class for exceptions that expose a localized message for the client.
 */
abstract class AbstractLocalizedException extends Exception
{
    /**
     * The default exception message. Should not be used to display to the user.
     * @var string
     */
    protected const MESSAGE = 'An error occurred';

    /**
     * The localized user facing message. By default, the global error message
     * set by {@see defaultMessage} is used.
     */
    protected string $localizedMessage = '';

    public function __construct(string $message = '', int $code = 0)
    {
        parent::__construct(($message ?: static::MESSAGE), $code);

        $this->setLocalizedMessage(
            $this->defaultMessage()
        );
    }

    /**
     * A translatable message intended to display to the end user in the frontend.
     */
    final public function getLocalizedMessage(): string
    {
        return $this->localizedMessage;
    }

    /**
     * Sets a localized message for client display.
     * The message is run through {@see SanitizeUtility::sanitizeMessage()}
     */
    final public function setLocalizedMessage(string $message): self
    {
        $this->localizedMessage = SanitizeUtility::sanitizeMessage($message);

        return $this;
    }

    /**
     * Returns a generic localized fallback message that includes a link to
     * leave a support message.
     */
    final protected function defaultMessage(): string
    {
        $env = App::getInstance()->get(EnvironmentConfig::class);

        return sprintf(
            /* translators: %1$s is opening link and %2$s is closing link */
            __('Something went wrong. Please try again or %1$sleave a support message%2$s.', 'metricool'),
            '<a href="' . $env->get('frontend.trusted_urls.new_support_ticket') . '" target="_blank" rel="noopener noreferrer">',
            '</a>',
        );
    }
}
