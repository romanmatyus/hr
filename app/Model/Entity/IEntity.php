<?php

declare(strict_types=1);

namespace App\Model\Entity;


interface IEntity
{
	public function serialize(): array;
}
