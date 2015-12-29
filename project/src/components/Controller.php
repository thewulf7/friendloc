<?php
namespace thewulf7\friendloc\components;


use thewulf7\friendloc\services\
{
    AuthService, UserService, LocationService, MapService, EmailService, SearchService
};
use thewulf7\friendloc\components\router\Response;
use thewulf7\friendloc\models\User;

/**
 * Class Controller
 *
 * @package thewulf7\friendloc\components
 * @method \thewulf7\friendloc\services\AuthService getAuthService()
 * @method \thewulf7\friendloc\services\EmailService getEmailService()
 * @method \thewulf7\friendloc\services\LocationService getLocationService()
 * @method \thewulf7\friendloc\services\MapService getMapService()
 * @method \thewulf7\friendloc\services\SearchService getSearchService()
 * @method \thewulf7\friendloc\services\UserService getUserService()
 * @method \thewulf7\friendloc\models\User getCurrentUser()
 * @method mixed getTemplater()
 */
abstract class Controller
{
    use ApplicationHelper;

    /**
     * @var string
     */
    public $layout = 'main';

    /**
     * @param string $path
     */
    public function redirect(string$path = '')
    {
        header('Location: ' . $path);
    }

    /**
     * Template renderer
     *
     * @param string $view
     * @param array  $variables
     */
    public function render(string $view, array $variables = [])
    {
        $layout         = 'layout/' . $this->layout;
        $layoutTemplate = $this->getTemplater()->loadTemplate($layout . '.twig');
        $this->getTemplater()->display($view . '.twig', array_merge(
            [
                'layout' => $layoutTemplate,
                'title'  => $this->getContainer()->get('thewulf7\friendloc\components\config\iConfig')->get('appName'),
            ], $variables));
    }

    /**
     * Simple PHP renderer
     *
     * @param string $view
     * @param array  $variables
     * @param bool   $output
     */
    public function renderPHP(string $view, array $variables = [], bool $output = true)
    {
        $layout = 'layout/' . $this->layout;

        $this->renderPartial(
            $layout,
            [
                'content' => $this->renderPartial($view, $variables, false),
            ],
            $output
        );
    }

    /**
     * @param       $view
     * @param array $variables
     * @param bool  $output
     *
     * @return bool|string
     * @throws \Exception
     */
    public function renderPartial(string $view, array $variables = [], bool $output = true)
    {
        $file = __DIR__ . '/../views/' . $view . '.php';

        return $this->_renderPartial($file, $variables, $output);
    }

    /**
     * @param       $fullpath
     * @param array $variables
     * @param bool  $output
     *
     * @return bool|string
     * @throws \Exception
     */
    private function _renderPartial(string $fullpath, array $variables = [], bool $output = true)
    {
        extract($variables);

        if (file_exists($fullpath))
        {
            if (!$output)
            {
                ob_start();
            }
            include $fullpath;

            return !$output ? ob_get_clean() : true;
        } else
        {
            throw new \Exception('File ' . $fullpath . ' not found');
        }
    }

    /**
     * @return array
     */
    protected function guestAllowedMethods(): array
    {
        return [];
    }

    /**
     * @param string $method
     *
     * @return mixed
     */
    public function beforeAction(string $method)
    {

        $methods = $this->guestAllowedMethods();
        $model   = $this->getAuthService()->authByHash(Auth::getHash());

        if ($model === false && in_array($method, $methods, true))
        {
            return true;
        }

        return $model;
    }

    /**
     * @param mixed  $data
     * @param int    $code
     *
     * @return bool
     */
    public function sendResponse($data = [], int $code = 200): bool
    {
        http_response_code($code);
        echo json_encode(new Response($data));
        return true;
    }

    public function sendErrorResponse($data = [], int $code = 400): bool
    {
        http_response_code($code);
        echo json_encode(new Response(['errors' => $data, 'status' => $code]));
        return true;
    }
}