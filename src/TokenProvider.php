<?php

namespace ChrisHardie\CrowdtangleApi;

interface TokenProvider
{
    public function getToken(): string;
}