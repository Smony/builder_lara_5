<?php

$arrSegments = explode('/', Request::path());

if ($arrSegments[0] != 'admin') {
    try {
        $_model = config('builder.tree.model');
        if ($_model) {
            $slug = end($arrSegments);

            if (!$slug || $slug == LaravelLocalization::setLocale()) {
                $slug = '/';
            }

            if (config('builder.tree.extension')) {
                $slug = str_replace(config('builder.tree.extension'), '', $slug);
            }

            $nodes = $_model::where('slug', 'like', $slug)->get();
            foreach ($nodes as $node) {
                if ($node->getUrl() == Request::url()) {
                    break;
                }
            }

            if (isset($node->id)) {
                $slugTree = $slug;
                $_nodeUrl = trim($node->getUrlNoLocation(), '/');

                Route::group(
                    ['middleware' => ['web']],
                    function () {
                        Route::group(
                            ['prefix' => LaravelLocalization::setLocale()],
                            function () {
                                Route::get('{slug?}', [
                                    'as'   => 'route_admin',
                                    'uses' => 'Vis\Builder\TableAdminController@showPageUrlTree',
                                ])->where('slug', '.+');
                            }
                        );
                    }
                );
            }
        }
    } catch (Exception $e) {
    }

    /*
     * other tree
     */
    $otherTreeUrl = config('builder.tree.other_tree_url');

    if ($otherTreeUrl && is_array($otherTreeUrl)) {
        $startUrl = $arrSegments[0];

        if ($arrSegments[0] == LaravelLocalization::setLocale()) {
            if (isset($arrSegments[1])) {
                $startUrl = $arrSegments[1];
            } else {
                $startUrl = '/';
            }
        }

        $urls = array_keys($otherTreeUrl);

        if ($urls && count($urls) && in_array($startUrl, $urls)) {
            if (isset($otherTreeUrl[$startUrl])) {
                $configName = $otherTreeUrl[$startUrl];

                $definition = config('builder.'.$configName);
                $model = $definition['model'];

                $slug = end($arrSegments);

                if (!isset($slugTree)) {
                    $nodes = $model::where('slug', 'like', $slug)->get();

                    foreach ($nodes as $node) {
                        if ($node->getUrl() == Request::url()) {
                            break;
                        }
                    }

                    if (isset($node->id)) {
                        $_nodeUrl = $node->getUrlNoLocation();
                        $templates = $definition['templates'];

                        $middleware = ['web'];
                        if (isset($templates[$node->template]['middleware']) && !empty($templates[$node->template]['middleware'])) {
                            foreach ((array) $templates[$node->template]['middleware'] as $midWare) {
                                $middleware[] = $midWare;
                            }
                        }

                        Route::group(
                            ['middleware' => $middleware],
                            function () use ($node, $_nodeUrl, $templates) {
                                Route::group(
                                    ['prefix' => LaravelLocalization::setLocale()],
                                    function () use ($node, $_nodeUrl, $templates) {
                                        Route::get(
                                            $_nodeUrl,
                                            function () use ($node, $templates) {
                                                if (!isset($templates[$node->template])) {
                                                    App::abort(404);
                                                }

                                                list($controller, $method) = explode('@', $templates[$node->template]['action']);

                                                $app = app();
                                                $controller = $app->make('App\\Http\\Controllers\\'.$controller);

                                                return $controller->callAction('init', [$node, $method]);
                                            }
                                        );
                                    }
                                );
                            }
                        );
                    }
                }
            }
        }
    }
}
