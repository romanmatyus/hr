<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use stdClass;


abstract class BasePresenter extends Nette\Application\UI\Presenter
{
	public function flashMessage($message, string $type = 'info'): stdClass
	{
		if ($this->isAjax()) {
			$this->redrawControl('flashes');
		}
		return parent::flashMessage($message, $type);
	}
}
