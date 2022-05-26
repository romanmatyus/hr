<?php

declare(strict_types=1);

namespace App\Model\Repository;

use App\Model\Entity\Employee;
use DOMDocument;
use ReflectionClass;

class EmployeeRepository implements IRepository
{
	/** @var string */
	protected $storagePath;

	/** @var array */
	private $data;


	public function __construct(string $storagePath)
	{
		$this->storagePath = $storagePath;
	}


	public function persist(Employee $e): Employee
	{
		if ($this->data === null) {
			$this->loadFromStorage();
		}
		$this->data[$e->uuid] = $e;
		return $e;
	}


	public function removeAndFlush(Employee $e): void
	{
		$this->remove($e);
		$this->flush();
	}


	public function remove(Employee $e): void
	{
		if ($this->data === null) {
			$this->loadFromStorage();
		}
		unset($this->data[$e->uuid]);
	}


	public function getByUuid(string $uuid): ?Employee
	{
		if ($this->data === null) {
			$this->loadFromStorage();
		}
		return $this->data[$uuid];
	}


	public function findAll(): iterable
	{
		if ($this->data === null) {
			$this->loadFromStorage();
		}
		return (array) $this->data;
	}


	public function loadFromStorage(): void
	{
		if (realpath($this->storagePath) === false || !file_exists($this->storagePath)) {
			return;
		}
		$dom = new DOMDocument;
		$dom->loadXML(file_get_contents($this->storagePath));

		$xml = simplexml_import_dom($dom);
		$data = [];
		foreach ($xml as $e) {
			$className = 'App\Model\Entity\\' . $xml->children()->getName();
			$entity = new $className((string) $e->uuid);
			foreach ($e as $key => $value) {
				if ($key !== 'uuid') {
					$reflection = new \ReflectionProperty($className, $key);
					$type = str_replace('?', '', (string) $reflection->getType());
					if ($type === 'int') {
						$value = (int) $value;
					} elseif ($type === 'string') {
						$value = (string) $value;
					} elseif ($type === 'bool') {
						$value = ((string) $value === 'true') ? true : false;
					} elseif (count(explode('::', (string) $value)) > 1) {
						$class = explode('::', (string) $value)[0];
						$type = ucfirst(explode('::', (string) $value)[1]);
					} else {
						$value = (string) $value;
					}
					$entity->$key = $value;
				}
			}
			$data[$entity->uuid] = $entity;
		}
		$this->data = $data;
	}


	public function flush()
	{
		$xmlString = "<?xml version=\"1.0\"?>\n<data>\n";
		foreach ($this->data as $e) {
			$xmlString .= "\t<" . (new ReflectionClass($e))->getShortName() . ">\n";
			foreach ($e->serialize() as $key => $value) {
				$valueString = $value;
				$xmlString .= "\t\t<" . $key . '>';
				$xmlString .= $valueString;
				$xmlString .= '</' . $key . ">\n";
			}
			$xmlString .= "\t</" . (new ReflectionClass($e))->getShortName() . ">\n";
		}
		$xmlString .= '</data>';
		file_put_contents($this->storagePath, $xmlString);
	}
}
