<?php
namespace Networkteam\Neos\FriendlyCaptcha\Validation\Validator;

/***************************************************************
 *  (c) 2021 networkteam GmbH - all rights reserved
 ***************************************************************/

use Neos\Flow\Annotations as Flow;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use Neos\Flow\Validation\Exception\InvalidValidationOptionsException;
use Neos\Flow\Validation\Validator\AbstractValidator;
use Psr\Http\Message\ResponseInterface;

class CaptchaSolutionValidator extends AbstractValidator
{

    /**
     * @Flow\InjectConfiguration(package="Networkteam.Neos.FriendlyCaptcha")
     * @var array
     */
    protected $captchaConfiguration;

    /**
     * @var \GuzzleHttp\ClientInterface
     * @Flow\Inject
     */
    protected $client;

    /**
     * @var array
     */
    protected $supportedOptions = [
        'allowNull' => [true, 'Allow null value and skip validation in this case', 'boolean'],
    ];

    /**
     * Do not skip validation of empty values (in AbstractValidator) since empty strings should not be allowed
     *
     * @var bool
     */
    protected $acceptsEmptyValues = false;

    protected function isValid($value)
    {
        // make validation optional
        if ($value === null && $this->options['allowNull']) {
            return;
        }

        if (empty($value)) {
            $this->addError('Empty captcha solution', 1625742772);
            return;
        }

        try {
            $this->validateConfiguration();

            $options = [
                RequestOptions::JSON => [
                    'solution' => $value,
                    'secret' => $this->captchaConfiguration['secret']
                ]
            ];

            // add optional 'sitekey' paramater
            if (!empty($this->captchaConfiguration['sitekey'])) {
                $options[RequestOptions::JSON]['sitekey'] = $this->captchaConfiguration['sitekey'];
            }

            $response = $this->client->request('post', $this->captchaConfiguration['verifyEndpoint'], $options);
            $errorMessage = $this->getErrorMessageFromApiResponse((string)$response->getBody());
        } catch (\Throwable $e) {
            if ($e instanceof RequestException && $e->getResponse() instanceof ResponseInterface) {
                $responseBody = (string)$e->getResponse()->getBody();
                $errorMessage = $this->getErrorMessageFromApiResponse($responseBody);
            }

            if (empty($errorMessage)) {
                $errorMessage = $e->getMessage();
            }
        } finally {
            if (!empty($errorMessage)) {
                $this->addError($errorMessage, 1639585455);
            }
        }
    }

    /**
     * Checks if this validator is correctly configured
     *
     * @return void
     * @throws InvalidValidationOptionsException if the configured validation options are incorrect
     */
    protected function validateConfiguration()
    {
        if (empty($this->captchaConfiguration['secret'])) {
            throw new InvalidValidationOptionsException('The setting "secret" must not be empty.', 1624389609);
        }

        if (empty($this->captchaConfiguration['verifyEndpoint'])) {
            throw new InvalidValidationOptionsException('The setting "verifyEndpoint" must not be empty.', 1624380619);
        }
    }

    /**
     * @param string $responseBody JSON string
     */
    protected function getErrorMessageFromApiResponse(string $responseBody): string
    {
        $apiData = json_decode($responseBody, true);

        if (isset($apiData['success']) && $apiData['success'] !== true) {
            if (isset($apiData['details'])) {
                $message = sprintf(
                    '%s. Error codes: %s',
                    $apiData['details'],
                    implode(',', $apiData['errors'])
                );
            } else {
                $message = sprintf(
                    'Error codes: %s',
                    implode(',', $apiData['errors'])
                );
            }
            return $message;
        } else {
            return '';
        }
    }
}