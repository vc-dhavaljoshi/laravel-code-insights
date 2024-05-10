<?php

namespace Vcian\LaravelCodeInsights\Http\Controllers;

use Illuminate\Support\Facades\File;
use ReflectionException;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CodeDocController
{
    /**
     * @param Request $request
     * @return View
     * @throws ReflectionException
     */
    public function index(Request $request): View
    {
        return view(config('code-insights.public.folder') . '::doc', [
            'controllers' => $this->controllers(),
            'models' => $this->models(),
            'methods' => $this->methods($request->class ?? ''),
            'repositories' => $this->repositories(),
            'services' => $this->services(),
            'sourceCode' => $this->sourceCode($request->class ?? ''),
            'providers' => $this->providers(),
            'helpers' => $this->helpers(),
            'activeClass' => $request->class ?? '',
            'breadcrumb' => $this->breadcrumb($request->class),
        ]);
    }

    /**
     * @return object
     */
    public function controllers(): object
    {
        return $this->collection(app_path('Http/Controllers'), config('code-insights.namespaces.controllers'));
    }

    /**
     * @return object
     */
    public function models(): object
    {
        return $this->collection(app_path('Models'), config('code-insights.namespaces.models'));
    }

    /**
     * @return object
     */
    public function repositories(): object
    {
        return $this->collection(app_path('Repositories'), config('code-insights.namespaces.repositories'));
    }

    /**
     * @return object
     */
    public function services(): object
    {
        return $this->collection(app_path('Services'), config('code-insights.namespaces.services'));
    }

    /**
     * @return object
     */
    public function helpers(): object
    {
        return $this->collection(app_path('Helpers'), config('code-insights.namespaces.helpers'));
    }

    /**
     * @param string $class
     * @return object
     * @throws ReflectionException
     */
    public function methods(string $class): object
    {
        return collect()
            ->when(class_exists($class) || trait_exists($class), function ($collection) use ($class) {
                $reflectionClass = new \ReflectionClass($class);
                foreach ($reflectionClass->getMethods() as $method) {
                    if ($method->class === $class &&
                        !$method->isStatic() &&
                        $method->getName() !== '__construct' &&
                        !str_contains($method->getFileName(), 'vendor')
                    ) {
                        $collection->push(['name' => $method->getName()]);
                    }
                }
                return $collection;
            });
    }

    /**
     * @param string $class
     * @return string
     * @throws ReflectionException
     */
    public function sourceCode(string $class): string
    {
        if ($class) {
            $reflectionClass = new \ReflectionClass($class);
            return file_get_contents($reflectionClass->getFileName());
        }

        return "";
    }

    /**
     * @return object
     */
    public function providers(): object
    {
        return collect(File::glob(app_path('Providers') . '/*.php'))
            ->map(function ($file) {
                $className = basename($file, '.php');
                return [
                    'name' => "App\\Providers\\$className",
                    'shortName' => $className,
                ];
            });
    }

    /**
     * @param string $data
     * @return string
     */
    public function stringFilter(string $data): string
    {
        return str_replace(
            ['/', '.php'],
            ['\\', ''],
            $data
        );
    }

    /**
     * @param $class
     * @return array
     */
    public function classValidation($class): array
    {
        if (class_exists($class)) {
            $reflectionClass = new \ReflectionClass($class);
            return [
                'name' => $class,
                'shortName' => $reflectionClass->getShortName()
            ];
        }

        return [];
    }

    /**
     * @param $path
     * @param $namespace
     * @return object
     */
    public function collection($path, $namespace): object
    {
        return collect(is_dir($path) ? File::allFiles($path) : [])
            ->map(function ($file) use ($namespace) {
                return $this->classValidation(
                    $namespace . $this->stringFilter($file->getRelativePathname())
                );
            })
            ->filter()
            ->values();
    }

    /**
     * @param $class
     * @return string
     */
    public function breadcrumb($class): string
    {
        $segments = explode("\\", $class);
        $lastTwoSegments = array_slice($segments, -2);

        return ($class) ? ($lastTwoSegments[0] ?? '') . '/' . ($lastTwoSegments[1] ?? '') : '';
    }
}
