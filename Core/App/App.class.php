<?php

use Router\{
    Request,
    Router
};

class App {

    public Request $request;
    public array $serverRequest;
    public array $routeList;
    public Router $router;

    public function __construct(array $server, array $routes) {
        $this->request = new Request();
        $this->serverRequest = $server;
        $this->routeList = $routes;
        $this->router = new Router($this->request, $this->serverRequest, $this->routeList);
    }

    public function run() {
        echo $this->router->resolve();
    }
}
