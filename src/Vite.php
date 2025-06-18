<?php

namespace Bigfork\Vitesse;

use Illuminate\Foundation\Vite as LaravelVite;
use SilverStripe\Control\Director;
use SilverStripe\Control\SimpleResourceURLGenerator;
use SilverStripe\Core\Injector\Injectable;
use SilverStripe\View\SSViewer;
use SilverStripe\View\ThemeResourceLoader;
use function Illuminate\Filesystem\join_paths;

class Vite extends LaravelVite
{
    use Injectable;

    protected static ?Vite $instance = null;

    protected string $publicPath = 'public';

    public static function inst(): static
    {
        return static::$instance ?: static::$instance = static::create();
    }

    public function usePublicPath(?string $path): static
    {
        $this->publicPath = $path;
        return $this;
    }

    public function hotFile(): string
    {
        return $this->hotFile ?? $this->publicPath('/hot');
    }

    protected function manifestPath($buildDirectory): string
    {
        return $this->publicPath($buildDirectory . '/' . $this->manifestFilename);
    }

    protected function publicPath($path = ''): string
    {
        $themes = array_filter(SSViewer::get_themes(), fn ($theme) => $theme !== '$public');
        $themeResourceLoader = ThemeResourceLoader::inst();
        $paths = $themeResourceLoader->getThemePaths($themes);
        $paths = array_filter($paths, fn ($path) => !str_starts_with($path, 'vendor'));
        foreach ($paths as $themePath) {
            $absoluteThemePath = join_paths(BASE_PATH, $themePath);
            $absolutePath = join_paths($absoluteThemePath, $this->publicPath, $path);
            if (file_exists($absolutePath)) {
                return $absolutePath;
            }
        }
        return join_paths(BASE_PATH, $this->publicPath, $path);
    }

    /**
     * Custom asset path resolver for Silverstripe paths, registered via YAML config
     * @param string $path
     * @return string
     */
    protected static function resolveAssetPath(string $path): string
    {
        $filePath = ThemeResourceLoader::inst()->findThemedResource($path);
        if (!$filePath) {
            return '';
        }

        $generator = new SimpleResourceURLGenerator();
        $generator->setNonceStyle(null); // Vite handles this
        return Director::absoluteURL($generator->urlForResource($filePath));
    }
}
