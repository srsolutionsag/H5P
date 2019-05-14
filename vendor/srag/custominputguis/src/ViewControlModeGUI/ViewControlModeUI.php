<?php

namespace srag\CustomInputGUIs\H5P\ViewControlModeUI;

use ilSession;
use srag\DIC\H5P\DICTrait;

/**
 * Class ViewControlModeUI
 *
 * @package srag\CustomInputGUIs\H5P\ViewControlModeUI
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ViewControlModeUI {

	use DICTrait;
	const CMD_HANDLE_BUTTONS = "ViewControlModeUIHandleButtons";
	/**
	 * @var array
	 */
	protected $buttons = [];
	/**
	 * @var string
	 */
	protected $default_active_id = "";
	/**
	 * @var string
	 */
	protected $id = "";
	/**
	 * @var string
	 */
	protected $link = "";


	/**
	 * ViewControlModeUI constructor
	 */
	public function __construct() {

	}


	/**
	 * @param array $buttons
	 *
	 * @return self
	 */
	public function withButtons(array $buttons) {
		$this->buttons = $buttons;

		return $this;
	}


	/**
	 * @param string $default_active_id
	 *
	 * @return self
	 */
	public function withDefaultActiveId($default_active_id) {
		$this->default_active_id = $default_active_id;

		return $this;
	}


	/**
	 * @param string $id
	 *
	 * @return self
	 */
	public function withId($id) {
		$this->id = $id;

		return $this;
	}


	/**
	 * @param string $link
	 *
	 * @return self
	 */
	public function withLink($link) {
		$this->link = $link;

		return $this;
	}


	/**
	 * @return string
	 */
	public function render() {
		ilSession::set(self::CMD_HANDLE_BUTTONS . "_" . $this->id . "_url", $_SERVER["REQUEST_URI"]);

		$actions = [];

		foreach ($this->buttons as $id => $txt) {
			$actions[$txt] = $this->link . "&" . self::CMD_HANDLE_BUTTONS . "=" . $id;
		}

		return self::output()->getHTML(self::dic()->ui()->factory()->viewControl()->mode($actions, "")
			->withActive($this->buttons[$this->getActiveId()]));
	}


	/**
	 *
	 */
	public function handleButtons()/*: void*/ {
		$active_id = filter_input(INPUT_GET, self::CMD_HANDLE_BUTTONS);

		ilSession::set(self::CMD_HANDLE_BUTTONS . "_" . $this->id, $active_id);

		self::dic()->ctrl()->redirectToURL(ilSession::get(self::CMD_HANDLE_BUTTONS . "_" . $this->id . "_url"));
	}


	/**
	 * @return string
	 */
	public function getActiveId() {
		$active_id = ilSession::get(self::CMD_HANDLE_BUTTONS . "_" . $this->id);

		if ($active_id === null || !isset($this->buttons[$active_id])) {
			return $active_id = $this->default_active_id;
		}

		return $active_id;
	}
}
