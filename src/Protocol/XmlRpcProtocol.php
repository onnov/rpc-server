<?php

namespace Moriony\RpcServer\Protocol;

use Exception;
use Moriony\RpcServer\Exception\RpcException;
use Moriony\RpcServer\Exception\RpcExceptionInterface;
use Moriony\RpcServer\Request\RpcRequestInterface;
use Moriony\RpcServer\Request\XmlRpcRequest;
use Moriony\RpcServer\Response\XmlRpcResponse;
use Moriony\RpcServer\ResponseSerializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;

class XmlRpcProtocol implements ProtocolInterface
{
    const MESSAGE_UNEXPECTED_ERROR = 'Unexpected error occurred.';

    protected $serializer;

    /**
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param Request $request
     * @return XmlRpcRequest
     */
    public function createRequest(Request $request)
    {
        return new XmlRpcRequest($request);
    }

    /**
     * @param RpcRequestInterface $request
     * @param mixed $data
     * @return XmlRpcResponse
     */
    public function createResponse(RpcRequestInterface $request, $data)
    {
        $body = $this->serializer->serialize($data);
        return new XmlRpcResponse($body, 200, []);
    }

    /**
     * @param Exception $exception
     * @return XmlRpcResponse
     */
    public function createErrorResponse(Exception $exception)
    {
        if (!$exception instanceof RpcExceptionInterface) {
            $exception = new RpcException();
        }
        $body = $this->serializer->serialize([
            'faultCode' => $exception->getCode(),
            'faultString' => $exception->getMessage(),
        ]);
        return new XmlRpcResponse($body, 200, []);
    }


}