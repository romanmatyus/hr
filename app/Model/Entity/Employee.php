<?php

declare(strict_types=1);

namespace App\Model\Entity;

use Nette;
use ReflectionClass;


/**
 * @property-read string $uuid
 * @property string|null $name
 * @property int $age
 * @property string $gender
 * @property string|null $position
 * @property-read bool $stored
 */
class Employee implements IEntity
{
	private string $uuid;
	private ?string $name = null;
	private ?int $age = null;
	private ?string $gender = null;
	private ?string $position = null;
	private bool $stored = false;


	public function __construct(string $uuid = null)
	{
		if ($uuid !== null && !preg_match('#^[a-zA-Z0-9_]+$#D', $uuid)) {
			throw new Nette\ArgumentOutOfRangeException('UUID must be non-empty alphanumeric string');
		}
		$this->uuid = ($uuid)
			? $uuid
			: md5(uniqid(md5(self::class), true));
	}


	public function setName(string $name = null): self
	{
		$this->name = $name;
		return $this;
	}


	public function setAge(int $age = null): self
	{
		$this->age = $age;
		return $this;
	}


	public function setGender(string $gender = null): self
	{
		if (!in_array($gender, [Gender::Male, Gender::Female], true) && $gender !== null) {
			throw new Nette\ArgumentOutOfRangeException('Gender must be male or female');
		}
		$this->gender = $gender;
		return $this;
	}


	public function setPosition(string $position = null): self
	{
		$this->position = $position;
		return $this;
	}


	public function setStored(bool $stored): self
	{
		$this->stored = $stored;
		return $this;
	}


	public function __get($name): mixed
	{
		return $this->{$name};
	}


	public function __set(string $name, $value)
	{
		return call_user_func_array([$this, 'set' . ucfirst($name)], [$value]);
	}


	public function serialize(): array
	{
		$data = [];
		foreach ((new ReflectionClass($this))->getProperties() as $property) {
			if ((string) $property->getType() === 'bool') {
				$data[$property->getName()] = ($this->{$property->getName()})
					? 'true'
					: 'false';
			} else {
				$data[$property->getName()] = $this->{$property->getName()};
			}
		}
		return array_filter($data);
	}
}
