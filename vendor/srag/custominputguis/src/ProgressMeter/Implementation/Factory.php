<?php

/* Copyright (c) 2017 Ralph Dittrich <dittrich@qualitus.de> Extended GPL, see docs/LICENSE */

namespace srag\CustomInputGUIs\H5P\ProgressMeter\Implementation;

use srag\CustomInputGUIs\H5P\ProgressMeter\Component\Factory as FactoryComponent;

/**
 * Class Factory
 *
 * https://github.com/ILIAS-eLearning/ILIAS/tree/trunk/src/UI/Implementation/Component/Chart/ProgressMeter/Factory.php
 *
 * @package srag\CustomInputGUIs\H5P\ProgressMeter\Implementation
 *
 * @author  Ralph Dittrich <dittrich@qualitus.de>
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @since ILIAS 5.4
 */
class Factory implements FactoryComponent {

	/**
	 * @inheritdoc
	 */
	public function standard($maximum, $main, $required = NULL, $comparison = NULL) {
		return new Standard($maximum, $main, $required, $comparison);
	}


	/**
	 * @inheritdoc
	 */
	public function fixedSize($maximum, $main, $required = NULL, $comparison = NULL) {
		return new FixedSize($maximum, $main, $required, $comparison);
	}


	/**
	 * @inheritdoc
	 */
	public function mini($maximum, $main, $required = NULL) {
		return new Mini($maximum, $main, $required);
	}
}
