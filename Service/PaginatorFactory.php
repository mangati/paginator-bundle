<?php

namespace Mangati\PaginatorBundle;

use Doctrine\ORM\Query;
use Mangati\PaginatorBundle\Helper\Paginator;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Pagerfanta\View\TwitterBootstrap4View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

/**
 * PaginatorFactory
 *
 * @author rogerio
 */
class PaginatorFactory
{
    /**
     * @var RouterInterface
     */
    private $router;
    
    /**
     * @var string
     */
    private $pageParam = 'page';
    
    /**
     * @var string
     */
    private $pageLength = 10;
    
    /**
     * @var array
     */
    private $extraParams = [];
    
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
        
        if (!class_exists('\Pagerfanta\Pagerfanta')) {
            throw new \Exception('Para usar o paginator precisa instalar o Pagerfanta: composer req pagerfanta/pagerfanta.');
        }
    }

    /**
     * Quantidade de itens por página (padrão 10)
     * @return int
     */
    public function withPageLength(int $pageLength)
    {
        $this->pageLength = $pageLength;
        return $this;
    }

    /**
     * Nome do parâmetro da requisição (padrão: 'page')
     * @param string $pageParam
     * @return $this
     */
    public function withPageParam(string $pageParam)
    {
        $this->pageParam = $pageParam;
        return $this;
    }
    
    /**
     * Outros parâmetros para serem adicionados na paginação (exemplo: campo de busca).
     * @param array $params
     * @return $this
     */
    public function withExtraParams(array $params)
    {
        $this->extraParams = $params;
        return $this;
    }
    
    public function getPageLength(): int
    {
        return $this->pageLength;
    }
    
    public function getPageParam(): string
    {
        return $this->pageParam;
    }
    
    public function getExtraParams(): array
    {
        return $this->extraParams;
    }

    /**
     * @param Request $request
     * @param Query   $query
     * @param string  $routeName
     * @return Paginator
     */
    public function create(Request $request, Query $query, string $routeName): Paginator
    {
        $router     = $this->router;
        $pageParam  = $this->pageParam;
        $pageLength = $this->pageLength;
        $parameters = [];
        
        foreach ($this->extraParams as $extraParam) {
            $parameters[$extraParam] = $request->get($extraParam);
        }
        
        $routeGenerator = function ($page) use ($router, $routeName, $pageParam, $parameters) {
            $params = array_merge($parameters, [ $pageParam => $page ]);
            return $router->generate($routeName, $params);
        };
        
        $adapter    = new DoctrineORMAdapter($query);
        $pagerfanta = new Pagerfanta($adapter);
        $view       = new TwitterBootstrap4View();
        $options    = [
            'proximity' => 3,
            'prev_message' => '←',
            'next_message' => '→',
        ];
        
        try {
            $page = max(1, (int) $request->get($pageParam));
            $pagerfanta->setMaxPerPage($pageLength);
            $pagerfanta->setCurrentPage($page);
        
            $html   = $view->render($pagerfanta, $routeGenerator, $options);
            $result = $pagerfanta->getCurrentPageResults();
        } catch (\Pagerfanta\Exception\OutOfRangeCurrentPageException $e) {
            $html   = '';
            $result = [];
        }
        
        return new Paginator($result, $html, $routeName);
    }
}
