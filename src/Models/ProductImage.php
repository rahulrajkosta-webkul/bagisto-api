<?php

namespace Webkul\BagistoApi\Models;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\GraphQl\Query;
use ApiPlatform\Metadata\GraphQl\QueryCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\Response;
use Webkul\BagistoApi\Resolver\BaseQueryItemResolver;
use Webkul\BagistoApi\State\CursorAwareCollectionProvider;
use Webkul\BagistoApi\State\ProductImageProvider;
use Webkul\Product\Models\ProductImage as BaseProductImage;
use Webkul\Product\Models\ProductImageTranslation;

#[ApiResource(
    routePrefix: '/api/shop',
    shortName: 'ProductImages',
    uriTemplate: '/product-images',
    operations: [
        new GetCollection(
            provider: ProductImageProvider::class,
            openapi: new Operation(
                tags: ['Product'],
                summary: 'List product images (root collection)',
                description: 'Public endpoint. Returns all product images across the store.',
                responses: [
                    '200' => new Response(
                        description: 'Product image collection',
                        content: new \ArrayObject([
                            'application/json' => [
                                'example' => [
                                    [
                                        'id' => 967,
                                        'type' => 'images',
                                        'path' => 'product/1/zKcWZTLDjcawJmaNg8g1cpARqwVONgEKEflabstT.webp',
                                        'productId' => 1,
                                        'position' => 1,
                                        'publicPath' => 'http://localhost:8000/storage/product/1/zKcWZTLDjcawJmaNg8g1cpARqwVONgEKEflabstT.webp',
                                    ],
                                ],
                            ],
                        ]),
                    ),
                ],
            ),
        ),
    ],
    graphQlOperations: [
        new QueryCollection(
            provider: CursorAwareCollectionProvider::class,
            args: [
                'product_id' => ['type' => 'Int', 'description' => 'Filter by product ID'],
            ]
        ),
    ]
)]
#[ApiResource(
    routePrefix: '/api/shop',
    shortName: 'ProductImages',
    uriTemplate: '/product-images/{id}',
    operations: [
        new Get(
            provider: ProductImageProvider::class,
            openapi: new Operation(
                tags: ['Product'],
                summary: 'Get a single product image by ID',
                description: 'Public endpoint. Returns a single product image.',
                responses: [
                    '200' => new Response(
                        description: 'Product image',
                        content: new \ArrayObject([
                            'application/json' => [
                                'example' => [
                                    'id' => 967,
                                    'type' => 'images',
                                    'path' => 'product/1/zKcWZTLDjcawJmaNg8g1cpARqwVONgEKEflabstT.webp',
                                    'productId' => 1,
                                    'position' => 1,
                                    'publicPath' => 'http://localhost:8000/storage/product/1/zKcWZTLDjcawJmaNg8g1cpARqwVONgEKEflabstT.webp',
                                ],
                            ],
                        ]),
                    ),
                    '404' => new Response(
                        description: 'Product image not found.',
                    ),
                ],
            ),
        ),
    ],
    graphQlOperations: [
        new Query(resolver: BaseQueryItemResolver::class),
    ]
)]
#[ApiResource(
    routePrefix: '/api/shop',
    shortName: 'ProductImages',
    uriTemplate: '/products/{productId}/images',
    uriVariables: [
        'productId' => new Link(
            fromClass: Product::class,
            fromProperty: 'images',
            identifiers: ['id']
        ),
    ],
    operations: [
        new GetCollection(
            provider: ProductImageProvider::class,
            openapi: new Operation(
                tags: ['Product'],
                summary: 'List images for a product',
                description: 'Returns the image collection for the given product ID.',
                responses: [
                    '200' => new Response(
                        description: 'Product image collection',
                        content: new \ArrayObject([
                            'application/json' => [
                                'example' => [
                                    [
                                        'id' => 967,
                                        'type' => 'images',
                                        'path' => 'product/1/zKcWZTLDjcawJmaNg8g1cpARqwVONgEKEflabstT.webp',
                                        'productId' => 1,
                                        'position' => 1,
                                        'publicPath' => 'http://localhost:8000/storage/product/1/zKcWZTLDjcawJmaNg8g1cpARqwVONgEKEflabstT.webp',
                                    ],
                                ],
                            ],
                        ]),
                    ),
                ],
            ),
        ),
    ],
    graphQlOperations: []
)]
class ProductImage extends BaseProductImage
{
    /**
     * v2.4 made product images translatable (`alt_text`), and the translatable trait
     * derives the translation class from the model it is used on — which here is this
     * subclass, so it looks for a `ProductImageTranslation` in the API namespace and
     * fatals. Alt text is not part of this resource, so the parent's translation model is
     * named rather than wrapped.
     *
     * @var string
     */
    protected $translationModel = ProductImageTranslation::class;

    protected $visible = [
        'id',
        'type',
        'path',
        'product_id',
        'position',
        'public_path',
    ];

    #[ApiProperty(readable: true, writable: false)]
    public function getPublicPathAttribute(): ?string
    {
        return $this->getUrlAttribute();
    }

    #[ApiProperty(identifier: true, writable: false)]
    public function getId(): ?int
    {
        return $this->id;
    }
}
