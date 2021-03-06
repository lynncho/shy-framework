<?php

namespace Shy\Core\Contracts;

use Throwable;
use Shy\Http\Contracts\Response;
use Shy\Http\Contracts\View;

interface ExceptionHandler
{
    /**
     * Set Throwable
     *
     * @param Throwable $throwable
     */
    public function set(Throwable $throwable);

    /**
     * Logging.
     *
     * @param Logger $logger
     */
    public function logging(Logger $logger);

    /**
     * Report.
     */
    public function report();

    /**
     * Response.
     *
     * @param Config $config
     * @param Response $response
     * @param View $view
     */
    public function response(Config $config = null, Response $response = null, View $view = null);

}
