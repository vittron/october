<?php

namespace Kibo\Phast\Services\Bundler;

use Kibo\Phast\Common\JSON;
use Kibo\Phast\Exceptions\ItemNotFoundException;
use Kibo\Phast\HTTP\Response;
use Kibo\Phast\Logging\LoggingTrait;
use Kibo\Phast\Retrievers\Retriever;
use Kibo\Phast\Security\ServiceSignature;
use Kibo\Phast\Services\ServiceFilter;
use Kibo\Phast\Services\ServiceRequest;
use Kibo\Phast\ValueObjects\Resource;
use Kibo\Phast\ValueObjects\URL;

class Service {
    use LoggingTrait;

    /**
     * @var ServiceSignature
     */
    private $signature;

    /**
     * @var Retriever
     */
    private $cssRetriever;

    /**
     * @var ServiceFilter
     */
    private $cssFilter;

    /**
     * @var Retriever
     */
    private $jsRetriever;

    /**
     * @var ServiceFilter
     */
    private $jsFilter;

    private $tokenRefMaker;

    public function __construct(
        ServiceSignature $signature,
        Retriever $cssRetriever,
        ServiceFilter $cssFilter,
        Retriever $jsRetriever,
        ServiceFilter $jsFilter,
        TokenRefMaker $tokenRefMaker
    ) {
        $this->signature = $signature;
        $this->cssRetriever = $cssRetriever;
        $this->cssFilter = $cssFilter;
        $this->jsRetriever = $jsRetriever;
        $this->jsFilter = $jsFilter;
        $this->tokenRefMaker = $tokenRefMaker;
    }

    /**
     * @param ServiceRequest $request
     * @return Response
     */
    public function serve(ServiceRequest $request) {
        $response = new Response();
        $response->setHeader('Content-Type', 'application/json');
        $response->setContent($this->streamResponse($request));
        return $response;
    }

    private function streamResponse(ServiceRequest $request) {
        yield '[';
        $firstRow = true;
        foreach ($this->getParams($request) as $key => $params) {
            if (isset($params['ref'])) {
                $ref = $params['ref'];
                $params = $this->tokenRefMaker->getParams($ref);
                if (!$params) {
                    $this->logger()->error('Could not resolve ref {ref}', ['ref' => $ref]);
                    yield $this->generateJSONRow(['status' => 404], $firstRow);
                    continue;
                }
            }
            if (!isset($params['src'])) {
                $this->logger()->error('No src found for set {key}', ['key' => $key]);
                yield $this->generateJSONRow(['status' => 404], $firstRow);
                continue;
            }
            if (!$this->verifyParams($params)) {
                $this->logger()->error('Params verification failed for set {key}', ['key' => $key]);
                yield $this->generateJSONRow(['status' => 401], $firstRow);
                continue;
            }
            list($retriever, $filter) = $this->getRetrieverAndFilter($params);
            $resource = Resource::makeWithRetriever(
                URL::fromString($params['src']),
                $retriever
            );
            try {
                $this->logger()->info('Applying for set {key}', ['key' => $key]);
                $filtered = $filter->apply($resource, $params);
                yield $this->generateJSONRow([
                    'status' => 200,
                    'content' => $this->cleanUTF8($filtered->getContent()),
                ], $firstRow);
            } catch (ItemNotFoundException $e) {
                $this->logger()->error(
                    'Could not find {url} for set {key}',
                    ['url' => $params['src'], 'key' => $key]
                );
                yield $this->generateJSONRow(['status' => 404], $firstRow);
            } catch (\Exception $e) {
                $this->logger()->critical(
                    'Unhandled exception for set {key}: {type} Message: {message} File: {file} Line: {line}',
                    [
                        'key' => $key,
                        'type' => get_class($e),
                        'message' => $e->getMessage(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                    ]
                );
                yield $this->generateJSONRow(['status' => 500], $firstRow);
            }
        }
        yield ']';
    }

    private function getParams(ServiceRequest $request) {
        $params = $request->getParams();
        if (isset($params['src_0'])) {
            return (new BundlerParamsParser())->parse($request);
        }
        return (new ShortBundlerParamsParser())->parse($request);
    }

    private function verifyParams(array $params) {
        return ServiceParams::fromArray($params)->verify($this->signature);
    }

    private function getRetrieverAndFilter(array $params) {
        if (isset($params['isScript'])) {
            return [$this->jsRetriever, $this->jsFilter];
        }
        return [$this->cssRetriever, $this->cssFilter];
    }

    private function cleanUTF8($buffer) {
        return preg_replace_callback(
            '~
                [\x09\x0A\x0D\x20-\x7E]++          # ASCII
              | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
              |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
              | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
              |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
              |  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
              | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
              |  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
              | (.)
            ~xs',
            function ($match) {
                if (isset($match[1]) && strlen($match[1])) {
                    return '';
                }
                return $match[0];
            },
            $buffer
        );
    }

    private function generateJSONRow(array $content, &$firstRow) {
        if (!$firstRow) {
            $prepend = ',';
        } else {
            $prepend = '';
            $firstRow = false;
        }
        return $prepend . JSON::encode($content);
    }
}
