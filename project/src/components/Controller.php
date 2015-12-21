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
 */
abstract class Controller
{
    use ApplicationHelper;

    public $layout = 'main';

    /**
     * @param string $path
     */
    public function redirect($path = '')
    {
        header('Location: ' . $path);
    }

    /**
     * Simple PHP renderer
     *
     * @param string $view
     * @param array  $variables
     * @param bool   $output
     */
    public function render($view, $variables = [], $output = true)
    {
        $layout = 'layout/' . $this->layout;

        $this->renderPartial(
            $layout,
            [
                'content' => $this->renderPartial($view, $variables, false)
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
}