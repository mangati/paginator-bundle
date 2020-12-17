# Paginator Bundle

Symfony 4+ paginator bundle.


## Usage

Controller:

```php
// app controller

/**
 * @Route("/", name="my_index_route")
 */
public function index(Request $request, PaginatorFactory $paginatorFactory)
{
    $qb = $this
        ->getDoctrine()
        ->getManager()
        ->createQueryBuilder()
        ->select('e', 's')
        ->from(Entity::class, 'e');
    
    $query = $qb->getQuery();
    
    $paginator = $paginatorFactory
        ->withExtraParams(['q'])
        ->create(
            $request,
            $query,
            'my_index_route'
        );
    
    return $this->render('index.html.twig', [
        'paginator' => $paginator,
    ]);
}
```

View:

```twig
{# index.html.twig #}

<table>
    <thead>
        <tr>
            ...
        </tr>
    </thead>
    <tbody>
        {% for entity in paginator.result %}
            <tr>
                ...
            </tr>
        {% endfor %}
    </tbody>
</table>

{{ paginator.html|raw }}
```