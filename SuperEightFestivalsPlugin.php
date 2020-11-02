<?php

ini_set('max_execution_time', 0);

require_once dirname(__FILE__) . '/helpers/S8FLogger.php';
require_once dirname(__FILE__) . '/helpers/IOFunctions.php';
require_once dirname(__FILE__) . '/helpers/SuperEightFestivalsFunctions.php';
require_once dirname(__FILE__) . '/helpers/DBFunctions.php';
require_once dirname(__FILE__) . '/helpers/ControllersHelper.php';
require_once dirname(__FILE__) . '/helpers/CountryHelper.php';

class SuperEightFestivalsPlugin extends Omeka_Plugin_AbstractPlugin
{
    protected $_hooks = array(
        'install', // when the plugin is installed
        'uninstall', // when the plugin is uninstalled
        'initialize', // when the plugin starts up
        'define_routes', // to add our custom routes
        'admin_head', // override admin head to add custom modules
    );
    protected $_filters = array(
        'admin_navigation_main', // admin sidebar
        'public_navigation_main', // admin sidebar
    );
    protected $_options = array();

    public function hookInstall()
    {
        // create directories
        create_plugin_directories();

        // Setup database
        drop_tables(); // out with the old tables
        create_tables(); // in with the new tables

        // sample data
//        $this->add_sample_data();
        // all defaults used in the website
        $this->add_default_data();
    }

    function add_default_data()
    {
        $defaultCountriesFile = __DIR__ . "/__res/default-countries.txt";
        if (file_exists($defaultCountriesFile)) {
            $fn = fopen($defaultCountriesFile, "r");
            while (!feof($fn)) {
                $result = fgets($fn);
                list($countryName, $lat, $long) = explode(",", trim($result));
                try {
                    $country = new SuperEightFestivalsCountry();
                    $country->name = $countryName;
                    $country->latitude = $lat;
                    $country->longitude = $long;
                    $country->save();
                } catch (Throwable $e) {
                    logger_log(LogLevel::Error, "Failed to add country. " . $e->getMessage());
                }
            }
            fclose($fn);
        }

        $defaultCitiesFile = __DIR__ . "/__res/default-cities.txt";
        if (file_exists($defaultCitiesFile)) {
            $fn = fopen($defaultCitiesFile, "r");
            while (!feof($fn)) {
                $result = fgets($fn);
                list($countryName, $cityName, $lat, $long) = explode(",", trim($result));
                try {
                    $city = new SuperEightFestivalsCity();
                    $country = SuperEightFestivalsCountry::get_by_name($countryName);
                    if (!$country) {
                        logger_log(LogLevel::Error, "Failed to add city: no country found with name '${countryName}'");
                        continue;
                    }
                    $city->country_id = $country->id;
                    $city->name = $cityName;
                    $city->latitude = $lat;
                    $city->longitude = $long;
                    $city->save();
                } catch (Throwable $e) {
                    logger_log(LogLevel::Error, "Failed to add city. " . $e->getMessage());
                }
            }
            fclose($fn);
        }
    }

    public function hookInitialize()
    {
    }

    function hookUninstall()
    {
        // Drop tables
        drop_tables();

        // delete files
        delete_plugin_directories();
    }

    function hookAdminHead()
    {
        echo "<script type='module' src='/plugins/SuperEightFestivals/views/admin/javascripts/components/s8f-modal.js'></script>\n";
        echo "<script type='module' src='/plugins/SuperEightFestivals/views/admin/javascripts/components/s8f-alerts-area.js'></script>\n";
        echo "<script type='module' src='/plugins/SuperEightFestivals/views/admin/javascripts/components/s8f-table.js'></script>\n";

        echo "<script type='module' src='/plugins/SuperEightFestivals/views/admin/javascripts/components/s8f-filmmakers-table.js'></script>\n";
        echo "<script type='module' src='/plugins/SuperEightFestivals/views/admin/javascripts/components/s8f-countries-table.js'></script>\n";
        echo "<script type='module' src='/plugins/SuperEightFestivals/views/admin/javascripts/components/s8f-cities-table.js'></script>\n";
        echo "<script type='module' src='/plugins/SuperEightFestivals/views/admin/javascripts/components/s8f-festivals-table.js'></script>\n";
    }

    public function filterAdminNavigationMain($nav)
    {
        $nav = array_filter($nav, function ($k) {
            $itemLabel = $k['label'];
            return !in_array(strtolower($itemLabel), array(
                "items",
                "collections",
                "item types",
                "items",
                "tags",
            ));
        });
        $nav[] = array(
            'label' => __('Super 8 Festivals'),
            'uri' => url('super-eight-festivals'),
        );
        return $nav;
    }

    public function filterPublicNavigationMain($nav)
    {
        $nav = array(
            array(
                'label' => 'About',
                'uri' => '/about',
            ),
            array(
                'label' => 'Federation',
                'uri' => '/federation',
            ),
            array(
                'label' => 'History',
                'uri' => '/federation#history',
            ),
            array(
                'label' => 'Filmmakers',
                'uri' => '/filmmakers',
            ),
            array(
                'label' => 'Festival Cities',
                'uri' => '/cities',
            ),
        );
        return $nav;
    }

    function id_from_route($route)
    {
        $route = str_replace("/", "_", $route);
        $route = str_replace(":", "", $route);
        if (preg_match('/_$/', $route)) $route = substr($route, 0, strlen($route) - 1);
        return $route;
    }

    function add_route($router, $route, $controller, $action)
    {
        $router->addRoute("s8f_" . $this->id_from_route($route), new Zend_Controller_Router_Route($route, array(
            'module' => 'super-eight-festivals',
            'controller' => $controller,
            'action' => $action
        )));
    }

    function add_api_route($router, $full_route, $action)
    {
        $this->add_route($router, $full_route, "api", $action);
    }

    function add_static_route($router, $id, $fullRoute, $action, $adminOnly)
    {
        $router->addRoute(
            $id,
            new Zend_Controller_Router_Route(
                $fullRoute,
                array(
                    'module' => 'super-eight-festivals',
                    'controller' => $adminOnly ? "admin" : "public",
                    "action" => $action,
                )
            )
        );
    }

    function hookDefineRoutes($args)
    {
        $router = $args['router'];

        if (is_admin_theme()) {
            // Route: /countries/
            $this->add_route($router, ":module/countries/", "admin-countries", "index");
            // Route: /countries/[country]/
            $this->add_route($router, ":module/countries/:country/", "admin-countries", "single");
            // Route: /countries/[country]/cities/
            $this->add_route($router, ":module/countries/:country/cities/", "admin-country-cities", "index");
            // Route: /countries/[country]/cities/[city]/
            $this->add_route($router, ":module/countries/:country/cities/:city/", "admin-country-cities", "single");
            // Route: /countries/[country]/cities/[city]/banners/
            $this->add_route($router, ":module/countries/:country/cities/:city/banners/", "admin-country-city-banners", "index");
            $this->add_route($router, ":module/countries/:country/cities/:city/banners/:banner/", "admin-country-city-banners", "single");
            $this->add_route($router, ":module/countries/:country/cities/:city/banners/:banner/edit/", "admin-country-city-banners", "edit");
            $this->add_route($router, ":module/countries/:country/cities/:city/banners/:banner/delete/", "admin-country-city-banners", "delete");
            $this->add_route($router, ":module/countries/:country/cities/:city/banners/add/", "admin-country-city-banners", "add");
            // Route: /countries/[country]/cities/[city]/festivals/
            $this->add_route($router, ":module/countries/:country/cities/:city/festivals/", "admin-country-city-festivals", "index");
            // Route: /countries/[country]/cities/[city]/festivals/[festival]/
            $this->add_route($router, ":module/countries/:country/cities/:city/festivals/:festivalID/", "admin-country-city-festivals", "single");
            // Route: /countries/[country]/cities/[city]/festivals/[festival]/film-catalogs/
            $this->add_route($router, ":module/countries/:country/cities/:city/festivals/:festivalID/film-catalogs/", "admin-country-city-festival-film-catalogs", "index");
            $this->add_route($router, ":module/countries/:country/cities/:city/festivals/:festivalID/film-catalogs/:filmCatalogID/", "admin-country-city-festival-film-catalogs", "single");
            $this->add_route($router, ":module/countries/:country/cities/:city/festivals/:festivalID/film-catalogs/:filmCatalogID/edit/", "admin-country-city-festival-film-catalogs", "edit");
            $this->add_route($router, ":module/countries/:country/cities/:city/festivals/:festivalID/film-catalogs/:filmCatalogID/delete/", "admin-country-city-festival-film-catalogs", "delete");
            $this->add_route($router, ":module/countries/:country/cities/:city/festivals/:festivalID/film-catalogs/add/", "admin-country-city-festival-film-catalogs", "add");
            // Route: /countries/[country]/cities/[city]/festivals/[festival]/films/
            $this->add_route($router, ":module/countries/:country/cities/:city/festivals/:festivalID/films/", "admin-country-city-festival-films", "index");
            $this->add_route($router, ":module/countries/:country/cities/:city/festivals/:festivalID/films/:filmID/", "admin-country-city-festival-films", "single");
            $this->add_route($router, ":module/countries/:country/cities/:city/festivals/:festivalID/films/:filmID/edit/", "admin-country-city-festival-films", "edit");
            $this->add_route($router, ":module/countries/:country/cities/:city/festivals/:festivalID/films/:filmID/delete/", "admin-country-city-festival-films", "delete");
            $this->add_route($router, ":module/countries/:country/cities/:city/festivals/:festivalID/films/add/", "admin-country-city-festival-films", "add");
            // Route: /countries/[country]/cities/[city]/festivals/[festival]/memorabilia/
            $this->add_route($router, ":module/countries/:country/cities/:city/festivals/:festivalID/memorabilia/", "admin-country-city-festival-memorabilia", "index");
            $this->add_route($router, ":module/countries/:country/cities/:city/festivals/:festivalID/memorabilia/:memorabiliaID/", "admin-country-city-festival-memorabilia", "single");
            $this->add_route($router, ":module/countries/:country/cities/:city/festivals/:festivalID/memorabilia/:memorabiliaID/edit/", "admin-country-city-festival-memorabilia", "edit");
            $this->add_route($router, ":module/countries/:country/cities/:city/festivals/:festivalID/memorabilia/:memorabiliaID/delete/", "admin-country-city-festival-memorabilia", "delete");
            $this->add_route($router, ":module/countries/:country/cities/:city/festivals/:festivalID/memorabilia/add/", "admin-country-city-festival-memorabilia", "add");
            // Route: /countries/[country]/cities/[city]/festivals/[festival]/print-media/
            $this->add_route($router, ":module/countries/:country/cities/:city/festivals/:festivalID/print-media/", "admin-country-city-festival-print-media", "index");
            $this->add_route($router, ":module/countries/:country/cities/:city/festivals/:festivalID/print-media/:printMediaID/", "admin-country-city-festival-print-media", "single");
            $this->add_route($router, ":module/countries/:country/cities/:city/festivals/:festivalID/print-media/:printMediaID/edit/", "admin-country-city-festival-print-media", "edit");
            $this->add_route($router, ":module/countries/:country/cities/:city/festivals/:festivalID/print-media/:printMediaID/delete/", "admin-country-city-festival-print-media", "delete");
            $this->add_route($router, ":module/countries/:country/cities/:city/festivals/:festivalID/print-media/add/", "admin-country-city-festival-print-media", "add");
            // Route: /countries/[country]/cities/[city]/festivals/[festival]/photos/
            $this->add_route($router, ":module/countries/:country/cities/:city/festivals/:festivalID/photos/", "admin-country-city-festival-photos", "index");
            $this->add_route($router, ":module/countries/:country/cities/:city/festivals/:festivalID/photos/:photoID/", "admin-country-city-festival-photos", "single");
            $this->add_route($router, ":module/countries/:country/cities/:city/festivals/:festivalID/photos/:photoID/edit/", "admin-country-city-festival-photos", "edit");
            $this->add_route($router, ":module/countries/:country/cities/:city/festivals/:festivalID/photos/:photoID/delete/", "admin-country-city-festival-photos", "delete");
            $this->add_route($router, ":module/countries/:country/cities/:city/festivals/:festivalID/photos/add/", "admin-country-city-festival-photos", "add");
            // Route: /countries/[country]/cities/[city]/festivals/[festival]/posters/
            $this->add_route($router, ":module/countries/:country/cities/:city/festivals/:festivalID/posters/", "admin-country-city-festival-posters", "index");
            $this->add_route($router, ":module/countries/:country/cities/:city/festivals/:festivalID/posters/:posterID/", "admin-country-city-festival-posters", "single");
            $this->add_route($router, ":module/countries/:country/cities/:city/festivals/:festivalID/posters/:posterID/edit/", "admin-country-city-festival-posters", "edit");
            $this->add_route($router, ":module/countries/:country/cities/:city/festivals/:festivalID/posters/:posterID/delete/", "admin-country-city-festival-posters", "delete");
            $this->add_route($router, ":module/countries/:country/cities/:city/festivals/:festivalID/posters/add/", "admin-country-city-festival-posters", "add");

            // Route: /filmmakers/
            $this->add_route($router, ":module/filmmakers/", "admin-filmmakers", "index");
            // Route: /filmmakers/[filmmaker]/
            $this->add_route($router, ":module/filmmakers/:filmmakerID/", "admin-filmmakers", "single");
            $this->add_route($router, ":module/filmmakers/:filmmakerID/edit/", "admin-filmmakers", "edit");
            $this->add_route($router, ":module/filmmakers/:filmmakerID/delete/", "admin-filmmakers", "delete");
            $this->add_route($router, ":module/filmmakers/add/", "admin-filmmakers", "add");
            $this->add_route($router, ":module/filmmakers/:filmmakerID/photos/", "admin-filmmaker-photos", "index");
            $this->add_route($router, ":module/filmmakers/:filmmakerID/photos/:filmmakerPhotoID/edit/", "admin-filmmaker-photos", "edit");
            $this->add_route($router, ":module/filmmakers/:filmmakerID/photos/:filmmakerPhotoID/delete/", "admin-filmmaker-photos", "delete");
            $this->add_route($router, ":module/filmmakers/:filmmakerID/photos/add/", "admin-filmmaker-photos", "add");

            // Route: /federation/
            $this->add_route($router, ":module/federation/", "admin-federation", "index");
            $this->add_route($router, ":module/federation/newsletters/", "admin-federation-newsletters", "index");
            $this->add_route($router, ":module/federation/newsletters/:newsletterID/", "admin-federation-newsletters", "single");
            $this->add_route($router, ":module/federation/newsletters/:newsletterID/edit/", "admin-federation-newsletters", "edit");
            $this->add_route($router, ":module/federation/newsletters/:newsletterID/delete/", "admin-federation-newsletters", "delete");
            $this->add_route($router, ":module/federation/newsletters/add/", "admin-federation-newsletters", "add");
            $this->add_route($router, ":module/federation/photos/", "admin-federation-photos", "index");
            $this->add_route($router, ":module/federation/photos/:photoID/", "admin-federation-photos", "single");
            $this->add_route($router, ":module/federation/photos/:photoID/edit/", "admin-federation-photos", "edit");
            $this->add_route($router, ":module/federation/photos/:photoID/delete/", "admin-federation-photos", "delete");
            $this->add_route($router, ":module/federation/photos/add/", "admin-federation-photos", "add");
            $this->add_route($router, ":module/federation/magazines/", "admin-federation-magazines", "index");
            $this->add_route($router, ":module/federation/magazines/:magazineID/", "admin-federation-magazines", "single");
            $this->add_route($router, ":module/federation/magazines/:magazineID/edit/", "admin-federation-magazines", "edit");
            $this->add_route($router, ":module/federation/magazines/:magazineID/delete/", "admin-federation-magazines", "delete");
            $this->add_route($router, ":module/federation/magazines/add/", "admin-federation-magazines", "add");
            $this->add_route($router, ":module/federation/bylaws/", "admin-federation-bylaws", "index");
            $this->add_route($router, ":module/federation/bylaws/:bylawID/", "admin-federation-bylaws", "single");
            $this->add_route($router, ":module/federation/bylaws/:bylawID/edit/", "admin-federation-bylaws", "edit");
            $this->add_route($router, ":module/federation/bylaws/:bylawID/delete/", "admin-federation-bylaws", "delete");
            $this->add_route($router, ":module/federation/bylaws/add/", "admin-federation-bylaws", "add");

            // Route: /debug/
            $this->add_route($router, ":module/debug/", "admin-debug", "index");
//            $this->add_route($router, ":module/debug/purge/all", "admin-debug", "debug-purge-all");
//            $this->add_static_route($router, "debug_purge_all", ":module/debug/purge/all", "debug-purge-all", true);
//            $this->add_static_route($router, "debug_purge_unused", ":module/debug/purge/unused", "debug-purge-unused", true);
//            $this->add_static_route($router, "debug_create_tables", ":module/debug/create-tables", "debug-create-tables", true);
//            $this->add_static_route($router, "debug_create_missing_columns", ":module/debug/create-missing-columns", "debug-create-missing-columns", true);
//            $this->add_static_route($router, "debug_create_directories", ":module/debug/create-directories", "debug-create-directories", true);
//            $this->add_static_route($router, "debug_generate_missing_thumbnails", ":module/debug/generate-missing-thumbnails", "debug-generate-missing-thumbnails", true);
//            $this->add_static_route($router, "debug_regenerate_all_thumbnails", ":module/debug/regenerate-all-thumbnails", "debug-regenerate-all-thumbnails", true);
//            $this->add_static_route($router, "debug_delete_all_thumbnails", ":module/debug/delete-all-thumbnails", "debug-delete-all-thumbnails", true);
//            $this->add_static_route($router, "debug_fix_festivals", ":module/debug/fix-festivals", "debug-fix-festivals", true);
//            $this->add_static_route($router, "debug_relocate_files", ":module/debug/relocate-files", "debug-relocate-files", true);

            // Route: /staff/
            $this->add_route($router, ":module/staff/", "admin-staff", "index");
            $this->add_route($router, ":module/staff/:staffID/", "admin-staff", "single");
            $this->add_route($router, ":module/staff/:staffID/edit", "admin-staff", "edit");
            $this->add_route($router, ":module/staff/:staffID/delete/", "admin-staff", "delete");
            $this->add_route($router, ":module/staff/add/", "admin-staff", "add");

            // Route: /contributor/
            $this->add_route($router, ":module/contributors/", "admin-contributors", "index");
            $this->add_route($router, ":module/contributors/:contributorID/", "admin-contributors", "single");
            $this->add_route($router, ":module/contributors/:contributorID/edit", "admin-contributors", "edit");
            $this->add_route($router, ":module/contributors/:contributorID/delete/", "admin-contributors", "delete");
            $this->add_route($router, ":module/contributors/add/", "admin-contributors", "add");


        } else {
//            $this->add_public_static_route($router, "index", "", "index"); // commented out because the theme should handle the index
            $this->add_static_route($router, "search", "search", "search", false);
            $this->add_static_route($router, "about", "about", "about", false);
            $this->add_static_route($router, "contact", "contact", "contact", false);
            $this->add_static_route($router, "submit", "submit", "submit", false);
            $this->add_static_route($router, "federation", "federation", "federation", false);
            $this->add_static_route($router, "cities", "cities", "cities", false);
            $this->add_static_route($router, "city", "cities/:city", "city", false);
            $this->add_static_route($router, "filmmakers", "filmmakers", "filmmakers", false);
            $this->add_static_route($router, "filmmaker", "filmmakers/:filmmakerID", "filmmaker", false);
        }

        // ADD API ROUTES
        $this->add_api_route($router, "/rest-api/", "index");
        // users
        $this->add_api_route($router, "/rest-api/users/", "all-users");
        $this->add_api_route($router, "/rest-api/users/:user/", "single-user");
        $this->add_api_route($router, "/rest-api/users/add/", "add-user");
        // filmmakers
        $this->add_api_route($router, "/rest-api/filmmakers/", "all-filmmakers");
        $this->add_api_route($router, "/rest-api/filmmakers/:filmmakerID/", "single-filmmaker");
        $this->add_api_route($router, "/rest-api/filmmakers/add/", "add-filmmaker");
        // countries
        $this->add_api_route($router, "/rest-api/countries/", "all-countries");
        $this->add_api_route($router, "/rest-api/countries/:country/", "single-country");
        $this->add_api_route($router, "/rest-api/countries/add/", "add-country");
        // cities
        $this->add_api_route($router, "/rest-api/cities/", "all-cities");
        $this->add_api_route($router, "/rest-api/cities/:city/", "single-city");
        $this->add_api_route($router, "/rest-api/countries/:country/cities/", "country-all-cities");
        $this->add_api_route($router, "/rest-api/countries/:country/cities/:city/", "country-single-city");
        $this->add_api_route($router, "/rest-api/countries/:country/cities/add/", "country-add-city");
        // festivals
        $this->add_api_route($router, "/rest-api/festivals/", "all-festivals");
        $this->add_api_route($router, "/rest-api/festivals/:festival/", "single-festival");
        $this->add_api_route($router, "/rest-api/cities/:city/festivals/", "city-all-festivals");
        $this->add_api_route($router, "/rest-api/cities/:city/festivals/:festival/", "city-single-festival");
        $this->add_api_route($router, "/rest-api/cities/:city/festivals/add/", "city-add-festival");
        $this->add_api_route($router, "/rest-api/countries/:country/cities/:city/festivals/", "country-city-all-festivals");
        $this->add_api_route($router, "/rest-api/countries/:country/cities/:city/festivals/:festival/", "country-city-single-festival");
        $this->add_api_route($router, "/rest-api/countries/:country/cities/:city/festivals/add/ ", "country-city-add-festival");
    }

}