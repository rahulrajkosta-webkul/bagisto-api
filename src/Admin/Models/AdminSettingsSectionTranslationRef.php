<?php

namespace Webkul\BagistoApi\Admin\Models;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use Illuminate\Database\Eloquent\Model;

/**
 * Theme translation — nested sub-resource of AdminSettingsSection (`translations`
 * connection). Backed by `theme_section_translations` as a plain HasMany
 * (standard FK `section_id` → no pivot gotcha).
 *
 * `options` is genuinely dynamic theme-config JSON, so it stays a JSON scalar
 * node field (never objectified). `locale` surfaces as `locale`, the row id as
 * `_id`, via the central converter.
 */
#[ApiResource(
    shortName: 'AdminSettingsThemeTranslationRef',
    operations: [],
    graphQlOperations: [],
    normalizationContext: ['attributes' => ['id', 'locale', 'options']],
)]
class AdminSettingsSectionTranslationRef extends Model
{
    /** @var string */
    protected $table = 'theme_section_translations';

    /** @var bool */
    public $timestamps = false;

    /** @var array */
    protected $casts = [
        'id' => 'int',
        'options' => 'array',
    ];

    /**
     * `normalizationContext` keeps drafts out of the serialized payload, but the GraphQL
     * type is built from the table's columns, so the draft column has to be hidden too or
     * it surfaces as a `draftOptions` field on the type.
     *
     * @var array
     */
    protected $hidden = [
        'draft_options',
    ];

    #[ApiProperty(identifier: true, writable: false)]
    public function getId(): ?int
    {
        return $this->id !== null ? (int) $this->id : null;
    }
}
