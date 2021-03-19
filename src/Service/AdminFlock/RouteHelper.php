<?php

namespace App\Service\AdminFlock;

use Doctrine\Common\Annotations\Reader as AnnotationReader;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

class RouteHelper
{
    private $annotation_reader;
    private $router;

    public function __construct(AnnotationReader $annotation_reader, RouterInterface $router)
    {
        $this->annotation_reader = $annotation_reader;
        $this->router = $router;
    }

    /**
     * given an entity, get route object from controller annotation
     *
     * @param Object $entity
     * @return Route
     */
    public function getRoute($entity): ?Route
    {
        try {
            $controllerNamespace = $this->getControllerNamespace($entity);
            $controllerReflection = new \ReflectionClass($controllerNamespace);

            return $this->annotation_reader->getClassAnnotation($controllerReflection, Route::class);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * get route name
     *
     * @param Object $entity
     * @param string|'' $action controller method\action
     * @return String
     */
    public function getRouteName($entity, $action = ''): ?string
    {
        $route = $this->getRoute($entity);

        if ($route) {
            return !empty($action) ? $route->getName() . '_' . $action : $route->getName();
        }

        return null;
    }

    private function getControllerNamespace($entity)
    {
        $namespaceArray = explode('\\', get_class($entity), 3);
        $namespacedEntityName = $namespaceArray[2];
        $namespacedEntityName = str_replace('App\Entity\\', '', $namespacedEntityName);
        return $controllerNamespace = 'App\\Controller\\' . $namespacedEntityName . 'Controller';
    }

    /**
     * @param string $route
     * @return string
     */
    public function getToken(string $route): string
    {
        $salt = 'as11129387dùùèò3409cvm,ìììì__';
        return md5($route . $salt . date('Ymd') . session_id());
    }
}