<?php

declare(strict_types=1);

namespace Ruga\Datatables\Middleware;

use Laminas\Diactoros\Request;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\ServerRequest;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Ruga\Datatables\DatasourcePlugins\DatasourcePluginManager;


/**
 * DatatablesMiddleware creates a DatatablesRequest from a serverSide request and tries to find the desired plugin.
 * If found, the process method is executed and returns a DatatablesResponse, which is returned to the client.
 *
 * @see     DatatablesMiddlewareFactory
 */
class DatatablesMiddleware implements MiddlewareInterface
{
    /** @var DatasourcePluginManager */
    private $datasourcePluginManager;
    
    
    
    public function __construct(DatasourcePluginManager $datasourcePluginManager)
    {
        $this->datasourcePluginManager = $datasourcePluginManager;
    }
    
    
    
    /**
     * Process an incoming server request.
     *
     * Processes an incoming server request in order to produce a response.
     * If unable to produce the response itself, it may delegate to the provided
     * request handler to do so.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        \Ruga\Log::functionHead($this);
        
        try {
            $datatablesRequest = new DatatablesRequest($request);
            $datasourcePlugin = $this->datasourcePluginManager->get($datatablesRequest->getPluginAlias());
            $datatablesResponse = $datasourcePlugin->process($datatablesRequest);
            
            $jsonEncodingOptions = JsonResponse::DEFAULT_JSON_FLAGS;
            if (!in_array('XMLHttpRequest', $request->getHeader('X-Requested-With'))) {
                $jsonEncodingOptions = $jsonEncodingOptions | JSON_PRETTY_PRINT;
            }
            return new JsonResponse($datatablesResponse, 200, [], $jsonEncodingOptions);
        } catch (\Exception $e) {
            return new JsonResponse(
                [
                    'error' => $e->getMessage(),
                    'error-exception' => get_class($e),
                    'error-trace' => $e->getTrace(),
                    'query' => '',
                ],
                $e->getCode() == 0 ? 500 : $e->getCode(),
                [],
                JsonResponse::DEFAULT_JSON_FLAGS | JSON_PRETTY_PRINT | JSON_FORCE_OBJECT
            );
        }
    }
    
    
}