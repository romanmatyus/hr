<?php

declare(strict_types=1);

namespace App\Model;

use App\Model\Repository\IRepository;
use App\Model\Repository\EmployeeRepository;

/**
 * @property-read EmployeeRepository $employee
 */
class Model
{
	private array $repositories = [];


	public function addRepository(string $name, IRepository $repository): self
	{
		$this->repositories[$name] = $repository;
		return $this;
	}


	public function __get(string $name): IRepository
	{
		if (isset($this->repositories[$name])) {
			return $this->repositories[$name];
		}
		throw new ModelException(sprintf('Repository %s not found', $name));
	}
}
