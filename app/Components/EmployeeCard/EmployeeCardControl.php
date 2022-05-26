<?php

declare(strict_types=1);

namespace App\Components\EmployeeCard;

use App\Model\Entity\Employee;
use App\Model\Entity\Gender;
use App\Model\Model;
use Nette;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use ReflectionClass;


class EmployeeCardControl extends Control
{
	public const TPL = __DIR__ . '/template.latte';

	/** @var callable[] */
	public $onRemove;

	/** @var Employee */
	private $employee;

	/** @var Model */
	private $model;

	/** @var bool */
	private $edited = false;


	public function __construct(
		Employee $employee,
		Model $model,
	) {
		$this->employee = $employee;
		$this->model = $model;
		if ($this->employee->stored === false) {
			$this->edited = true;
		}
	}


	/**
	 * Render
	 */
	public function render()
	{
		$this->template->employee = $this->employee;
		$this->template->edited = $this->edited;
		$this->template->setFile(self::TPL)->render();
	}


	protected function createComponent(string $name): ?Nette\ComponentModel\IComponent
	{
		switch ($name) {
			case 'form':
				$c = new Form;

				$c->addText('name', 'Meno');
				$c->addText('age', 'Vek')
					->addCondition(Form::FILLED)
						->addRule(Form::PATTERN, 'Zadajte, prosím, iba čísla', '.*[0-9].*')
						->addRule(Form::MIN, 'Zamestnenc musí mať aspoň 15 rokov', 15)
						->addRule(Form::MAX, 'Zamestnenc nesmie mať nad 100 rokov', 100);
				$c->addRadioList('gender', 'Pohlavie', [
					Gender::Male => 'Muž',
					Gender::Female => 'Žena',
				]);

				$c->setDefaults($this->employee->serialize());

				$c->addSubmit('submit', 'Uložiť');

				$c->onSuccess[] = [$this, 'formSucceeded'];

				return $c;
			default:
				return parent::createComponent($name);
		}
	}


	public function formSucceeded(Form $form, ArrayHash $data): void
	{
		$eRef = new ReflectionClass($this->employee);
		foreach ($data as $key => $value) {
			if ($value === null || $value === '') {
				$this->employee->{$key} = null;
			} else {
				settype($value, str_replace('?', '', (string) $eRef->getProperty($key)->getType()));
				$this->employee->{$key} = $value;
			}
		}
		$this->employee->setStored(true);
		$this->model->employee->persist($this->employee);
		$this->model->employee->flush();
		$this->edited = false;
		$this->redrawControl('content');
	}


	public function handleRemove()
	{
		$this->model->employee->removeAndFlush($this->employee);
		$this->onRemove($this->employee);
	}


	public function handleEdit()
	{
		$this->edited = true;
		$this->redrawControl('content');
	}


	public function handleBack()
	{
		if ($this->employee->stored === false) {
			$this->handleRemove();
		}
		$this->edited = false;
		$this->redrawControl('content');
	}
}

interface IEmployeeCardControlFactory
{
	public function create(Employee $employee): EmployeeCardControl;
}
