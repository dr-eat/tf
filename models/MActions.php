<?php
/**
 * MActions - Base object of different actions
 *
 * PHP version 8.2
 *
 * @author    Aleksandr Jermakovich <dreat@dreat.net>
 * @copyright 2025
 */
class MActions {
    public App $app;

    public function __construct()
    {
        $this->app = App::get();
    }
}