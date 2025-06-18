<?php

namespace Bigfork\Vitesse;

use Exception;
use SilverStripe\View\TemplateGlobalProvider;

class TemplateProvider implements TemplateGlobalProvider
{
    /**
     * Template usage: <% vite 'path/to/style.css', 'path/to/javascript.js' %>
     * @param array $res
     * @return string
     */
    public static function vite(array $res): string
    {
        $args = array_column($res['Arguments'] ?? [], 'php');
        $argsString = '[' . implode(', ', $args) . ']';
        return <<<PHP
\$val .= \Bigfork\Vitesse\Vite::inst()($argsString);
PHP;
    }

    /**
     * Template usage: <% viteReactRefresh %>
     * @return string
     */
    public static function viteReactRefresh(): string
    {
        return <<<PHP
\$val .= \Bigfork\Vitesse\Vite::inst()->reactRefresh();
PHP;
    }

    /**
     * Template usage: $viteAsset('path/to/image.jpg')
     * @param string $asset
     * @return string
     */
    public static function getViteAsset(string $asset): string
    {
        return Vite::inst()->asset($asset);
    }

    /**
     * Template usage: $viteContent('path/to/image.svg')
     * @param string $asset
     * @return string
     * @throws Exception
     */
    public static function getViteContent(string $asset): string
    {
        return Vite::inst()->content($asset);
    }

    /**
     * Template usage: <% if $viteIsRunningHot %><% end_if %>
     * @return bool
     */
    public static function getViteIsRunningHot(): bool
    {
        return Vite::inst()->isRunningHot();
    }

    public static function get_template_global_variables(): array
    {
        return [
            'viteAsset' => 'getViteAsset',
            'viteContent' => 'getViteContent',
            'viteIsRunningHot' => 'getViteIsRunningHot',
        ];
    }
}
