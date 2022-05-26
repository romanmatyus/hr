<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Components\EmployeeCard\IEmployeeCardControlFactory;
use App\Model\Entity\Employee;
use App\Model\Entity\Gender;
use App\Model\Model;
use DateTime;
use Nette\Application\UI\Multiplier;
use Nette\ComponentModel\IComponent;
use Nette\Utils\Json;


final class EmployeePresenter extends BasePresenter
{
	/** @var Model @inject */
	public $model;

	/** @var IEmployeeCardControlFactory @inject */
	public $employeeCardControlFactory;


	public function renderList(): void
	{
		$this->template->employees = $this->model->employee->findAll();
	}


	public function renderChart(): void
	{
		$this->template->employees = $this->model->employee->findAll();

		$a = [];
		foreach ($this->model->employee->findAll() as $e) {
			if ($e->stored) {
				$a[] = [
					'color' => ($e->gender === Gender::Male)
						? '#4B0082'
						: (
							($e->gender === Gender::Female)
								? '#ff5ff8'
								: '#a5a5a5'
						),
					'name' => $e->name,
					'age' => $e->age,
				];
			}
		}

		usort($a, fn($a, $b) => $a['age'] - $b['age']);

		$employeesList = [];
		foreach ($a as $e) {
			$employeesList[] = '"' . $e['name'] . '"';
		}
		$this->template->employeesList = '[' . implode(',', $employeesList) . ']';

		$employeesAge = [];
		foreach ($a as $e) {
			$employeesAge[] = (int) $e['age'];
		}
		$this->template->employeesAge = '[' . implode(',', $employeesAge) . ']';

		$employeesColor = [];
		foreach ($a as $e) {
			$employeesColor[] = '"' . $e['color'] . '"';
		}
		$this->template->employeesColor = '[' . implode(',', $employeesColor) . ']';
	}


	public function handleAdd()
	{
		$employee = $this->model->employee->persist(new Employee);
		$this->model->employee->flush();
		$this['employeeCard-' . $employee->uuid]->handleEdit();
		$this->payload->addUuid = $employee->uuid;
		$this->redrawControl('content');
	}


	public function actionRegenerate()
	{
		for ($i=0; $i<6; $i++) {
			$data = Json::decode(file_get_contents('https://api.namefake.com'));
			$e = (new \App\Model\Entity\Employee)
				->setName($data->name)
				->setAge((new DateTime)->diff(new \DateTime($data->birth_data))->y)
				->setGender(is_int(strpos($data->pict, 'female'))
					? \App\Model\Entity\Gender::Female
					: \App\Model\Entity\Gender::Male)
				->setStored(true);
			$this->model->employee->persist($e);
		}
		$this->model->employee->flush();
		$this->redirect('list');
	}


	protected function createComponent(string $name): IComponent
	{
		switch ($name) {
			case 'employeeCard':
				return new Multiplier(function (string $uuid) {
					foreach ($this->model->employee->findAll() as $e) {
						if ($e->uuid === $uuid) {
							$c = $this->employeeCardControlFactory->create($e);
							$c->onRemove[] = function (Employee $e) {
								if ($this->isAjax()) {
									if ($e->stored) {
										$this->flashMessage('Zamestnanec bol úspešne odstránený', 'success');
									}
									$this->payload->success = true;
									$this->payload->removedUuid = $e->uuid;
								}
							};
							return $c;
						}
					}
				});
		}
		return parent::createComponent($name);
	}
}
