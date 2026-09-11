<?php

namespace Webkul\BagistoApi\Admin\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Webkul\BagistoApi\Admin\Dto\AdminSettingsSectionRestDto;
use Webkul\BagistoApi\Admin\Helper\AdminAuthHelper;
use Webkul\BagistoApi\Admin\Models\AdminSettingsSection;
use Webkul\BagistoApi\Exception\AuthenticationException;
use Webkul\BagistoApi\Exception\ResourceNotFoundException;
use Webkul\Theme\Models\Section;

/**
 * Theme detail — GET /api/admin/settings/themes/{id} + adminSettingsTheme query.
 *
 * Branches: GraphQL → the AdminSettingsSection Eloquent model (translations
 * resolves as a connection); REST → the flat AdminSettingsSectionRestDto.
 */
class AdminSettingsSectionItemProvider implements ProviderInterface
{
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): AdminSettingsSection|AdminSettingsSectionRestDto
    {
        if (! AdminAuthHelper::resolveAdmin()) {
            throw new AuthenticationException(__('bagistoapi::app.admin.profile.unauthenticated'));
        }

        $id = (int) basename((string) ($uriVariables['id'] ?? $context['args']['id'] ?? 0));

        if ($id <= 0) {
            throw new ResourceNotFoundException(__('bagistoapi::app.admin.settings.theme.not-found'));
        }

        if (! empty($context['graphql_operation_name'])) {
            $model = AdminSettingsSection::with('translations')->find($id);

            if (! $model) {
                throw new ResourceNotFoundException(__('bagistoapi::app.admin.settings.theme.not-found'));
            }

            return $model;
        }

        $theme = Section::with('translations')->find($id);

        if (! $theme) {
            throw new ResourceNotFoundException(__('bagistoapi::app.admin.settings.theme.not-found'));
        }

        return $this->buildRestDto($theme);
    }

    /**
     * Public alias used by the processor to reuse the REST mapping logic.
     */
    public function buildRestDtoPublic(object $theme): AdminSettingsSectionRestDto
    {
        return $this->buildRestDto($theme);
    }

    protected function buildRestDto(object $theme): AdminSettingsSectionRestDto
    {
        /** @var Section $theme */
        $dto = new AdminSettingsSectionRestDto;

        $dto->id = (int) $theme->id;
        $dto->name = $theme->name;
        $dto->type = $theme->type;
        $dto->sortOrder = (int) $theme->sort_order;
        $dto->status = (bool) $theme->status;
        $dto->channelId = (int) $theme->channel_id;
        $dto->themeCode = $theme->theme_code;
        $dto->createdAt = $theme->created_at?->toIso8601String();
        $dto->updatedAt = $theme->updated_at?->toIso8601String();

        $translations = [];
        foreach ($theme->translations ?? [] as $tr) {
            $translations[] = [
                'locale' => $tr->locale,
                'options' => $tr->options,
            ];
        }
        $dto->translations = $translations;

        return $dto;
    }
}
