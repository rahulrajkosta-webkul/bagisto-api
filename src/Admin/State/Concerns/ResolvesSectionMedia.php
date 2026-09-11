<?php

namespace Webkul\BagistoApi\Admin\State\Concerns;

/**
 * Where a section's uploads live on disk.
 *
 * v2.4 files uploads under the theme that owns them rather than a flat `theme/{id}`
 * directory, so deleting a section has to look the path up the same way
 * Webkul\Theme\Repositories\SectionRepository does.
 */
trait ResolvesSectionMedia
{
    protected function mediaDirectory(object $section): string
    {
        return 'themes/'.$section->theme_code.'/sections/'.$section->id;
    }
}
