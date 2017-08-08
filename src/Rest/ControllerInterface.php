<?php
declare(strict_types=1);
/**
 * /src/Rest/ControllerInterface.php
 *
 * @author  TLe, Tarmo Leppänen <tarmo.leppanen@protacon.com>
 */
namespace App\Rest;

use Symfony\Component\HttpFoundation\Request;

/**
 * Interface ControllerInterface
 *
 * @package App\Rest
 * @author  TLe, Tarmo Leppänen <tarmo.leppanen@protacon.com>
 */
interface ControllerInterface
{
    const METHOD_COUNT      = 'countMethod';
    const METHOD_CREATE     = 'createMethod';
    const METHOD_DELETE     = 'deleteMethod';
    const METHOD_FIND       = 'findMethod';
    const METHOD_FIND_ONE   = 'findOneMethod';
    const METHOD_IDS        = 'idsMethod';
    const METHOD_PATCH      = 'patchMethod';
    const METHOD_UPDATE     = 'updateMethod';

    /**
     * @return ResourceInterface
     *
     * @throws \UnexpectedValueException
     */
    public function getResource(): ResourceInterface;

    /**
     * @return ResponseHandlerInterface
     *
     * @throws \UnexpectedValueException
     */
    public function getResponseHandler(): ResponseHandlerInterface;

    /**
     * Getter method for used DTO class for current controller.
     *
     * @param string|null $method
     *
     * @return string
     *
     * @throws \UnexpectedValueException
     */
    public function getDtoClass(string $method = null): string;

    /**
     * Getter method for used DTO class for current controller.
     *
     * @param string|null $method
     *
     * @return string
     *
     * @throws \UnexpectedValueException
     */
    public function getFormTypeClass(string $method = null): string;

    /**
     * Method to initialize REST controller.
     *
     * @param ResourceInterface        $resource
     * @param ResponseHandlerInterface $responseHandler
     */
    public function init(ResourceInterface $resource, ResponseHandlerInterface $responseHandler): void;

    /**
     * Method to validate REST trait method.
     *
     * @param Request $request
     * @param array   $allowedHttpMethods
     *
     * @throws \LogicException
     * @throws \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException
     */
    public function validateRestMethod(Request $request, array $allowedHttpMethods): void;
}
