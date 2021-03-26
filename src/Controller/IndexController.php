<?php

namespace App\Controller;

use App\Facades\View;
use Badcow\DNS\Parser\Parser;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Controller for the index
 *
 * @author Ronan Chilvers <ronan@d3r.com>
 */
class IndexController
{
    /**
     * Index action
     *
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function index(
        ServerRequestInterface $request,
        ResponseInterface $response
    ) {
        return View::render(
            $response,
            'index/index.html.twig'
        );
    }

    /**
     * Upload action
     *
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function upload(
        ServerRequestInterface $request,
        ResponseInterface $response
    ) {
        $uploads = $request->getUploadedFiles();
        $data = $request->getParam('data');

        if (!isset($uploads['data'], $uploads['data']['zone_file'])) {
            // @TODO Remove var_dump
            var_dump('invalid input'); exit();
        }
        $upload = $uploads['data']['zone_file'];
        if (UPLOAD_ERR_OK !== $upload->getError()) {
            // @TODO Remove var_dump
            var_dump('upload error'); exit();
        }

        $domain = $data['domain'];
        if ('.' !== substr($domain, -1)) {
            $domain .= '.';
        }
        if (false === filter_var($domain, FILTER_VALIDATE_DOMAIN)) {
            // @TODO Remove var_dump
            var_dump('invalid domain'); exit();
        }

        $servers = [];
        foreach ($data['servers'] as $server) {
            if (false !== filter_var($server, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
                $servers[] = $server;
            }
        }
        if (0 == count($servers)) {
            // @TODO Remove var_dump
            var_dump('no nameservers provided'); exit();
        }

        $file = (string) $upload->getStream();
        $zone = Parser::parse(
            $domain,
            $file
        );

        return View::render(
            $response,
            'index/upload.html.twig',
            [
                'zone' => $zone,
                'servers' => $servers,
            ]
        );
    }
}
