<?php

namespace simple_seo_api;

interface ThreadRequests{
    public function addPage($page);
    public function exec();
}