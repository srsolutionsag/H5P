<?php

declare(strict_types=1);

use ILIAS\UI\Implementation\Component\ComponentHelper;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
trait ilH5PTargetHelper
{
    use ComponentHelper;

    /**
     * @var ilCtrl
     */
    protected $ctrl;

    /**
     * @param string[]|string $target
     */
    protected function getLinkTarget($target, ?string $command, array $options = [], bool $async = false): string
    {
        $target = $this->toArray($target);
        $this->checkArgListElements('target', $target, 'string');

        $previous_state = $this->getAndEraseCurrentState($target);

        // apply $options to the final command class.
        $this->setTargetOptions($target[array_key_last($target)], $options);

        $link_target = $this->ctrl->getLinkTargetByClass(
            $target,
            $command ?? '',
            false,
            $async
        );

        $this->setNewState($previous_state);;

        return $link_target;
    }

    /**
     * @param string[]|string $target
     */
    protected function getFormAction($target, string $command = null, array $options = []): string
    {
        $target = $this->toArray($target);
        $this->checkArgListElements('target', $target, 'string');

        $previous_state = $this->getAndEraseCurrentState($target);

        // apply $options to the final command class.
        $this->setTargetOptions($target[array_key_last($target)], $options);

        $form_action = $this->ctrl->getFormActionByClass($target, $command ?? '');

        $this->setNewState($previous_state);

        return $form_action;
    }

    private function setTargetOptions(string $target_class, array $options): void
    {
        foreach ($options as $parameter_name => $parameter_value) {
            $this->ctrl->setParameterByClass($target_class, $parameter_name, $parameter_value);
        }
    }

    /**
     * @param string[] $target
     * @return array<string, array<string, string>>
     */
    private function getAndEraseCurrentState(array $target): array
    {
        $options = [];
        foreach ($target as $target_class) {
            // note for this action in ILIAS<=7 there needs to be a valid path for this operation.
            $options[$target_class] = $this->ctrl->getParameterArrayByClass($target_class);
            $this->ctrl->clearParametersByClass($target_class);
        }

        return $options;
    }

    /**
     * @param array<string, array<string, string>> $state
     */
    private function setNewState(array $state): void
    {
        foreach ($state as $target_class => $previous_options) {
            $this->setTargetOptions($target_class, $previous_options);
        }
    }
}
