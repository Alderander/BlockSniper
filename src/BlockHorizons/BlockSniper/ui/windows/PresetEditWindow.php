<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\ui\windows;

use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\presets\Preset;
use BlockHorizons\BlockSniper\presets\PresetPropertyProcessor;
use BlockHorizons\BlockSniper\ui\WindowHandler;
use pocketmine\network\mcpe\protocol\ModalFormResponsePacket;

class PresetEditWindow extends Window {

	/** @var null|Preset */
	private $preset = null;

	public function process(): void {
		if($this->preset === null) {
			return;
		}
		$shapes = $this->processShapes();
		$types = $this->processTypes();
		$d = $this->preset->getData();
		$shapeKey = array_search($d[2], $shapes);
		$typeKey = array_search($d[3], $types);

		$this->data = [
			"type" => "custom_form",
			"title" => (new Translation(Translation::UI_PRESET_EDIT_TITLE))->getMessage(),
			"content" => [
				[
					"type" => "input",
					"text" => (new Translation(Translation::UI_PRESET_EDIT_NAME))->getMessage(),
					"default" => $d[0],
					"placeholder" => (new Translation(Translation::UI_PRESET_EDIT_NAME))->getMessage()
				],
				[
					"type" => "slider",
					"text" => (new Translation(Translation::UI_PRESET_EDIT_SIZE))->getMessage(),
					"min" => 0,
					"max" => $this->getLoader()->getSettings()->getMaxRadius(),
					"step" => 1,
					"default" => $d[1]
				],
				[
					"type" => "dropdown",
					"text" => (new Translation(Translation::UI_PRESET_EDIT_SHAPE))->getMessage(),
					"default" => $shapeKey === false ? 0 : $shapeKey,
					"options" => $shapes
				],
				[
					"type" => "dropdown",
					"text" => (new Translation(Translation::UI_PRESET_EDIT_TYPE))->getMessage(),
					"default" => $typeKey === false ? 0 : $typeKey,
					"options" => $types
				],
				[
					"type" => "toggle",
					"text" => (new Translation(Translation::UI_PRESET_EDIT_HOLLOW))->getMessage(),
					"default" => $d[4]
				],
				[
					"type" => "toggle",
					"text" => (new Translation(Translation::UI_PRESET_EDIT_DECREMENT))->getMessage(),
					"default" => $d[5]
				],
				[
					"type" => "slider",
					"text" => (new Translation(Translation::UI_PRESET_EDIT_HEIGHT))->getMessage(),
					"min" => 0,
					"max" => $this->getLoader()->getSettings()->getMaxRadius(),
					"step" => 1,
					"default" => $d[6]
				],
				[
					"type" => "toggle",
					"text" => (new Translation(Translation::UI_PRESET_EDIT_PERFECT))->getMessage(),
					"default" => $d[7]
				],
				[
					"type" => "input",
					"text" => (new Translation(Translation::UI_PRESET_EDIT_BLOCKS))->getMessage(),
					"placeholder" => "stone,stone_brick:1,2",
					"default" => $d[8]
				],
				[
					"type" => "input",
					"text" => (new Translation(Translation::UI_PRESET_EDIT_OBSOLETE))->getMessage(),
					"placeholder" => "stone,stone_brick:1,2",
					"default" => $d[9]
				],
				[
					"type" => "input",
					"text" => (new Translation(Translation::UI_PRESET_EDIT_BIOME))->getMessage(),
					"placeholder" => "plains",
					"default" => $d[10]
				],
				[
					"type" => "input",
					"text" => (new Translation(Translation::UI_PRESET_EDIT_TREE))->getMessage(),
					"placeholder" => "oak",
					"default" => $d[11]
				]
			]
		];
	}

	/**
	 * @param Preset $preset
	 */
	public function setPreset(Preset $preset) {
		$this->preset = $preset;
	}

	/**
	 * @return Preset
	 */
	public function getPreset(): Preset {
		return $this->preset;
	}

	public function handle(ModalFormResponsePacket $packet): bool {
		$data = json_decode($packet->formData, true);
		$processor = new PresetPropertyProcessor($this->player, $this->loader);
		foreach($data as $key => $value) {
			$processor->process($key, $value);
		}
		$this->navigate(WindowHandler::WINDOW_PRESET_LIST_MENU, $this->player, new WindowHandler());
		return true;
	}
}