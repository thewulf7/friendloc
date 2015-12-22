<?php
namespace thewulf7\friendloc\components;


use thewulf7\friendloc\services\
{
    AuthService, UserService
};

/**
 * Class Controller
 *
 * @package thewulf7\friendloc\components
 * @method AuthService getAuthService()
 * @method UserService getUserService()
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
    public function redirect($path = '')
    {
        header('Location: ' . $path);
    }

    /**
     * Template renderer
     *
     * @param string $view
     * @param array  $variables
     */
    public function render($view, $variables = [])
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
    public function renderPHP($view, $variables = [], $output = true)
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
    public function renderPartial($view, $variables = [], $output = true)
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
    private function _renderPartial($fullpath, $variables = [], $output = true)
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
     * @return bool
     */
    public function beforeAction(string $method): bool
    {
        $methods = $this->guestAllowedMethods();
        $model   = $this->getAuthService()->authByHash(Auth::getHash());

        if ($model === false && in_array($method, $methods, true))
        {
            return true;
        }

        if ($model === false)
        {
            $this->redirect('/auth/login');
        }

        return true;
    }
}