<?php
declare(strict_types=1);

namespace Haidukua\BotCore;

use Haidukua\BotCore\Contract\BrowserFilter;
use Haidukua\BotCore\Contract\BrowserMiddleware;
use Haidukua\BotCore\Contract\ProxyManagerInterface;
use Symfony\Component\BrowserKit\CookieJar;
use Symfony\Component\BrowserKit\Exception\BadMethodCallException;
use Symfony\Component\BrowserKit\History;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class Browser extends HttpBrowser
{
    /**
     * @var BrowserMiddleware[]
     */
    private array $middlewares = [];

    /**
     * @var BrowserFilter[]
     */
    private array $filters = [];

    private int $retries = 0;

    public function __construct(
        HttpClientInterface $client,
        private readonly ?ProxyManagerInterface $proxyManager,
        ?History $history = null,
        ?CookieJar $cookieJar = null,
    ) {
        parent::__construct($client, $history, $cookieJar);
    }

    public function addMiddleware(BrowserMiddleware $middleware): void
    {
        $this->middlewares[] = $middleware;
    }

    public function addFilter(BrowserFilter $filter): void
    {
        $this->filters[] = $filter;
    }

    #[\Override]
    public function request(
        string $method,
        string $uri,
        array $parameters = [],
        array $files = [],
        array $server = [],
        ?string $content = null,
        bool $changeHistory = true,
    ): Crawler {
        $crawler = parent::request(
            $method,
            $uri,
            $parameters,
            $files,
            $server,
            $content,
            $changeHistory,
        );

        foreach ($this->middlewares as $middleware) {
            $middleware->postRequest($this);
        }

        return $crawler;
    }

    public function goTo(string $uri): Crawler
    {
        return $this->request('GET', $uri);
    }

    #[\Override]
    public function submitForm(string $button, array $fieldValues = [], string $method = 'POST', array $serverParameters = [], string $selector = 'body'): Crawler
    {
        $buttonNode = $this->crawler->filter($selector)->selectButton($button);
        $form = $buttonNode->form($fieldValues, $method);

        return $this->submit($form, [], $serverParameters);
    }

    public function clickLinkByHref(string $linkHref, string $rootSelector = 'body'): Crawler
    {
        $links = $this->getCrawler()->filter($rootSelector)->filter('a')->links();

        $linkHref = preg_quote($linkHref, '/');
        foreach ($links as $link) {

            if (preg_match("/$linkHref/", $link->getUri())) {
                return $this->request('GET', $link->getUri());
            }
        }

        throw new \LogicException(sprintf('Url by href `%s` not found', $linkHref));
    }

    public function clickLinkBySelector(string $linkText, string $selector = 'body'): Crawler
    {
        $crawler = $this->crawler ?? throw new BadMethodCallException(sprintf('The "request()" method must be called before "%s()".', __METHOD__));

        return $this->click($crawler->filter($selector)->selectLink($linkText)->link());
    }

    public function openFile(string $url, string $path): Crawler
    {
        $this->crawler = $this->createCrawlerFromContent($url, file_get_contents($path), 'text/html; charset=UTF-8');

        return $this->crawler;
    }

    public function plainRequest(Request $request): Response
    {
        return $this->doRequest($request, true);
    }

    #[\Override]
    protected function doRequest(object $request, bool $plainRequest = false): Response
    {
        if (!$request instanceof Request) {
            throw new \LogicException(
                sprintf('Expected instance of `%s` instead of `%s`', Request::class, $request::class),
            );
        }

        $proxy = $this->proxyManager?->get();

        if ($proxy !== null) {
            $proxyString = sprintf(
                'http://%s:%s@%s:%s',
                $proxy->login,
                $proxy->password,
                $proxy->ip,
                $proxy->port,
            );

            $_SERVER['https_proxy'] = $_SERVER['http_proxy'] = $proxyString;
        }

        foreach ($this->middlewares as $middleware) {
            $middleware->preRequest($this);
        }

        try {
            $response = parent::doRequest($request);
        } catch (TransportExceptionInterface $e) {
            if (++$this->retries <= 5) {
                if ($proxy !== null) {
                    $this->proxyManager->fail($proxy);
                }

                sleep(3);

                return $this->doRequest($request);
            }

            throw $e;
        }

        return $response;
    }

    protected function filterRequest(Request $request): Request
    {
        foreach ($this->filters as $filter) {
            $request = $filter->filterRequest($request);
        }

        return $request;
    }

    protected function filterResponse(object $response): object
    {
        foreach ($this->filters as $filter) {
            $response = $filter->filterResponse($response);
        }

        return $response;
    }
}
