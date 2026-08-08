<?php

namespace App\Repositories\Contracts;

interface ProductRepositoryInterface
{
    public function findById($externalProductId);
    public function all();
}