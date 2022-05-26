<?php

declare(strict_types=1);

namespace App\Model\Repository;

interface IRepository
{
	public function findAll(): iterable;
}
