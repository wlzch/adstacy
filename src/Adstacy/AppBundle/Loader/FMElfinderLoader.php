<?php

namespace Adstacy\AppBundle\Loader;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use FM\ElFinderPHP\Connector\ElFinderConnector;
use FM\ElfinderBundle\Bridge\ElFinderBridge;

class FMElfinderLoader
{
    /**
     * @var array $options
     */
    protected $options = array();

    /**
     * @var ContainerInterface $container
     */
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->options = $this->configure();
    }

    /**
     * @return array
     */
    protected function configure()
    {
        $request = $this->container->get('request');
        $parameters = $this->container->getParameter('fm_elfinder');
        $username = $this->container->get('security.context')->getToken()->getUser()->getUsername();

        $options = array();
        $options['debug'] = $parameters['connector']['debug'];
        $options['roots'] = array();

        foreach ($parameters['connector']['roots'] as $parameter) {
            $path = $parameter['path'];
            $dir = sprintf('%s/%s/%s', $this->container->getParameter('web_dir'), $path, $username);
            if (!file_exists($dir)) mkdir($dir);
            $driver = $this->container->has($parameter['driver']) ? $this->container->get($parameter['driver']) : null;
            $options['roots'][] = array(
                'driver'        => $parameter['driver'],
                'service'       => $driver,
                'path'          => $path . '/' . $username . '/',
                'URL'           => isset($parameter['url']) && $parameter['url']
                    ? strpos($parameter['url'], 'http') === 0
                        ? $parameter['url']
                        : sprintf('%s://%s%s/%s/%s/', $request->getScheme(), $request->getHttpHost(), $request->getBasePath(), $parameter['url'], $username)
                    : sprintf('%s://%s%s/%s/%s/', $request->getScheme(), $request->getHttpHost(), $request->getBasePath(), $path, $username),
                'accessControl' => array($this, 'access'),
                'uploadAllow'   => $parameter['upload_allow'],
                'uploadDeny'    => $parameter['upload_deny'],
                'uploadMaxSize' => $parameter['upload_max_size']
            );
        }

        return $options;
    }

    /**
     * Starts the elFinder
     */
    public function load()
    {
        $connector = new ElFinderConnector(new ElFinderBridge($this->options));
        $connector->run();
    }

    /**
     * Simple function to demonstrate how to control file access using "accessControl" callback.
     * This method will disable accessing files/folders starting from '.' (dot)
     *
     * @param  string  $attr  attribute name (read|write|locked|hidden)
     * @param  string  $path  file path relative to volume root directory started with directory separator
     * @return bool|null
     **/
    public function access($attr, $path, $data, $volume) {
        	return strpos(basename($path), '.') === 0       // if file/folder begins with '.' (dot)
		? !($attr == 'read' || $attr == 'write')    // set read+write to false, other (locked+hidden) set to true
		:  null;                                    // else elFinder decide it itself
    }
}
