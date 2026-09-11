<?php

namespace Webkul\BagistoApi\Admin\Models;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GraphQl\Mutation;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model;
use Webkul\BagistoApi\Admin\Dto\AdminSettingsSectionMassDeleteInput;
use Webkul\BagistoApi\Admin\State\AdminSettingsSectionMassDeleteProcessor;

/**
 * Mass-delete admin settings theme sections.
 *
 * REST:    POST /api/admin/settings/themes/mass-delete
 * GraphQL: createAdminSettingsThemeMassDelete
 *
 * A bulk convenience this API adds on top of Appearance\SectionController::destroy;
 * v2.4 dropped the admin panel's own mass-delete screen. Non-existent IDs are
 * silently skipped (best-effort loop).
 */
#[ApiResource(
    routePrefix: '/api/admin',
    shortName: 'AdminSettingsThemeMassDelete',
    normalizationContext: ['skip_null_values' => false],
    operations: [
        new Post(
            uriTemplate: '/settings/themes/mass-delete',
            input: AdminSettingsSectionMassDeleteInput::class,
            processor: AdminSettingsSectionMassDeleteProcessor::class,
            status: 200,
            openapi: new Model\Operation(
                tags: ['Admin Settings: Themes'],
                summary: 'Mass delete theme customizations',
                requestBody: new Model\RequestBody(
                    required: true,
                    content: new \ArrayObject([
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'required' => ['indices'],
                                'properties' => [
                                    'indices' => ['type' => 'array', 'items' => ['type' => 'integer'], 'example' => [3, 4]],
                                ],
                            ],
                        ],
                    ]),
                ),
                responses: [
                    '200' => new Model\Response(description: 'Themes deleted.'),
                    '422' => new Model\Response(description: 'Empty indices.'),
                ],
            ),
        ),
    ],
    graphQlOperations: [
        new Mutation(
            name: 'create',
            input: AdminSettingsSectionMassDeleteInput::class,
            processor: AdminSettingsSectionMassDeleteProcessor::class,
            description: 'Mass-delete theme customizations. Becomes createAdminSettingsThemeMassDelete.',
        ),
    ],
)]
class AdminSettingsSectionMassDelete
{
    #[ApiProperty(identifier: true, writable: false)]
    public ?int $id = null;

    /** @var int[]|null */
    #[ApiProperty(writable: false)]
    public ?array $deleted = null;

    #[ApiProperty(writable: false)]
    public ?string $message = null;
}
